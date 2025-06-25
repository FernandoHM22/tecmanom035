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


}
