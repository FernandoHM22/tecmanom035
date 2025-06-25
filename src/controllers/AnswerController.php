<?php
require_once BASE_PATH . '/src/models/Answer.php';

class AnswerController
{
      public static function submitAnswers($answers, $employeeNumber, $pdo)
    {
        $success = Answer::saveSurveyAnswers($answers, $employeeNumber, $pdo);

        return $success
            ? ['success' => true]
            : ['success' => false, 'message' => 'No se pudieron guardar las respuestas'];
    }
}
