<?php
class Risk
{
    /* ----- Usados por los AJAX ----- */

    public static function queueJob(PDO $pdo, string $employee, string $scopeType = 'category', string $answerSet = 'main', ?int $guideId = null): array
    {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO dbo.RiskComputeJobs (EmployeeNumber, ScopeType, AnswerSet, GuideID)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$employee, $scopeType, $answerSet, $guideId]);
            return ['dedup' => false, 'jobId' => $pdo->lastInsertId()];
        } catch (PDOException $e) {
            // 2601 / 2627: duplicate key
            if (strpos($e->getMessage(), 'IX_RiskComputeJobs_Dedup') !== false || in_array($e->errorInfo[1] ?? 0, [2601, 2627], true)) {
                return ['dedup' => true];
            }
            throw $e;
        }
    }

    public static function findResults(PDO $pdo, string $employee, string $scopeType = 'category', string $answerSet = 'main'): array
    {
        $stmt = $pdo->prepare("
          SELECT ScopeID, ScopeName, TotalScore, NivelRiesgo, CalculatedAt
          FROM dbo.EmployeeRiskResults
          WHERE EmployeeNumber=? AND ScopeType=? AND AnswerSet=?
          ORDER BY ScopeID
        ");
        $stmt->execute([$employee, $scopeType, $answerSet]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /* ----- Usados por el worker ----- */

    public static function pickNext(PDO $pdo, int $limit = 5): array
    {
        $limit = max(1, (int)$limit); // sanitiza

        // Armamos el SQL con TOP fijo (SQL Server permite TOP(n) con un entero literal)
        $sql = "
        SET NOCOUNT ON;

        ;WITH toclaim AS (
            SELECT TOP ($limit) j.JobID
            FROM dbo.RiskComputeJobs AS j WITH (READPAST, UPDLOCK, ROWLOCK)
            WHERE j.Status = 'queued'
            ORDER BY j.CreatedAt ASC
        )
        UPDATE j
            SET j.Status     = 'running',
                j.StartedAt  = SYSUTCDATETIME()
        OUTPUT inserted.JobID,
               inserted.EmployeeNumber,
               inserted.ScopeType,
               inserted.AnswerSet,
               inserted.GuideID
        FROM dbo.RiskComputeJobs AS j
        INNER JOIN toclaim t ON t.JobID = j.JobID;
    ";

        // No hace falta transacción explícita: el UPDATE es atómico
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function markDone(PDO $pdo, int $jobId): void
    {
        $pdo->prepare("UPDATE dbo.RiskComputeJobs SET Status='done', FinishedAt=SYSUTCDATETIME(), ErrorMessage=NULL WHERE JobID=?")
            ->execute([$jobId]);
    }

    public static function markFailed(PDO $pdo, int $jobId, string $err): void
    {
        $pdo->prepare("UPDATE dbo.RiskComputeJobs SET Status='failed', FinishedAt=SYSUTCDATETIME(), ErrorMessage=? WHERE JobID=?")
            ->execute([$err, $jobId]);
    }

    public static function recomputeNow(PDO $pdo, string $employee, string $scopeType = 'category', string $answerSet = 'main', ?int $guideId = null): void
    {
        $stmt = $pdo->prepare("EXEC dbo.sp_RecomputeRisk_FromThresholds @EmployeeNumber=?, @ScopeType=?, @AnswerSet=?, @GuideID=?");
        $stmt->execute([$employee, $scopeType, $answerSet, $guideId]);
    }
}
