<?php

namespace App\Enums;

enum SettingKey: string
{
    // System
    case DefaultCountry = 'DEFAULT_COUNTRY';
    case DefaultLanguage = 'DEFAULT_LANGUAGE';
    case DateFormat = 'DATE_FORMAT';
    case TimeFormat = 'TIME_FORMAT';
    case CalendarStartDay = 'CALENDAR_START_DAY';
    case DefaultTimezone = 'DEFAULT_TIMEZONE';
    case DefaultTaxRate = 'DEFAULT_TAX_RATE';
    case EmailVerificationEnabled = 'EMAIL_VERIFICATION_ENABLED';
    case LandingPageEnabled = 'LANDING_PAGE_ENABLED';

    // ReCaptcha
    case RecaptchaEnabled = 'RECAPTCHA_ENABLED';
    case RecaptchaVersion = 'RECAPTCHA_VERSION';
    case RecaptchaSiteKey = 'RECAPTCHA_SITE_KEY';
    case RecaptchaSecretKey = 'RECAPTCHA_SECRET_KEY';

    // Brand
    case LogoDark = 'LOGO_DARK';
    case LogoLight = 'LOGO_LIGHT';
    case Favicon = 'FAVICON';
    case TitleTextEn = 'TITLE_TEXT_EN';
    case TitleTextAr = 'TITLE_TEXT_AR';
    case FooterTextEn = 'FOOTER_TEXT_EN';
    case FooterTextAr = 'FOOTER_TEXT_AR';
    case ThemeColor = 'THEME_COLOR';
    case CustomColor = 'CUSTOM_COLOR';
    case SidebarVariant = 'SIDEBAR_VARIANT';
    case SidebarStyle = 'SIDEBAR_STYLE';
    case LayoutDirection = 'LAYOUT_DIRECTION';
    case ThemeMode = 'THEME_MODE';

    // Storage
    case StorageType = 'STORAGE_TYPE';
    case StorageFileTypes = 'STORAGE_FILE_TYPES';
    case StorageMaxUploadSize = 'STORAGE_MAX_UPLOAD_SIZE';
    case AwsAccessKeyId = 'AWS_ACCESS_KEY_ID';
    case AwsSecretAccessKey = 'AWS_SECRET_ACCESS_KEY';
    case AwsDefaultRegion = 'AWS_DEFAULT_REGION';
    case AwsBucket = 'AWS_BUCKET';
    case AwsUrl = 'AWS_URL';
    case AwsEndpoint = 'AWS_ENDPOINT';
    case WasabiAccessKey = 'WASABI_ACCESS_KEY';
    case WasabiSecretKey = 'WASABI_SECRET_KEY';
    case WasabiRegion = 'WASABI_REGION';
    case WasabiBucket = 'WASABI_BUCKET';
    case WasabiUrl = 'WASABI_URL';
    case WasabiRoot = 'WASABI_ROOT';

    // Currency
    case DecimalFormat = 'DECIMAL_FORMAT';
    case DefaultCurrency = 'DEFAULT_CURRENCY';
    case DecimalSeparator = 'DECIMAL_SEPARATOR';
    case ThousandsSeparator = 'THOUSANDS_SEPARATOR';
    case FloatNumber = 'FLOAT_NUMBER';
    case CurrencySymbolSpace = 'CURRENCY_SYMBOL_SPACE';
    case CurrencySymbolPosition = 'CURRENCY_SYMBOL_POSITION';

    // Slack
    case SlackEnabled = 'SLACK_ENABLED';
    case SlackWebhookUrl = 'SLACK_WEBHOOK_URL';

    // Email
    case EmailProvider = 'EMAIL_PROVIDER';
    case EmailDriver = 'EMAIL_DRIVER';
    case EmailHost = 'EMAIL_HOST';
    case EmailPort = 'EMAIL_PORT';
    case EmailUsername = 'EMAIL_USERNAME';
    case EmailPassword = 'EMAIL_PASSWORD';
    case EmailEncryption = 'EMAIL_ENCRYPTION';
    case EmailFromAddress = 'EMAIL_FROM_ADDRESS';
    case EmailFromName = 'EMAIL_FROM_NAME';

    // Google Calendar
    case GoogleCalendarEnabled = 'GOOGLE_CALENDAR_ENABLED';
    case GoogleCalendarId = 'GOOGLE_CALENDAR_ID';
    case GoogleCalendarJsonPath = 'GOOGLE_CALENDAR_JSON_PATH';
    case GoogleCalendarClientId = 'GOOGLE_CALENDAR_CLIENT_ID';
    case GoogleCalendarClientSecret = 'GOOGLE_CALENDAR_CLIENT_SECRET';
    case GoogleCalendarRedirectUri = 'GOOGLE_CALENDAR_REDIRECT_URI';
    case IsGoogleCalendarSync = 'IS_GOOGLE_CALENDAR_SYNC';

