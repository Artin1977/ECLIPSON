<?php
// افزایش حافظه برای چت‌های طولانی
ini_set('memory_limit', '256M');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

// لیست اکانت‌ها (دقیقاً مطابق کانفیگ)
$VALID_CODES = [
    'plus123'    => 'PLUS',
    'plus999'    => 'PLUS',
    'pro456'     => 'PRO',
    'pro777'     => 'PRO',
    'artin_boss' => 'PRO_PLUS',
    'vip999'     => 'PRO_PLUS'
];

$LIMITS = [ 'FREE' => 500, 'PLUS' => 4000, 'PRO' => 13000, 'PRO_PLUS' => 30000 ];

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

// تعیین نام فایل دیتابیس
if ($code === '' || $tier === 'FREE') {
    $userIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown_ip';
    $fileName = 'free_' . md5($userIp);
} else {
    $fileName = md5($code);
}

$userFile = $dataDir . '/' . $fileName . '.json';

// سیستم امن خواندن و نوشتن فایل (تضمین جلوگیری از پاک شدن ناگهانی چت‌ها)
$fp = fopen($userFile, 'c+');
if (!$fp) {
    echo json_encode(['error' => 'Server filesystem error']);
    exit;
}

// قفل کردن فایل برای جلوگیری از تداخل
flock($fp, LOCK_EX);

// خواندن اطلاعات فعلی
$raw = stream_get_contents($fp);
$db = ['usage' => [], 'chats' => []];
if (!empty($raw)) {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $db = $decoded;
    }
}

// پردازش تاییدیه ورود و دریافت چت‌ها
if ($action === 'verify') {
    flock($fp, LOCK_UN);
    fclose($fp);
    echo json_encode([
        'valid' => ($tier !== 'FREE'),
        'tier'  => $tier,
        'limit' => $limit,
        'usage' => $db['usage'][$date] ?? 0,
        'chats' => $db['chats'] ?? []
    ]);
    exit;
}

// پردازش سینک کردن اطلاعات و توکن‌ها
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

    // تولید جیسون با پشتیبانی کامل از ایموجی و حروف فارسی
    $jsonOutput = json_encode($db, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    
    // فقط در صورتی روی فایل مینویسیم که مشکلی در کدهای بالا رخ نداده باشه
    if ($jsonOutput !== false) {
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, $jsonOutput);
    }
    
    flock($fp, LOCK_UN);
    fclose($fp);

    echo json_encode([
        'status' => 'success',
        'usage'  => $db['usage'][$date] ?? 0
    ]);
    exit;
}

flock($fp, LOCK_UN);
fclose($fp);
echo json_encode(['error' => 'Invalid action']);
exit;
