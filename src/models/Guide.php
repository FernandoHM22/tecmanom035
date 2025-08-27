<?php

class Guide
{
    public static function findGuide($guideValue, $pdo)
    {
        $stmt = $pdo->prepare("
        SELECT 
            q.QuestionID,
            q.QuestionText,
            dim.DimensionName,
            dom.DomainName,
            c.CategoryName,
            g.GuideID,
            g.GuideName,
            (
                SELECT 
                    a.OptionText AS [option],
                    a.value
                FROM AnswerScales a
                WHERE a.GuideID = g.GuideID
                AND a.QuestionID = q.QuestionID
                ORDER BY a.Id ASC
                FOR JSON PATH
            ) AS Scale
        FROM Questions q
        LEFT JOIN Dimensions dim ON q.DimensionID = dim.DimensionID
        LEFT JOIN Domains dom ON dim.DomainID = dom.DomainID
        LEFT JOIN Categories c ON dom.CategoryID = c.CategoryID
        LEFT JOIN Guides g ON c.GuideID = g.GuideID
        WHERE g.GuideID = :guideValue
        ORDER BY q.QuestionID ASC
    ");

        $stmt->bindParam(':guideValue', $guideValue, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ✅ Decodificar la columna Scale (JSON string) en array PHP
        foreach ($results as &$row) {
            if (!empty($row['Scale']) && is_string($row['Scale'])) {
                $row['Scale'] = json_decode($row['Scale'], true);
            }
        }

        return $results;
    }

    public static function getGuideInfo($projectId, $pdo)
    {
        $stmt = $pdo->prepare("
           SELECT GuideName,
            Description
        FROM Guides WHERE GuideID = :guide_type

        ");
        $stmt->bindParam(':guide_type', $projectId, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAllSupplementaryQuestions($projectId, $pdo)
    {
        $stmt = $pdo->prepare("
        SELECT 
        qs.QuestionID,
        qs.QuestionText,
        cs.name AS CategoryName,
        (
                SELECT 
                    cas.OptionText AS [option],
                    cas.[value],
                    cas.[Order]
                FROM ComplementaryAnswerScales cas
                WHERE cas.CategoryID = cs.CategoryID
                ORDER BY cas.[Order]
                FOR JSON PATH
            ) AS Scale
        FROM QuestionsServices qs
        INNER JOIN CategoryServices cs ON cs.CategoryID = qs.CategoryId
        INNER JOIN ProjectServices p ON cs.CategoryID = p.CategoryID
        WHERE p.ProjectID = :project_id
        ORDER BY qs.QuestionID");

        $stmt->bindParam(':project_id', $projectId, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ✅ Decodificar la columna Scale (JSON string) en array PHP
        foreach ($results as &$row) {
            if (!empty($row['Scale']) && is_string($row['Scale'])) {
                $row['Scale'] = json_decode($row['Scale'], true);
            }
        }
        return $results;
    }

    public static function getSurveyConfig($projectId, $pdo)
    {
        $stmt = $pdo->prepare("
            SELECT GuideID, year
            FROM SurveyConfigs
            WHERE ProjectID = :project_id
            AND active = 1
            ORDER BY CreateAt DESC
            OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY
        ");
        $stmt->bindParam(':project_id', $projectId, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getCategoryServicesList($pdo)
    {
        $stmt = $pdo->prepare("
            SELECT CategoryID, name AS CategoryName
            FROM CategoryServices
        ");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
