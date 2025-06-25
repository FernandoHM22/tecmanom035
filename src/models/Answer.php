<?php

class Answer
{
    public static function saveSurveyAnswers($answers, $employeeNumber, $pdo)
    {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO SurveyAnswers (
                    EmployeeNumber, QuestionID, AnswerValue, AnswerSet, CreatedAt
                ) VALUES (
                    :employee, :questionId, :answerValue, :set, GETDATE()
                )
            ");

            foreach ($answers as $row) {
                $stmt->execute([
                    ':employee' => $employeeNumber,
                    ':questionId' => $row['QuestionID'],
                    ':answerValue' => $row['Answer'],
                    ':set' => $row['set'] ?? 'main',
                ]);
            }

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error al guardar respuestas: " . $e->getMessage());
            return false;
        }
    }
}
