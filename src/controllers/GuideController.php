<?php
require_once BASE_PATH . '/src/models/Guide.php';

class GuideController
{
    public static function dataGuide($cb_codigo, $pdo)
    {
        $guideData = Guide::findGuide($cb_codigo, $pdo);

        if ($guideData) {
            return [
                'success' => true,
                'data' => $guideData
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se encontró información de la guía para el empleado'
            ];
        }
    }
    public static function guideInfo($guide_type, $pdo)
    {
        $guide_type = Guide::getGuideInfo($guide_type, $pdo);

        if ($guide_type) {
            return [
                'success' => true,
                'data' => $guide_type
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Información de guia no encontrada'
            ];
        }
    }

    public static function surveyConfig($projectId, $pdo)
    {
        $projectId = Guide::getSurveyConfig($projectId, $pdo);

        if ($projectId) {
            return [
                'success' => true,
                'data' => $projectId
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se encontró algún cuestionario programado para el proyecto especificado, por favor comuníquelo al aplicador'
            ];
        }
    }
    public static function supplementaryData($projectId, $pdo)
    {
        // Lógica actual sin filtros
        $questions = Guide::getAllSupplementaryQuestions($projectId, $pdo); // método en el modelo

        return $questions
            ? ['success' => true, 'data' => $questions]
            : ['success' => false, 'message' => 'No se encontraron preguntas'];
    }

    public static function getCategoryServices($pdo)
    {
        $services = Guide::getCategoryServicesList($pdo);

        if ($services) {
            return [
                'success' => true,
                'data' => $services
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se encontraron servicios para la categoría especificada.'
            ];
        }
    }
}
