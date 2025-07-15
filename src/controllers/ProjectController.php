<?php
require_once BASE_PATH . '/src/models/Project.php';

class ProjectController
{

    public static function projectList($region, $pdo)
    {
        $projects = Project::getProjectList($region, $pdo);

        if ($projects) {
            return [
                'success' => true,
                'data' => $projects
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se encontraron proyectos activos para la región especificada.'
            ];
        }
    }


    public static function projectAreas($projectId, $region, $pdo)
    {
        $projects = Project::getProjectAreas($projectId, $region, $pdo);

        if ($projects) {
            return [
                'success' => true,
                'data' => $projects
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se encontraron proyectos activos para la región especificada.'
            ];
        }
    }

    public static function projectShifts($projectId, $region, $pdo)
    {
        $projects = Project::getProjectShifts($projectId, $region, $pdo);

        if ($projects) {
            return [
                'success' => true,
                'data' => $projects
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se encontraron proyectos activos para la región especificada.'
            ];
        }
    }

    public static function projectSupervisors($projectId, $region, $pdo)
    {
        $projects = Project::getProjectSupervisors($projectId, $region, $pdo);

        if ($projects) {
            return [
                'success' => true,
                'data' => $projects
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se encontraron proyectos activos para la región especificada.'
            ];
        }
    }
}
