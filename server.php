<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

// ----------------------------------------------------
// ۱. تنظیمات کدهای VIP و سطح دسترسی آن‌ها (بسیار امن)
// می‌توانی بی‌نهایت کد برای مشتریان یا خودت اضافه کنی
// ----------------------------------------------------
$VALID_CODES = [
    'plus123'    => 'PLUS',       // اکانت پلاس اول
    'plus999'    => 'PLUS',       // اکانت پلاس دوم
    'pro456'     => 'PRO',        // اکانت پرو اول
    'pro777'     => 'PRO',        // اکانت پرو دوم
    'artin_boss' => 'PRO_PLUS',   // اکانت پرو پلاس آرتین
    'vip999'     => 'PRO_PLUS'    // اکانت پرو پلاس دوم
];

// ۲. محدودیت‌های روزانه
$LIMITS = [
    'FREE'     => 500,
    'PLUS'     => 4000,
    'PRO'      => 13000,
    'PRO_PLUS' => 30000
];

// ایجاد پوشه دیتابیس به صورت خودکار
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['error' => 'No input']);
    exit;
}

$action = $input['action'] ?? '';
$code = trim($input['code'] ?? '');
$date = $input['date'] ?? date('Y-m-d'); // تاریخ دریافتی از مرورگر کاربر

// تشخیص سطح اشتراک کاربر
$tier = 'FREE';
if ($code !== '' && array_key_exists($code, $VALID_CODES)) {
    $tier = $VALID_CODES[$code];
}
$limit = $LIMITS[$tier];

// ساخت فایل دیتابیس اختصاصی برای این رمز عبور
$fileName = ($code === '' || $tier === 'FREE') ? 'free_users' : md5($code);
$userFile = $dataDir . '/' . $fileName . '.json';

if (!file_exists($userFile)) {
    file_put_contents($userFile, json_encode(['usage' => [], 'chats' => []]));
}
$db = json_decode(file_get_contents($userFile), true);

// درخواست دریافت اطلاعات (تایید رمز و دانلود چت‌ها)
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

// درخواست ذخیره اطلاعات (آپلود مصرف و پیام‌های جدید)
if ($action === 'sync') {
    // بروزرسانی توکن مصرفی
    if (isset($input['usage_add']) && $input['usage_add'] > 0) {
        if (!isset($db['usage'][$date])) {
            $db['usage'][$date] = 0;
        }
        $db['usage'][$date] += $input['usage_add'];
    }

    // همگام‌سازی چت‌ها فقط برای کاربران VIP انجام می‌شود تا دیتابیس سنگین نشود
    if ($tier !== 'FREE' && isset($input['chats'])) {
        $db['chats'] = $input['chats'];
    }

    file_put_contents($userFile, json_encode($db));

    echo json_encode([
        'status' => 'success',
        'usage'  => $db['usage'][$date] ?? 0
    ]);
    exit;
}