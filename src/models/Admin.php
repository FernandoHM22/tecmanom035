<?php

class Admin
{
    public static function saveSurveyConfig($surveyConfig, $pdo)
    {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
            INSERT INTO SurveyConfigs (
                ProjectID, year, GuideID, active, ApplicationDate
            ) VALUES (
                :projectId, YEAR(GETDATE()), :guideId, 1, :dateApplication
            )
        ");

            if (
                !isset($surveyConfig['projectId'], $surveyConfig['dateApplication'], $surveyConfig['guide']) ||
                empty($surveyConfig['projectId']) ||
                empty($surveyConfig['dateApplication']) ||
                empty($surveyConfig['guide'])
            ) {
                throw new Exception("Datos incompletos o vacíos: " . json_encode($surveyConfig));
            }

            $stmt->execute([
                ':projectId' => $surveyConfig['projectId'],
                ':dateApplication' => $surveyConfig['dateApplication'],
                ':guideId' => $surveyConfig['guide'],
            ]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error al guardar configuracion de encuesta: " . $e->getMessage());
            return false;
        }
    }

    public static function saveRHData($dataHR, $pdo)
    {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
            INSERT INTO HumanResourceData (
                ProjectID, Name
            ) VALUES (
                :projectId, :gte
            )
        ");

            if (
                !isset($dataHR['projectId'], $dataHR['gte']) ||
                empty($dataHR['projectId']) ||
                empty($dataHR['gte'])
            ) {
                throw new Exception("Datos incompletos o vacíos: " . json_encode($dataHR));
            }

            $stmt->execute([
                ':projectId' => $dataHR['projectId'],
                ':gte' => $dataHR['gte'],
            ]);

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error al guardar configuracion de encuesta: " . $e->getMessage());
            return false;
        }
    }

    public static function saveServices($selectedServices, $pdo)
    {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
            INSERT INTO ProjectServices (
                ProjectID, CategoryID, CreatedAt, CreatedBy
            ) VALUES (
                :projectId, :categoryId, GETDATE(), :createdBy
            )
        ");

            // Extraer valores generales
            $projectId = $selectedServices['projectId'] ?? null;
            $createdBy = $selectedServices['createdBy'] ?? null;
            $categoryIds = $selectedServices['CategoryID'] ?? [];

            // Validación general
            if (empty($projectId) || empty($createdBy) || !is_array($categoryIds)) {
                throw new Exception("Datos generales incompletos: " . json_encode($selectedServices));
            }

            // Inserta cada categoría
            foreach ($categoryIds as $categoryId) {
                if (empty($categoryId)) {
                    throw new Exception("ID de categoría vacío.");
                }

                $stmt->execute([
                    ':projectId' => $projectId,
                    ':categoryId' => $categoryId,
                    ':createdBy' => $createdBy,
                ]);
            }

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error al guardar configuración de servicios del proyecto: " . $e->getMessage());
            return false;
        }
    }

    public static function saveSampleData($sampleData, $pdo)
    {
        try {
            $pdo->beginTransaction();
            self::saveGeneralSample($sampleData['projectId'], $sampleData['sampleGeneral'], $pdo);
            $areasInserted = self::saveSampleAreas($sampleData['projectId'], $sampleData['sampleAreas'], $pdo);
            $shiftsInserted = self::saveSampleShifts($sampleData['projectId'], $sampleData['sampleShifts'], $pdo);
            $supInserted = self::saveSampleSupervisors($sampleData['projectId'], $sampleData['sampleSupervisors'], $pdo);

            $pdo->commit();

            return [
                'success' => true,
                'inserted' => [
                    'areas' => $areasInserted,
                    'shifts' => $shiftsInserted,
                    'supervisors' => $supInserted
                ]
            ];
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error en muestra general: " . $e->getMessage());
            error_log($e->getTraceAsString()); // útil para debugging
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public static function saveGeneralSample($projectId, $data,  $pdo)
    {
        $stmt = $pdo->prepare("
            INSERT INTO SurveySamples (ProjectID, SampleValue, GeneralSampleFactor, SampleMaleValue, SampleFemaleValue)
            VALUES (:projectId, :sampleValue, :generalFactor, :sampleMale, :sampleFemale)
        ");
        $stmt->execute([
            ':projectId' => $projectId,
            ':sampleValue' => $data['sampleValue'],
            ':generalFactor' => $data['generalSampleFactor'],
            ':sampleMale' => $data['sampleMaleValue'],
            ':sampleFemale' => $data['sampleFemaleValue'],
        ]);
    }

    public static function saveSampleAreas($projectId,  $areas,  $pdo)
    {
        $stmt = $pdo->prepare("
            INSERT INTO SampleByArea (ProjectID, AreaName, TotalEmployees, MaleEmployees, FemaleEmployees)
            VALUES (:projectId, :area, :employees, :male, :female)
        ");

        $count = 0;
        foreach ($areas as $area) {
            error_log("Insertando área: " . json_encode($area));
            $stmt->execute([
                ':projectId' => $projectId,
                ':area' => $area['Area'],
                ':employees' => $area['Colaboradores'],
                ':male' => $area['Hombres'],
                ':female' => $area['Mujeres'],
            ]);
            $count++;
        }

        return $count;
    }

    public static function saveSampleShifts($projectId,  $shifts,  $pdo)
    {
        $stmt = $pdo->prepare("
            INSERT INTO SampleByShift (ProjectID, ShiftName, TotalEmployees, MaleEmployees, FemaleEmployees)
            VALUES (:projectId, :shift, :employees, :male, :female)
        ");

        $count = 0;
        foreach ($shifts as $shift) {
            $stmt->execute([
                ':projectId' => $projectId,
                ':shift' => $shift['Turno'],
                ':employees' => $shift['Colaboradores'],
                ':male' => $shift['Hombres'],
                ':female' => $shift['Mujeres'],
            ]);
            $count++;
        }
        return $count;
    }

    public static function saveSampleSupervisors($projectId,  $supervisors,  $pdo)
    {
        $stmt = $pdo->prepare("
            INSERT INTO SampleBySupervisor (ProjectID, SupervisorName, TotalEmployees, MaleEmployees, FemaleEmployees)
            VALUES (:projectId, :supervisor, :employees, :male, :female)
        ");
        $count = 0;
        foreach ($supervisors as $sup) {
            $stmt->execute([
                ':projectId' => $projectId,
                ':supervisor' => $sup['Supervisor'],
                ':employees' => $sup['Colaboradores'],
                ':male' => $sup['Hombres'],
                ':female' => $sup['Mujeres'],
            ]);
            $count++;
        }
        return $count;
    }
}

class Status
{
    public static function getProjectStatus($region, $pdo)
    {
        try {
            $stmt = $pdo->prepare("
            SELECT p.ProjectID, 
                p.ProjectName, 
                p.Headcount, 
                p.FemaleCount, 
                p.MaleCount, 
                COALESCE(ss.SampleValue, 0) as SampleValue,
                ss.GeneralSampleFactor,
                COALESCE(CAST(ss.GeneralSampleFactor * 100 AS DECIMAL), 0) as SamplePercentage,
                COALESCE(ss.SampleFemaleValue, 0) as SampleFemaleValue,
                COALESCE(ss.SampleMaleValue, 0) as SampleMaleValue,
                sc.GuideID,
				sc.ApplicationDate
            FROM Projects p 
            LEFT JOIN SurveyConfigs sc ON sc.ProjectID = p.ProjectID
            LEFT JOIN SurveySamples ss ON ss.ProjectID = p.ProjectID
            WHERE p.Region = :region AND p.IsActive = 1
            ORDER BY p.ProjectName, sc.ConfigID ASC
        ");

            $stmt->bindParam(':region', $region, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error al obtener estatus: " . $e->getMessage());
            return false;
        }
    }
}


class Tracker
{
    public static function getProjectSurveyGeneralSampleData($projectId, $selectYears, $pdo)
    {
        try {
            $stmt = $pdo->prepare("
              SELECT * FROM SurveySamples WHERE ProjectID = :projectId AND YEAR(CreatedAt) IN (:years)
        ");

            $stmt->bindParam(':projectId', $projectId, PDO::PARAM_STR);
            $stmt->bindParam(':years', $selectYears, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error al obtener estatus: " . $e->getMessage());
            return false;
        }
    }

    public static function getProjectSurveyAreaSampleData($projectId, $selectYears, $pdo)
    {
        try {
            $stmt = $pdo->prepare("
              SELECT * FROM SampleByArea WHERE ProjectID = :projectId AND YEAR(SavedAt) IN (:years)
        ");

            $stmt->bindParam(':projectId', $projectId, PDO::PARAM_STR);
            $stmt->bindParam(':years', $selectYears, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error al obtener estatus: " . $e->getMessage());
            return false;
        }
    }
    public static function getProjectSurveySupervisorSampleData($projectId, $selectYears, $pdo)
    {
        try {
            $stmt = $pdo->prepare("
              SELECT * FROM SampleBySupervisor WHERE ProjectID = :projectId AND YEAR(SavedAt) IN (:years)
        ");

            $stmt->bindParam(':projectId', $projectId, PDO::PARAM_STR);
            $stmt->bindParam(':years', $selectYears, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error al obtener estatus: " . $e->getMessage());
            return false;
        }
    }

    public static function getProjectSurveyShiftSampleData($projectId, $selectYears, $pdo)
    {
        try {
            $stmt = $pdo->prepare("
              SELECT * FROM SampleByShift WHERE ProjectID = :projectId AND YEAR(SavedAt) IN (:years)
        ");

            $stmt->bindParam(':projectId', $projectId, PDO::PARAM_STR);
            $stmt->bindParam(':years', $selectYears, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error al obtener estatus: " . $e->getMessage());
            return false;
        }
    }


    public static function getProjectRealTimeGeneralData($project, $selectYears, $pdo)
    {
        try {
            $stmt = $pdo->prepare("
             SELECT 
                COUNT(EmployeeNumber) AS Completed,
                COUNT(CASE WHEN Gender = 'Hombre' THEN 1 END) AS MaleCount,
                COUNT(CASE WHEN Gender = 'Mujer' THEN 1 END) AS FemaleCount
                FROM EmployeeSurveyData
                WHERE Client = :project AND YEAR(SurveyDate) = :year
            ");

            $stmt->bindParam(':project', $project, PDO::PARAM_STR);
            $stmt->bindParam(':year', $selectYears, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error al obtener estatus: " . $e->getMessage());
            return false;
        }
    }

    public static function getProjectRealTimeAreaData($project, $selectYears, $pdo)
    {
        try {
            $stmt = $pdo->prepare("
             SELECT
                Area AS AreaName,
                COUNT(EmployeeNumber) AS SampleCompleted,
                COUNT(CASE WHEN Gender = 'Hombre' THEN 1 END) AS MaleCompleted,
                COUNT(CASE WHEN Gender = 'Mujer' THEN 1 END) AS FemaleCompleted
                FROM EmployeeSurveyData
                WHERE Client = :project AND YEAR(SurveyDate) = :year
                GROUP BY Area
            ");

            $stmt->bindParam(':project', $project, PDO::PARAM_STR);
            $stmt->bindParam(':year', $selectYears, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error al obtener estatus: " . $e->getMessage());
            return false;
        }
    }

    public static function getProjectRealTimeSupervisorData($project, $selectYears, $pdo)
    {
        try {
            $stmt = $pdo->prepare("
             SELECT
                Supervisor AS SupervisorName,
                COUNT(EmployeeNumber) AS SampleCompleted,
                COUNT(CASE WHEN Gender = 'Hombre' THEN 1 END) AS MaleCompleted,
                COUNT(CASE WHEN Gender = 'Mujer' THEN 1 END) AS FemaleCompleted
                FROM EmployeeSurveyData
                WHERE Client = :project AND YEAR(SurveyDate) = :year
                GROUP BY Supervisor
            ");

            $stmt->bindParam(':project', $project, PDO::PARAM_STR);
            $stmt->bindParam(':year', $selectYears, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error al obtener estatus: " . $e->getMessage());
            return false;
        }
    }

    public static function getProjectRealTimeShiftData($project, $selectYears, $pdo)
    {
        try {
            $stmt = $pdo->prepare("
             SELECT
                WorkShift as ShiftName,
                COUNT(EmployeeNumber) AS SampleCompleted,
                COUNT(CASE WHEN Gender = 'Hombre' THEN 1 END) AS MaleCompleted,
                COUNT(CASE WHEN Gender = 'Mujer' THEN 1 END) AS FemaleCompleted
                FROM EmployeeSurveyData
                WHERE Client = :project AND YEAR(SurveyDate) = :year
                GROUP BY WorkShift
            ");

            $stmt->bindParam(':project', $project, PDO::PARAM_STR);
            $stmt->bindParam(':year', $selectYears, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Error al obtener estatus: " . $e->getMessage());
            return false;
        }
    }
}
