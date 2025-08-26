<?php
require_once BASE_PATH . '/src/models/Risk.php';

class RiskController
{
    public static function enqueueJob(string $employee, string $scopeType, string $answerSet, ?int $guideId, PDO $pdo): array
    {
        try {
            $res = Risk::queueJob($pdo, $employee, $scopeType, $answerSet, $guideId);
            return [
                'success' => true,
                'queued'  => !$res['dedup'],
                'dedup'   => $res['dedup'],
                'jobId'   => $res['jobId'] ?? null,
                'message' => $res['dedup'] ? 'Trabajo ya en cola' : 'Trabajo encolado'
            ];
        } catch (Throwable $t) {
            return ['success'=>false, 'message'=>$t->getMessage()];
        }
    }

    public static function getResults(string $employee, string $scopeType, string $answerSet, PDO $pdo): array
    {
        try {
            $rows = Risk::findResults($pdo, $employee, $scopeType, $answerSet);
            return ['success'=>true, 'data'=>$rows];
        } catch (Throwable $t) {
            return ['success'=>false, 'message'=>$t->getMessage()];
        }
    }
}
