<?php
// رفع مشکل CORS برای ارتباط فرانت‌اند و بک‌اند
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

// لیست اکانت‌ها (باید دقیقاً با config.js برابر باشد)
$VALID_CODES = [
    'plus123'    => 'PLUS',
    'plus999'    => 'PLUS',
    'pro456'     => 'PRO',
    'pro777'     => 'PRO',
    'artin_boss' => 'PRO_PLUS',
    'vip999'     => 'PRO_PLUS'
];

$LIMITS = [
    'FREE'     => 500,
    'PLUS'     => 4000,
    'PRO'      => 13000,
    'PRO_PLUS' => 30000
];

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!$input) {
    echo json_encode(['error' => 'No input provided']);
    exit;
}

$action = $input['action'] ?? '';
$code = trim($input['code'] ?? '');
$date = $input['date'] ?? date('Y-m-d');

$tier = 'FREE';
if ($code !== '' && array_key_exists($code, $VALID_CODES)) {
    $tier = $VALID_CODES[$code];
}
$limit = $LIMITS[$tier];

if ($code === '' || $tier === 'FREE') {
    $userIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown_ip';
    $fileName = 'free_' . md5($userIp);
} else {
    $fileName = md5($code);
}

$userFile = $dataDir . '/' . $fileName . '.json';

$db = ['usage' => [], 'chats' => []];
if (file_exists($userFile)) {
    $fileData = json_decode(file_get_contents($userFile), true);
    if (is_array($fileData)) {
        $db = $fileData;
    }
}

if ($action === 'verify') {
    echo json_encode([
        'valid' => ($tier !== 'FREE'),
        'tier'  => $tier,
        'limit' => $limit,
        'usage' => $db['usage'][$date] ?? 0,
        'chats' => $db['chats'] ?? []
    ]);
    exit;
}

if ($action === 'sync') {
    if (isset($input['usage_add']) && is_numeric($input['usage_add']) && $input['usage_add'] > 0) {
        if (!isset($db['usage'][$date])) {
            $db['usage'][$date] = 0;
        }
        $db['usage'][$date] += (int)$input['usage_add'];
    }

    if ($tier !== 'FREE' && isset($input['chats']) && is_array($input['chats'])) {
        $db['chats'] = $input['chats'];
    }

    file_put_contents($userFile, json_encode($db), LOCK_EX);

    echo json_encode([
        'status' => 'success',
        'usage'  => $db['usage'][$date] ?? 0
    ]);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
exit;
