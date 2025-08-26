<?php
require_once __DIR__.'/../config/config.php';
require_once BASE_PATH . '/src/models/Risk.php';

$pdo = getConnection(key: 'nom');

$batch = Risk::pickNext($pdo, 5);

foreach ($batch as $job) {
    $id        = (int)$job['JobID'];
    $employee  = $job['EmployeeNumber'];
    $scopeType = $job['ScopeType'];
    $answerSet = $job['AnswerSet'];
    $guideId   = $job['GuideID'] !== null ? (int)$job['GuideID'] : null;

    try {
        Risk::recomputeNow($pdo, $employee, $scopeType, $answerSet, $guideId);
        Risk::markDone($pdo, $id);
        echo "OK job #$id {$employee}/{$scopeType}\n";
    } catch (Throwable $t) {
        Risk::markFailed($pdo, $id, $t->getMessage());
        error_log("FAILED job #$id: ".$t->getMessage());
    }
}
