<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once dirname(__DIR__, 3) . '/config/config.php';

$data = json_decode(file_get_contents('php://input'), true);
$inputRaw = $data['projectName'] ?? '';
$firstWord = explode(' ', trim($inputRaw))[0]; // solo lo anterior al primer espacio
$inputNormalized = strtolower(preg_replace('/[^a-z0-9]/i', '', $firstWord));

$user = requireAuth();
$regionUser = $user['region'];

$baseDir = BASE_PATH . '/public/img/customerLogos/' . $regionUser . '/';
$baseUrl =  BASE_URL . '/public/img/customerLogos/' . $regionUser . '/';
$defaultLogo =  BASE_URL . '/public/img/logo-tecma.png';

$files = glob($baseDir . '*.png');
$found = null;
$highestSimilarity = 0;

foreach ($files as $filePath) {
    $fileName = pathinfo($filePath, PATHINFO_FILENAME);
    $normalizedFile = strtolower(preg_replace('/[^a-z0-9]/i', '', $fileName));

    if (
        strpos($normalizedFile, $inputNormalized) !== false ||
        strpos($inputNormalized, $normalizedFile) !== false
    ) {
        $found = $baseUrl . basename($filePath);
        break;
    }

    similar_text($normalizedFile, $inputNormalized, $percent);
    if ($percent > $highestSimilarity && $percent > 60) {
        $highestSimilarity = $percent;
        $found = $baseUrl . basename($filePath);
    }
}

header('Content-Type: application/json');
echo json_encode([
    'logo' => $found ?: $defaultLogo
]);
