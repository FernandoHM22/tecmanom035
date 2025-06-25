<?php

class Project
{

  public static function getProjectList($region, $pdo)
  {
    $stmt = $pdo->prepare("
          SELECT ProjectID, ProjectName, Region, Headcount, FemaleCount, MaleCount FROM Projects WHERE Region = :region AND IsActive = 1 ORDER BY ProjectName ASC");
    $stmt->bindParam(':region', $region, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
