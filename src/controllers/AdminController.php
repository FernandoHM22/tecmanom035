<?php
require_once BASE_PATH . '/src/models/Admin.php';

class AdminController
{
    public static function surveyConfig($surveyConfig, $pdo)
    {
        $success = Admin::saveSurveyConfig($surveyConfig, $pdo);

        return $success
            ? ['success' => true]
            : ['success' => false, 'message' => 'No se pudo guardar la configuración de la encuesta'];
    }

    public static function RHData($dataHR, $pdo)
    {
        $success = Admin::saveRHData($dataHR, $pdo);

        return $success
            ? ['success' => true]
            : ['success' => false, 'message' => 'No se pudo guardar la información de recursos humanos'];
    }

    public static function servicesProjectData($selectedServices, $pdo)
    {
        $success = Admin::saveServices($selectedServices, $pdo);

        return $success
            ? ['success' => true]
            : ['success' => false, 'message' => 'No se pudo guardar la información de servicios del proyecto'];
    }

    public static function sampleData($sampleData, $pdo)
    {
        $result = Admin::saveSampleData($sampleData, $pdo);

        if ($result['success'] === true) {
            return [
                'success' => true,
                'inserted' => $result['inserted'] ?? [],
            ];
        }

        return [
            'success' => false,
            'message' => $result['message'] ?? 'Error desconocido en el guardado de muestra'
        ];
    }

    public static function projectStatus($region, $pdo)
    {
        $response = Status::getProjectStatus($region, $pdo);

        if ($response) {
            return [
                'success' => true,
                'data' => $response
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Estatus del proyecto no encontrado para la región especificada'
            ];
        }
    }

    public static function projectSurveyGeneralSample($projectId, $selectYears, $pdo)
    {
        $response = Tracker::getProjectSurveyGeneralSampleData($projectId, $selectYears, $pdo);

        if ($response) {
            return [
                'success' => true,
                'data' => $response
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Muestra general no encontrada para el proyecto especificado'
            ];
        }
    }

    public static function projectSurveyAreaSample($projectId, $selectYears, $pdo)
    {
        $response = Tracker::getProjectSurveyAreaSampleData($projectId, $selectYears, $pdo);

        if ($response) {
            return [
                'success' => true,
                'data' => $response
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Muestra general no encontrada para el proyecto especificado'
            ];
        }
    }
    public static function projectSurveySupervisorSample($projectId, $selectYears, $pdo)
    {
        $response = Tracker::getProjectSurveySupervisorSampleData($projectId, $selectYears, $pdo);

        if ($response) {
            return [
                'success' => true,
                'data' => $response
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Muestra general no encontrada para el proyecto especificado'
            ];
        }
    }
    public static function projectSurveyShiftSample($projectId, $selectYears, $pdo)
    {
        $response = Tracker::getProjectSurveyShiftSampleData($projectId, $selectYears, $pdo);

        if ($response) {
            return [
                'success' => true,
                'data' => $response
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Muestra general no encontrada para el proyecto especificado'
            ];
        }
    }

    public static function projectRealTimeGeneralData($project, $selectYears, $pdo)
    {
        $response = Tracker::getProjectRealTimeGeneralData($project, $selectYears, $pdo);

        if ($response) {
            return [
                'success' => true,
                'data' => $response
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Muestra general no encontrada para el proyecto especificado'
            ];
        }
    }

    public static function projectRealTimeAreaData($projectId, $selectYears, $pdo)
    {
        $response = Tracker::getProjectRealTimeAreaData($projectId, $selectYears, $pdo);

        if ($response) {
            return [
                'success' => true,
                'data' => $response
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Muestra general no encontrada para el proyecto especificado'
            ];
        }
    }

    public static function projectRealTimeSupervisorData($projectId, $selectYears, $pdo)
    {
        $response = Tracker::getProjectRealTimeSupervisorData($projectId, $selectYears, $pdo);

        if ($response) {
            return [
                'success' => true,
                'data' => $response
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Muestra general no encontrada para el proyecto especificado'
            ];
        }
    }

    public static function projectRealTimeShiftData($projectId, $selectYears, $pdo)
    {
        $response = Tracker::getProjectRealTimeShiftData($projectId, $selectYears, $pdo);

        if ($response) {
            return [
                'success' => true,
                'data' => $response
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Muestra general no encontrada para el proyecto especificado'
            ];
        }
    }
}
