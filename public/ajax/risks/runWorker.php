<?php
try {
    require_once dirname(__DIR__, 3) . '/config/config.php';
    require_once BASE_PATH . '/src/models/Risk.php';
    header('Content-Type: application/json; charset=UTF-8');

    $pdo = getConnection(key: 'nom');
    $batch = Risk::pickNext($pdo, 6);
    $done = [];
    foreach ($batch as $job) {
        try {
            Risk::recomputeNow(
                $pdo,
                $job['EmployeeNumber'],
                $job['ScopeType'],
                $job['AnswerSet'],
                $job['GuideID'] !== null ? (int)$job['GuideID'] : null
            );
            Risk::markDone($pdo, (int)$job['JobID']);
            $done[] = $job['JobID'];
            echo json_encode(['ok' => true, 'processed' => $done]);
        } catch (Throwable $t) {
            Risk::markFailed($pdo, (int)$job['JobID'], $t->getMessage());
            error_log("FAILED job #{$job['JobID']}: " . $t->getMessage());
        }
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
