<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

// لیست اکانت‌ها و محدودیت‌ها (مطابق کانفیگ شما)
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

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['error' => 'No input provided']);
    exit;
}

$action = $input['action'] ?? '';
$code = trim($input['code'] ?? '');
$date = $input['date'] ?? date('Y-m-d'); // تاریخ روز به فرمت YYYY-MM-DD

$tier = 'FREE';
if ($code !== '' && array_key_exists($code, $VALID_CODES)) {
    $tier = $VALID_CODES[$code];
}

// ساخت فایل فقط برای ذخیره توکن‌ها
$fileName = ($code === '' || $tier === 'FREE') ? 'free_' . md5($_SERVER['REMOTE_ADDR'] ?? 'ip') : md5($code);
$userFile = $dataDir . '/tokens_' . $fileName . '.json';

$fp = fopen($userFile, 'c+');
if (!$fp) {
    echo json_encode(['error' => 'Server filesystem error']);
    exit;
}

// قفل کردن فایل برای جلوگیری از دور زدن محدودیت توسط دو سیستم همزمان
flock($fp, LOCK_EX);

$raw = stream_get_contents($fp);
$db = empty($raw) ? [] : json_decode($raw, true);

if (!isset($db['usage'][$date])) {
    $db['usage'][$date] = 0;
}

$current_usage = $db['usage'][$date];

// فقط بررسی وضعیت توکن
if ($action === 'verify') {
    flock($fp, LOCK_UN);
    fclose($fp);
    echo json_encode([
        'valid' => ($tier !== 'FREE'),
        'tier'  => $tier,
        'limit' => $LIMITS[$tier],
        'usage' => $current_usage
    ]);
    exit;
}

// اضافه کردن توکن مصرفی جدید به کل توکن‌های امروز
if ($action === 'sync') {
    $add = (int)($input['usage_add'] ?? 0);
    if ($add > 0) {
        $current_usage += $add;
        $db['usage'][$date] = $current_usage;

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($db));
    }
    
    flock($fp, LOCK_UN);
    fclose($fp);
    
    echo json_encode([
        'status' => 'success',
        'usage'  => $current_usage
    ]);
    exit;
}

flock($fp, LOCK_UN);
fclose($fp);
echo json_encode(['error' => 'Invalid action']);
exit;
