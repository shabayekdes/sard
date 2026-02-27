<?php

return [
    // System Settings
    ['key' => 'DEFAULT_COUNTRY', 'value' => 'SA'],
    ['key' => 'DEFAULT_LANGUAGE', 'value' => 'ar'],
    ['key' => 'DATE_FORMAT', 'value' => 'Y-m-d'],
    ['key' => 'TIME_FORMAT', 'value' => 'H:i'],
    ['key' => 'CALENDAR_START_DAY', 'value' => 'sunday'],
    ['key' => 'DEFAULT_TIMEZONE', 'value' => 'Asia/Riyadh'],
    ['key' => 'ENABLE_EMAIL_VERIFICATION', 'value' => true],
    ['key' => 'LANDING_PAGE_ENABLED', 'value' => false],
    ['key' => 'DEFAULT_TAX_RATE', 'value' => '15'],
    ['key' => 'RECAPTCHA_ENABLED', 'value' => false],
    ['key' => 'RECAPTCHA_VERSION', 'value' => 'v3'],
    ['key' => 'RECAPTCHA_SITE_KEY', 'value' => ''],
    ['key' => 'RECAPTCHA_SECRET_KEY', 'value' => ''],

    // Brand Settings
    ['key' => 'LOGO_DARK', 'value' => '/images/logos/logo-dark.png'],
    ['key' => 'LOGO_LIGHT', 'value' => '/images/logos/logo-light.png'],
    ['key' => 'FAVICON', 'value' => '/images/logos/favicon.ico'],
    ['key' => 'TITLE_TEXT', 'value' => 'Sard app - تطبيق سرد'],
    ['key' => 'FOOTER_TEXT', 'value' => '© 2026 Sard . All rights reserved. - جميع الحقوق محفوظة لشركة سرد 2026'],
    ['key' => 'THEME_COLOR', 'value' => 'green'],
    ['key' => 'CUSTOM_COLOR', 'value' => '#205341'],
    ['key' => 'SIDEBAR_VARIANT', 'value' => 'inset'],
    ['key' => 'SIDEBAR_STYLE', 'value' => 'plain'],
    ['key' => 'LAYOUT_DIRECTION', 'value' => 'left'],
    ['key' => 'THEME_MODE', 'value' => 'light'],

    // Storage Settings
    ['key' => 'STORAGE_TYPE', 'value' => 'local'],
    ['key' => 'STORAGE_FILE_TYPES', 'value' => 'jpg,png,webp,gif,pdf,doc,docx,txt,csv'],
    ['key' => 'STORAGE_MAX_UPLOAD_SIZE', 'value' => '2048'],
    ['key' => 'AWS_ACCESS_KEY_ID', 'value' => ''],
    ['key' => 'AWS_SECRET_ACCESS_KEY', 'value' => ''],
    ['key' => 'AWS_DEFAULT_REGION', 'value' => 'us-east-1'],
    ['key' => 'AWS_BUCKET', 'value' => ''],
    ['key' => 'AWS_URL', 'value' => ''],
    ['key' => 'AWS_ENDPOINT', 'value' => ''],
    ['key' => 'WASABI_ACCESS_KEY', 'value' => ''],
    ['key' => 'WASABI_SECRET_KEY', 'value' => ''],
    ['key' => 'WASABI_REGION', 'value' => 'us-east-1'],
    ['key' => 'WASABI_BUCKET', 'value' => ''],
    ['key' => 'WASABI_URL', 'value' => ''],
    ['key' => 'WASABI_ROOT', 'value' => ''],

    // Currency Settings
    ['key' => 'DECIMAL_FORMAT', 'value' => '2'],
    ['key' => 'DEFAULT_CURRENCY', 'value' => 'SAR'],
    ['key' => 'DECIMAL_SEPARATOR', 'value' => '.'],
    ['key' => 'THOUSANDS_SEPARATOR', 'value' => ','],
    ['key' => 'FLOAT_NUMBER', 'value' => true],
    ['key' => 'CURRENCY_SYMBOL_SPACE', 'value' => false],
    ['key' => 'CURRENCY_SYMBOL_POSITION', 'value' => 'before'],

    // Slack Settings
    ['key' => 'SLACK_ENABLED', 'value' => false],
    ['key' => 'SLACK_WEBHOOK_URL', 'value' => ''],

    // Email Settings
    ['key' => 'EMAIL_PROVIDER', 'value' => 'smtp'],
    ['key' => 'EMAIL_DRIVER', 'value' => 'smtp'],
    ['key' => 'EMAIL_HOST', 'value' => 'smtp.emailit.com'],
    ['key' => 'EMAIL_PORT', 'value' => '587'],
    ['key' => 'EMAIL_USERNAME', 'value' => 'emailit'],
    ['key' => 'EMAIL_PASSWORD', 'value' => ''],
    ['key' => 'EMAIL_ENCRYPTION', 'value' => 'tls'],
    ['key' => 'EMAIL_FROM_ADDRESS', 'value' => 'no-reply@sard.app'],
    ['key' => 'EMAIL_FROM_NAME', 'value' => 'Sard'],

    // Cookie & Contact
    ['key' => 'ENABLE_LOGGING', 'value' => true],
    ['key' => 'STRICTLY_NECESSARY_COOKIES', 'value' => true],
    ['key' => 'CONTACT_US_URL', 'value' => 'https://sard.app'],
    ['key' => 'COOKIE_TITLE_EN', 'value' => 'Cookie Consent'],
    ['key' => 'COOKIE_TITLE_AR', 'value' => 'إشعار ملفات تعريف الارتباط'],
    ['key' => 'STRICTLY_COOKIE_TITLE_EN', 'value' => 'Strictly Necessary Cookies'],
    ['key' => 'STRICTLY_COOKIE_TITLE_AR', 'value' => 'ملفات تعريف الارتباط الضرورية'],
    ['key' => 'COOKIE_DESCRIPTION_EN', 'value' => 'We use cookies to improve your browsing experience, analyze website performance, and provide content tailored to your preferences.'],
    ['key' => 'COOKIE_DESCRIPTION_AR', 'value' => 'نستخدم ملفات تعريف الارتباط لتحسين تجربة التصفح، وتحليل أداء الموقع، وتقديم محتوى يتناسب مع تفضيلاتك.'],
    ['key' => 'STRICTLY_COOKIE_DESCRIPTION_EN', 'value' => 'These cookies are essential for the proper functioning of the website and cannot be disabled as they enable core features such as security and accessibility.'],
    ['key' => 'STRICTLY_COOKIE_DESCRIPTION_AR', 'value' => 'تُعد ملفات تعريف الارتباط هذه ضرورية لعمل الموقع بشكل صحيح، ولا يمكن تعطيلها، حيث تُمكّن الميزات الأساسية مثل الأمان وإمكانية الوصول.'],
    ['key' => 'CONTACT_US_TITLE_EN', 'value' => 'Contact Us'],
    ['key' => 'CONTACT_US_TITLE_AR', 'value' => 'إشعار ملفات تعريف الارتباط'],
    ['key' => 'CONTACT_US_DESCRIPTION_EN', 'value' => 'If you have any questions or concerns regarding our cookie policy, please feel free to contact us.'],
    ['key' => 'CONTACT_US_DESCRIPTION_AR', 'value' => 'إذا كان لديك أي استفسار أو ملاحظات بخصوص سياسة ملفات تعريف الارتباط، يُرجى التواصل معنا.'],
];
