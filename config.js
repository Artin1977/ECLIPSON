// تنظیمات حساس و امنیتی - این فایل را در اختیار دیگران قرار ندهید!

// کلید و آدرس سرور GapGPT
const GAPGPT_API_URL = 'https://api.gapgpt.app/v1/chat/completions';
const GAPGPT_API_KEY = 'sk-c4Lsmm4OV6HU0YgwwF2kJrcc8fxAAG7NAW8hDe4FflPO7EE6';

// رمز عبور برای اشتراک‌های مختلف (می‌تونی این‌ها رو به دلخواه تغییر بدی)
const PASSWORDS = {
    PLUS: 'plus123',       // رمز اکانت پلاس
    PRO: 'pro456',         // رمز اکانت پرو
    PRO_PLUS: 'proplus789' // رمز اکانت پرو پلاس
};

// محدودیت‌های توکن روزانه برای هر سطح
const LIMITS = {
    FREE: 1500,       // کاربر عادی
    PLUS: 4000,       // کاربر پلاس
    PRO: 15000,        // کاربر پرو
    PRO_PLUS: 30000   // کاربر پرو پلاس
};