    // Google Wallet
    case GoogleWalletIssuerId = 'GOOGLE_WALLET_ISSUER_ID';
    case GoogleWalletJsonPath = 'GOOGLE_WALLET_JSON_PATH';

    // ChatGPT
    case ChatgptKey = 'CHATGPT_KEY';
    case ChatgptModel = 'CHATGPT_MODEL';

    // SEO
    case MetaKeywords = 'META_KEYWORDS';
    case MetaDescription = 'META_DESCRIPTION';
    case MetaImage = 'META_IMAGE';

    // Cookie & Contact
    case EnableLogging = 'ENABLE_LOGGING';
    case StrictlyNecessaryCookies = 'STRICTLY_NECESSARY_COOKIES';
    case ContactUsUrl = 'CONTACT_US_URL';
    case CookieTitleEn = 'COOKIE_TITLE_EN';
    case CookieTitleAr = 'COOKIE_TITLE_AR';
    case StrictlyCookieTitleEn = 'STRICTLY_COOKIE_TITLE_EN';
    case StrictlyCookieTitleAr = 'STRICTLY_COOKIE_TITLE_AR';
    case CookieDescriptionEn = 'COOKIE_DESCRIPTION_EN';
    case CookieDescriptionAr = 'COOKIE_DESCRIPTION_AR';
    case StrictlyCookieDescriptionEn = 'STRICTLY_COOKIE_DESCRIPTION_EN';
    case StrictlyCookieDescriptionAr = 'STRICTLY_COOKIE_DESCRIPTION_AR';
    case ContactUsTitleEn = 'CONTACT_US_TITLE_EN';
    case ContactUsTitleAr = 'CONTACT_US_TITLE_AR';
    case ContactUsDescriptionEn = 'CONTACT_US_DESCRIPTION_EN';
    case ContactUsDescriptionAr = 'CONTACT_US_DESCRIPTION_AR';
    case ContactUsUrlEn = 'CONTACT_US_URL_EN';
    case ContactUsUrlAr = 'CONTACT_US_URL_AR';
    case CookieTitle = 'COOKIE_TITLE';
    case StrictlyCookieTitle = 'STRICTLY_COOKIE_TITLE';
    case CookieDescription = 'COOKIE_DESCRIPTION';
    case StrictlyCookieDescription = 'STRICTLY_COOKIE_DESCRIPTION';
    case ContactUsDescription = 'CONTACT_US_DESCRIPTION';

    // Twilio
    case TwilioSid = 'TWILIO_SID';
    case TwilioToken = 'TWILIO_TOKEN';
    case TwilioFrom = 'TWILIO_FROM';

    /**
     * Request key (camelCase) => SettingKey for system settings.
     *
     * @return array<string, self>
     */
    private static function systemRequestKeyMap(): array
    {
        return [
            'defaultCountry' => self::DefaultCountry,
            'defaultLanguage' => self::DefaultLanguage,
            'dateFormat' => self::DateFormat,
            'timeFormat' => self::TimeFormat,
            'calendarStartDay' => self::CalendarStartDay,
            'defaultTimezone' => self::DefaultTimezone,
            'defaultTaxRate' => self::DefaultTaxRate,
            'emailVerification' => self::EmailVerificationEnabled,
            'landingPageEnabled' => self::LandingPageEnabled,
        ];
    }

