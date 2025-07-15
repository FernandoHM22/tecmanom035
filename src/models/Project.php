<?php
class Project
{

  public static function getProjectList($region, $pdo)
  {
    $stmt = $pdo->prepare("
       SELECT 
        p.ProjectID, 
        p.ProjectName, 
        p.Region, 
        p.Headcount, 
        p.FemaleCount, 
        p.MaleCount, 
        sc.active as SurveyActive
    FROM Projects p
    OUTER APPLY (
        SELECT TOP 1 active 
        FROM SurveyConfigs sc 
        WHERE sc.ProjectID = p.ProjectID 
        ORDER BY sc.CreateAt DESC
    ) sc
    WHERE p.Region = :region
      AND p.IsActive = 1
    ORDER BY p.ProjectName ASC
    ");
    $stmt->bindParam(':region', $region, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getProjectAreas($projectId, $region, $pdo)
  {
    if ($region === 'CENTRAL') {
      $stmt = $pdo->prepare("
          SELECT 
            P.TB_ELEMENT AS Proyecto,
            A.TB_ELEMENT AS Area,
            COUNT (CB_CODIGO) as Colaboradores,
            COUNT(CASE WHEN C.CB_SEXO = 'F' THEN 1 END) AS Mujeres,
            COUNT(CASE WHEN C.CB_SEXO = 'M' THEN 1 END) AS Hombres
          FROM COLABORA AS C 
          LEFT JOIN NIVEL5 AS A ON C.CB_NIVEL5 = A.TB_CODIGO 
          LEFT JOIN V_NIVEL0 AS P ON C.CB_NIVEL0 = P.TB_CODIGO
          WHERE C.CB_NIVEL0 = :projectId
          AND C.CB_ACTIVO = 'S'
          GROUP BY P.TB_ELEMENT, A.TB_ELEMENT
          ORDER BY P.TB_ELEMENT, A.TB_ELEMENT
        ");
    } else if ($region === 'WEST') {
      $stmt = $pdo->prepare("
          SELECT 
            P.TB_ELEMENT AS Proyecto,
            A.TB_ELEMENT AS Area,
            COUNT (C.CB_CODIGO) as Colaboradores,
            COUNT(CASE WHEN C.CB_SEXO = 'F' THEN 1 END) AS Mujeres,
            COUNT(CASE WHEN C.CB_SEXO = 'M' THEN 1 END) AS Hombres
          FROM COLABORA AS C 
          LEFT JOIN NIVEL6 A ON C.CB_NIVEL6 = A.TB_CODIGO 
         LEFT JOIN NIVEL1 P ON C.CB_NIVEL1 = P.TB_CODIGO
          WHERE C.CB_NIVEL1 = :projectId
          AND C.CB_ACTIVO = 'S'
          GROUP BY P.TB_ELEMENT, A.TB_ELEMENT
          ORDER BY P.TB_ELEMENT, A.TB_ELEMENT
        ");
    }
    $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getProjectShifts($projectId, $region, $pdo)
  {
    if ($region === 'CENTRAL') {
      $stmt = $pdo->prepare("
         SELECT 
            P.TB_ELEMENT AS Proyecto,
            T.TU_DESCRIP as Turno,
            COUNT (C.CB_CODIGO) as Colaboradores,
            COUNT(CASE WHEN C.CB_SEXO = 'F' THEN 1 END) AS Mujeres,
            COUNT(CASE WHEN C.CB_SEXO = 'M' THEN 1 END) AS Hombres
          FROM COLABORA AS C 
          LEFT JOIN TURNO AS T ON C.CB_TURNO = T.TU_CODIGO 
          LEFT JOIN V_NIVEL0 AS P ON C.CB_NIVEL0 = P.TB_CODIGO
          WHERE C.CB_NIVEL0 = :projectId
          AND C.CB_ACTIVO = 'S'
          GROUP BY P.TB_ELEMENT, T.TU_DESCRIP
          ORDER BY P.TB_ELEMENT, T.TU_DESCRIP
        ");
    } else if ($region === 'WEST') {
      $stmt = $pdo->prepare("
      		SELECT 
            P.TB_ELEMENT AS Proyecto,
            T.TU_DESCRIP as Turno,
            COUNT (C.CB_CODIGO) as Colaboradores,
            COUNT(CASE WHEN C.CB_SEXO = 'F' THEN 1 END) AS Mujeres,
            COUNT(CASE WHEN C.CB_SEXO = 'M' THEN 1 END) AS Hombres
          FROM COLABORA AS C 
          LEFT JOIN TURNO AS T ON C.CB_TURNO = T.TU_CODIGO 
          LEFT JOIN NIVEL1 P ON C.CB_NIVEL1 = P.TB_CODIGO
          WHERE C.CB_NIVEL1 = :projectId
          AND C.CB_ACTIVO = 'S'
          GROUP BY P.TB_ELEMENT, T.TU_DESCRIP
          ORDER BY P.TB_ELEMENT, T.TU_DESCRIP
      ");
    }
    $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getProjectSupervisors($projectId, $region, $pdo)
  {
    if ($region === 'CENTRAL') {
      $stmt = $pdo->prepare("
          SELECT 
              P.TB_ELEMENT AS Proyecto,
              S.TB_ELEMENT AS Supervisor,
              COUNT (C.CB_CODIGO) AS Colaboradores,
            COUNT(CASE WHEN C.CB_SEXO = 'F' THEN 1 END) AS Mujeres,
            COUNT(CASE WHEN C.CB_SEXO = 'M' THEN 1 END) AS Hombres
          FROM COLABORA AS C 
              LEFT JOIN NIVEL3 S ON C.CB_NIVEL3 = S.TB_CODIGO
          LEFT JOIN V_NIVEL0 AS P ON C.CB_NIVEL0 = P.TB_CODIGO
          WHERE C.CB_NIVEL0 = :projectId
          AND C.CB_ACTIVO = 'S'
          GROUP BY P.TB_ELEMENT, S.TB_ELEMENT
          ORDER BY P.TB_ELEMENT, S.TB_ELEMENT
        ");
    } else if ($region === 'WEST') {
      $stmt = $pdo->prepare("
           SELECT 
              P.TB_ELEMENT AS Proyecto,
              S.TB_ELEMENT AS Supervisor,
              COUNT (C.CB_CODIGO) AS Colaboradores,
            COUNT(CASE WHEN C.CB_SEXO = 'F' THEN 1 END) AS Mujeres,
            COUNT(CASE WHEN C.CB_SEXO = 'M' THEN 1 END) AS Hombres
          FROM COLABORA AS C 
          LEFT JOIN NIVEL8 S ON C.CB_NIVEL8 = S.TB_CODIGO
          LEFT JOIN NIVEL1 P ON C.CB_NIVEL1 = P.TB_CODIGO
          WHERE C.CB_NIVEL1 = :projectId
          AND C.CB_ACTIVO = 'S'
          GROUP BY P.TB_ELEMENT, S.TB_ELEMENT
          ORDER BY P.TB_ELEMENT, S.TB_ELEMENT
        ");
    }
    $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
