<?php

class Employee
{
  public static function findByCodigo($cb_codigo, $region, $pdo)
  {
    if ($region === 'CENTRAL') {
      $stmt = $pdo->prepare("
            SELECT
                CASE WHEN C.CB_SEXO = 'F' THEN 'Mujer' ELSE 'Hombre' END AS genero,
                CAST(
                    (DATEDIFF(YEAR, C.CB_FEC_NAC, GETDATE())
                      - CASE 
                          WHEN MONTH(C.CB_FEC_NAC) > MONTH(GETDATE()) 
                            OR (MONTH(C.CB_FEC_NAC) = MONTH(GETDATE()) AND DAY(C.CB_FEC_NAC) > DAY(GETDATE()))
                        THEN 1 ELSE 0 
                      END) AS VARCHAR(10)
                ) + ' años' AS edad,
                EC.TB_ELEMENT AS estado_civil,
                EST.TB_ELEMENT AS nivel_estudios,
                T.TU_DESCRIP AS turno,
                CASE 
                  WHEN DATEDIFF(DAY, C.CB_FEC_ANT, GETDATE()) < 30 THEN 
                    CAST(DATEDIFF(DAY, C.CB_FEC_ANT, GETDATE()) AS VARCHAR(10)) + ' días'
                  WHEN DATEDIFF(MONTH, C.CB_FEC_ANT, GETDATE()) < 12 THEN 
                    CAST(DATEDIFF(MONTH, C.CB_FEC_ANT, GETDATE()) AS VARCHAR(10)) + ' meses'
                  ELSE 
                    CAST(DATEDIFF(MONTH, C.CB_FEC_ANT, GETDATE()) / 12 AS VARCHAR(10)) + ' años'
                END AS antiguedad,
                A.TB_ELEMENT AS area,
                S.TB_ELEMENT AS supervisor,
                C.PRETTYNAME AS nombre,
                C.CB_CODIGO,
                C.CB_NIVEL0 AS projectId,
                P.TB_ELEMENT AS cliente,
                E.TB_ELEMENT AS planta,
                C.CB_ACTIVO AS estatus,
                'CENTRAL' AS region
            FROM COLABORA C
            LEFT JOIN V_NIVEL0 P ON C.CB_NIVEL0 = P.TB_CODIGO
            LEFT JOIN NIVEL3 S ON C.CB_NIVEL3 = S.TB_CODIGO
            LEFT JOIN NIVEL5 A ON C.CB_NIVEL5 = A.TB_CODIGO
            LEFT JOIN NIVEL7 E ON C.CB_NIVEL7 = E.TB_CODIGO
            LEFT JOIN TURNO T ON C.CB_TURNO = T.TU_CODIGO
            LEFT JOIN EDOCIVIL EC ON C.CB_EDO_CIV = EC.TB_CODIGO
            LEFT JOIN ESTUDIOS EST ON C.CB_ESTUDIO = EST.TB_CODIGO
            WHERE C.CB_CODIGO = :cb_codigo
        ");
    } else if ($region === 'WEST') {
      $stmt = $pdo->prepare("
            SELECT
                CASE WHEN C.CB_SEXO = 'F' THEN 'Mujer' ELSE 'Hombre' END AS genero,
                CAST(
                    (DATEDIFF(YEAR, C.CB_FEC_NAC, GETDATE())
                      - CASE 
                          WHEN MONTH(C.CB_FEC_NAC) > MONTH(GETDATE()) 
                            OR (MONTH(C.CB_FEC_NAC) = MONTH(GETDATE()) AND DAY(C.CB_FEC_NAC) > DAY(GETDATE()))
                        THEN 1 ELSE 0 
                      END) AS VARCHAR(10)
                ) + ' años' AS edad,
                EC.TB_ELEMENT AS estado_civil,
                EST.TB_ELEMENT AS nivel_estudios,
                T.TU_DESCRIP AS turno,
                CASE 
                  WHEN DATEDIFF(DAY, C.CB_FEC_ANT, GETDATE()) < 30 THEN 
                    CAST(DATEDIFF(DAY, C.CB_FEC_ANT, GETDATE()) AS VARCHAR(10)) + ' días'
                  WHEN DATEDIFF(MONTH, C.CB_FEC_ANT, GETDATE()) < 12 THEN 
                    CAST(DATEDIFF(MONTH, C.CB_FEC_ANT, GETDATE()) AS VARCHAR(10)) + ' meses'
                  ELSE 
                    CAST(DATEDIFF(MONTH, C.CB_FEC_ANT, GETDATE()) / 12 AS VARCHAR(10)) + ' años'
                END AS antiguedad,
                A.TB_ELEMENT AS area,
                S.TB_ELEMENT AS supervisor,
                C.PRETTYNAME AS nombre,
                C.CB_CODIGO,
				C.CB_NIVEL1 AS projectId,
                P.TB_ELEMENT AS cliente,
                E.TB_ELEMENT AS planta,
                C.CB_ACTIVO AS estatus,
				'WEST' AS region
            FROM COLABORA C
            LEFT JOIN NIVEL1 P ON C.CB_NIVEL1 = P.TB_CODIGO
            LEFT JOIN NIVEL8 S ON C.CB_NIVEL8 = S.TB_CODIGO
            LEFT JOIN NIVEL6 A ON C.CB_NIVEL6 = A.TB_CODIGO
            LEFT JOIN NIVEL7 E ON C.CB_NIVEL7 = E.TB_CODIGO
            LEFT JOIN TURNO T ON C.CB_TURNO = T.TU_CODIGO
            LEFT JOIN EDOCIVIL EC ON C.CB_EDO_CIV = EC.TB_CODIGO
            LEFT JOIN ESTUDIOS EST ON C.CB_ESTUDIO = EST.TB_CODIGO
			WHERE C.CB_CODIGO = :cb_codigo
      ");
    }


    $stmt->bindParam(':cb_codigo', $cb_codigo, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function saveDemographics($data, $pdo)
  {
    $employeeNumber = $data['CB_CODIGO'];
    $region = $data['region'];

    $checkSql = "SELECT COUNT(*) FROM EmployeeSurveyData WHERE EmployeeNumber = :employeeNumber AND Region = :region";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([
      ':employeeNumber' => $employeeNumber,
      ':region' => $region
    ]);

    $exists = $checkStmt->fetchColumn() > 0;

    if ($exists) {
      $sql = "
            UPDATE EmployeeSurveyData SET
                FullName = :nombre,
                Gender = :genero,
                Age = :edad,
                AgeRange = :rango_edad,
                MaritalStatus = :estado_civil,
                EducationLevel = :nivel_estudios,
                WorkShift = :turno,
                SeniorityYears = :antiguedad,
                SeniorityRange = :rango_antiguedad,
                Area = :area,
                Supervisor = :supervisor,
                Client = :cliente,
                Plant = :planta,
                Status = :estatus,
                ModifiedAt = GETDATE()
            WHERE EmployeeNumber = :employeeNumber AND Region = :region
        ";
    } else {
      $sql = "
            INSERT INTO EmployeeSurveyData (
                EmployeeNumber, FullName, Gender, Age, AgeRange, MaritalStatus,
                EducationLevel, WorkShift, SeniorityYears, SeniorityRange,
                Area, Supervisor, Client, Plant, Status, SurveyDate, CreatedAt, Region
            )
            VALUES (
                :employeeNumber, :nombre, :genero, :edad, :rango_edad, :estado_civil,
                :nivel_estudios, :turno, :antiguedad, :rango_antiguedad,
                :area, :supervisor, :cliente, :planta, :estatus, GETDATE(), GETDATE(), :region
            )
        ";
    }

    $stmt = $pdo->prepare($sql);

    $params = [
      ':employeeNumber'     => $employeeNumber,
      ':nombre'             => $data['nombre'],
      ':genero'             => $data['genero'],
      ':edad'               => intval(value: preg_replace('/\\D/', '', $data['edad'] ?? '0')),
      ':rango_edad'         => $data['rango_edad'],
      ':estado_civil'       => $data['estado_civil'],
      ':nivel_estudios'     => $data['nivel_estudios'],
      ':turno'              => $data['turno'],
      ':antiguedad'         => intval(preg_replace('/\\D/', '', $data['antiguedad'] ?? '0')),
      ':rango_antiguedad'   => $data['rango_antiguedad'],
      ':area'               => $data['area'],
      ':supervisor'         => $data['supervisor'],
      ':cliente'            => $data['cliente'],
      ':planta'             => $data['planta'],
      ':estatus'            => $data['estatus'],
      ':region'            => $data['region']
    ];

    if (!$stmt->execute($params)) {
      throw new Exception("No se pudo guardar la información del empleado.");
    }

    return true;
  }

  public static function getData($email, $pdo)
  {
    $stmt = $pdo->prepare("
     SELECT C.PRETTYNAME, C.CB_PUESTO, P.PU_DESCRIP FROM COLABORA C LEFT JOIN PUESTO P ON C.CB_PUESTO = P.PU_CODIGO WHERE C.CB_G_TEX23 LIKE '%:email'
    ");
    $stmt->execute(['user_id' => $email]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // o fetch(PDO::FETCH_ASSOC) si es solo una fila
  }
}