    /**
     * Full request key => SettingKey map for all update settings (brand, recaptcha, storage, cookie, seo, google calendar, google wallet).
     * Includes snake_case keys that do not normalize to enum values (e.g. is_googlecalendar_sync).
     *
     * @return array<string, self>
     */
    private static function requestKeyMap(): array
    {
        return [
            ...self::systemRequestKeyMap(),
            // Brand
            'logoDark' => self::LogoDark,
            'logoLight' => self::LogoLight,
            'favicon' => self::Favicon,
            'titleTextEn' => self::TitleTextEn,
            'titleTextAr' => self::TitleTextAr,
            'footerTextEn' => self::FooterTextEn,
            'footerTextAr' => self::FooterTextAr,
            'themeColor' => self::ThemeColor,
            'customColor' => self::CustomColor,
            'sidebarVariant' => self::SidebarVariant,
            'sidebarStyle' => self::SidebarStyle,
            'layoutDirection' => self::LayoutDirection,
            'themeMode' => self::ThemeMode,
            // ReCaptcha
            'recaptchaEnabled' => self::RecaptchaEnabled,
            'recaptchaVersion' => self::RecaptchaVersion,
            'recaptchaSiteKey' => self::RecaptchaSiteKey,
            'recaptchaSecretKey' => self::RecaptchaSecretKey,
            // ChatGPT
            'chatgptKey' => self::ChatgptKey,
            'chatgptModel' => self::ChatgptModel,
            // Storage (snake_case)
            'storage_type' => self::StorageType,
            'storage_file_types' => self::StorageFileTypes,
            'storage_max_upload_size' => self::StorageMaxUploadSize,
            'aws_access_key_id' => self::AwsAccessKeyId,
            'aws_secret_access_key' => self::AwsSecretAccessKey,
            'aws_default_region' => self::AwsDefaultRegion,
            'aws_bucket' => self::AwsBucket,
            'aws_url' => self::AwsUrl,
            'aws_endpoint' => self::AwsEndpoint,
            'wasabi_access_key' => self::WasabiAccessKey,
            'wasabi_secret_key' => self::WasabiSecretKey,
            'wasabi_region' => self::WasabiRegion,
            'wasabi_bucket' => self::WasabiBucket,
            'wasabi_url' => self::WasabiUrl,
            'wasabi_root' => self::WasabiRoot,
            // Cookie & Contact
            'enableLogging' => self::EnableLogging,
            'strictlyNecessaryCookies' => self::StrictlyNecessaryCookies,
            'cookieTitleEn' => self::CookieTitleEn,
            'cookieTitleAr' => self::CookieTitleAr,
            'strictlyCookieTitleEn' => self::StrictlyCookieTitleEn,
            'strictlyCookieTitleAr' => self::StrictlyCookieTitleAr,
            'cookieDescriptionEn' => self::CookieDescriptionEn,
            'cookieDescriptionAr' => self::CookieDescriptionAr,
            'strictlyCookieDescriptionEn' => self::StrictlyCookieDescriptionEn,
            'strictlyCookieDescriptionAr' => self::StrictlyCookieDescriptionAr,
            'contactUsDescriptionEn' => self::ContactUsDescriptionEn,
            'contactUsDescriptionAr' => self::ContactUsDescriptionAr,
            'contactUsUrlEn' => self::ContactUsUrlEn,
            'contactUsUrlAr' => self::ContactUsUrlAr,
            'cookieTitle' => self::CookieTitle,
            'strictlyCookieTitle' => self::StrictlyCookieTitle,
            'cookieDescription' => self::CookieDescription,
            'strictlyCookieDescription' => self::StrictlyCookieDescription,
            'contactUsDescription' => self::ContactUsDescription,
            'contactUsUrl' => self::ContactUsUrl,
            // SEO
            'metaKeywords' => self::MetaKeywords,
            'metaDescription' => self::MetaDescription,
            'metaImage' => self::MetaImage,
            // Google Calendar
            'googleCalendarEnabled' => self::GoogleCalendarEnabled,
            'googleCalendarId' => self::GoogleCalendarId,
            'googleCalendarJsonPath' => self::GoogleCalendarJsonPath,
            'is_googlecalendar_sync' => self::IsGoogleCalendarSync,
            // Google Wallet
            'googleWalletIssuerId' => self::GoogleWalletIssuerId,
            'googleWalletJsonPath' => self::GoogleWalletJsonPath,
        ];
    }

    /**
     * Resolve a request key (camelCase or snake_case) to the enum case.
     * Uses full request key map first, then tryFrom on normalized key (camelCase to UPPERCASE, or strtoupper for snake_case).
     */
    public static function match(string $requestKey): ?self
    {
        $map = self::requestKeyMap();
        if (isset($map[$requestKey])) {
            return $map[$requestKey];
        }
        $camelNormalized = strtoupper(preg_replace('/([A-Z])/', '_$1', $requestKey));
        return self::tryFrom($camelNormalized) ?? self::tryFrom(strtoupper($requestKey));
    }

    /**
     * System setting keys (for update system settings).
     *
     * @return array<self>
     */
    public static function systemKeys(): array
    {
        return array_values(self::systemRequestKeyMap());
    }

    /**
     * Request keys accepted for system settings (for validation / iteration).
     *
     * @return array<string>
     */
    public static function systemRequestKeys(): array
    {
        return array_keys(self::systemRequestKeyMap());
    }

    /**
     * All key values (storage strings).
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Whether the given string is a valid setting key value.
     */
    public static function isValid(string $key): bool
    {
        return in_array($key, self::values(), true);
    }

    /**
     * Try to get enum case for a string key (e.g. from request or DB).
     */
    public static function tryFromKey(string $key): ?self
    {
        return self::tryFrom($key);
    }
}

