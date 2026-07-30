// تنظیمات حساس و امنیتی - این فایل را در اختیار دیگران قرار ندهید!

window.GAPGPT_API_URL = 'https://api.gapgpt.app/v1/chat/completions';
window.GAPGPT_API_KEY = 'sk-c4Lsmm4OV6HU0YgwwF2kJrcc8fxAAG7NAW8hDe4FflPO7EE6';

// آدرس فایل سرور (اگر سایت روی هاست واقعی است، آدرس کامل بدهید مثل https://yoursite.com/server.php)
window.CLOUD_SERVER_URL = 'server.php';

// لیست اکانت‌ها و سطح دسترسی آن‌ها
window.PASSWORDS = {
    'plus123': 'PLUS',       
    'plus999': 'PLUS',       
    'pro456': 'PRO',         
    'pro777': 'PRO',         
    'artin_boss': 'PRO_PLUS',
    'vip999': 'PRO_PLUS'     
};

// محدودیت‌های توکن روزانه برای هر سطح
window.LIMITS = {
    'FREE': 500,       
    'PLUS': 4000,      
    'PRO': 13000,      
    'PRO_PLUS': 30000  
};

// تنظیمات نوتیفیکیشن (اعلان بالای سایت)
window.APP_NOTIFICATION = {
    enabled: true, 
    message: '☁️ آپدیت مهم: مشکل تداخل چت‌ها برطرف شد. هر اکانت فضای ابری کاملاً مجزای خود را دارد!',
    color: 'bg-indigo-600' 
};
