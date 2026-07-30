// تنظیمات حساس و امنیتی - این فایل را در اختیار دیگران قرار ندهید!

// کلید و آدرس سرور GapGPT
const GAPGPT_API_URL = 'https://api.gapgpt.app/v1/chat/completions';
const GAPGPT_API_KEY = 'sk-c4Lsmm4OV6HU0YgwwF2kJrcc8fxAAG7NAW8hDe4FflPO7EE6';

// لیست اکانت‌ها و سطح دسترسی آن‌ها
const PASSWORDS = {
    'plus123': 'PLUS',       // اکانت پلاس اول
    'plus999': 'PLUS',       // اکانت پلاس دوم
    'pro456': 'PRO',         // اکانت پرو اول
    'pro777': 'PRO',         // اکانت پرو دوم
    'artin_boss': 'PRO_PLUS',// اکانت پرو پلاس آرتین
    'vip999': 'PRO_PLUS'     // اکانت پرو پلاس دوم
};

// محدودیت‌های توکن روزانه برای هر سطح
const LIMITS = {
    'FREE': 500,       // کاربر عادی
    'PLUS': 4000,      // کاربر پلاس
    'PRO': 13000,      // کاربر پرو
    'PRO_PLUS': 30000  // کاربر پرو پلاس
};

// تنظیمات نوتیفیکیشن (اعلان بالای سایت)
const APP_NOTIFICATION = {
    enabled: true, // برای مخفی کردن، این را false کنید
    message: '☁️ سیستم همگام‌سازی ابری چت‌ها و اکانت‌های ویژه فعال شد!',
    color: 'bg-red-600' // رنگ‌های مجاز: bg-red-600, bg-blue-600, bg-green-600, bg-orange-500
};
