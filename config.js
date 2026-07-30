// تنظیمات حساس و امنیتی - این فایل را در اختیار دیگران قرار ندهید!

window.GAPGPT_API_URL = 'https://api.gapgpt.app/v1/chat/completions';
window.GAPGPT_API_KEY = 'sk-c4Lsmm4OV6HU0YgwwF2kJrcc8fxAAG7NAW8hDe4FflPO7EE6';

// آدرس فایل سرور (اگر سایت روی هاست واقعی است، آدرس کامل بدهید مثل https://yoursite.com/server.php)
window.CLOUD_SERVER_URL = 'server.php';

// لیست اکانت‌ها و سطح دسترسی آن‌ها
window.PASSWORDS = {
    'plus123': 'PLUS',       
    'plus999': 'PLUS',       
    'pro456': 'PRO',  // آقای جعفر خوانی       
    'pro777': 'PRO',  // مامان غزال       
    'artin_boss': 'PRO_PLUS', // آرتین
    'vip999': 'PRO_PLUS'  // بابا محمد
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
    message: 'ممکن است بعضی وقت ها به دلیل ناپایداری اینترنت برای هوش مصنوعی قعطی بوجود بیاید',
    color: 'bg-indigo-600' 
};
