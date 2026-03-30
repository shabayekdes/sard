import { toHijri } from 'hijri-converter';

/** Display format for dates in tables: day, short month, year (e.g. 01 Jan, 2025) */
const TABLE_DATE_FORMAT = 'd M, Y';

/** Hijri month short names: English and Arabic (1–12) */
const HIJRI_MONTHS_SHORT_EN = ['Muh.', 'Saf.', 'Rab. I', 'Rab. II', 'Jum. I', 'Jum. II', 'Raj.', 'Sha.', 'Ram.', 'Shaw.', 'Dhu Q.', 'Dhu H.'];
const HIJRI_MONTHS_SHORT_AR = ['محرم', 'صفر', 'ربيع الأول', 'ربيع الآخر', 'جمادى الأولى', 'جمادى الآخرة', 'رجب', 'شعبان', 'رمضان', 'شوال', 'ذو القعدة', 'ذو الحجة'];

// Extend window interface
declare global {
    interface Window {
        appSettings: {
            get: (key: string, defaultValue?: any) => any;
            baseUrl: string;
            imageUrl: string;
            dateFormat: string;
            dateCalendarType: 'gregorian' | 'hijri';
            timeFormat: string;
            timezone: string;
            language: string;
            emailVerification: boolean;
            formatDateTime: (date: string | Date, includeTime?: boolean) => string | null;
            formatDate: (date: string | Date) => string | null;
            formatTime: (time: string | Date) => string | null;
            formatCurrency: (amount: number | string, options?: { showSymbol?: boolean; showCode?: boolean }) => string;
            formatCurrencyWithSuperAdminSettings: (amount: number | string, options?: { showSymbol?: boolean; showCode?: boolean }) => string;
            currencySettings: {
                decimalFormat: string;
                defaultCurrency: string;
                decimalSeparator: string;
                thousandsSeparator: string;
                floatNumber: boolean;
                currencySymbolSpace: boolean;
                currencySymbolPosition: string;
                currencySymbol: string;
                currencyCode: string;
                currencyName: string;
            };
        };
    }
}

// Initialize global settings
export function initializeGlobalSettings(settings: Record<string, any>) {
    // Set up currency settings
    const currencySettings = {
        decimalFormat: settings.DECIMAL_FORMAT || '2',
        defaultCurrency: settings.DEFAULT_CURRENCY || 'USD',
        decimalSeparator: settings.DECIMAL_SEPARATOR || '.',
        thousandsSeparator: settings.THOUSANDS_SEPARATOR || ',',
        floatNumber: settings.FLOAT_NUMBER !== '0',
        currencySymbolSpace: settings.CURRENCY_SYMBOL_SPACE === '1',
        currencySymbolPosition: settings.CURRENCY_SYMBOL_POSITION || 'before',
        currencySymbol: settings.currencySymbol || '$',
        currencyCode: settings.currencyCode || 'USD',
        currencyName: settings.currencyName || 'US Dollar',
    };

    function translatedFormat(dateObj: Date, format: string, locale: string, s: Record<string, any>): string {
        const calendarType = (s.dateCalendarType ?? s.DATE_CALENDAR_TYPE ?? 'gregorian') as string;
        const isAr = locale.startsWith('ar');

        if (calendarType === 'hijri') {
            const h = toHijri(dateObj.getFullYear(), dateObj.getMonth() + 1, dateObj.getDate());
            const d = String(h.hd).padStart(2, '0');
            const M = isAr ? HIJRI_MONTHS_SHORT_AR[h.hm - 1]! : HIJRI_MONTHS_SHORT_EN[h.hm - 1]!;
            const Y = String(h.hy);
            return format.replace('d', d).replace('M', M).replace('Y', Y);
        }

        if (format === TABLE_DATE_FORMAT) {
            const parts = new Intl.DateTimeFormat(locale === 'ar' ? 'ar' : 'en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
            }).formatToParts(dateObj);
            const day = parts.find((p) => p.type === 'day')?.value ?? '';
            const month = parts.find((p) => p.type === 'month')?.value ?? '';
            const year = parts.find((p) => p.type === 'year')?.value ?? '';
            return `${day} ${month}, ${year}`;
        }

        return convertPhpFormat(format, dateObj);
    }

    window.appSettings = {
        get: (key: string, defaultValue: any = null) => settings[key] ?? defaultValue,
        baseUrl: settings.base_url ?? 'http://localhost',
        imageUrl: settings.image_url ?? 'http://localhost',
        dateFormat: settings.dateFormat ?? settings.DATE_FORMAT ?? 'yyyy-MM-dd',
        dateCalendarType: (settings.dateCalendarType ?? settings.DATE_CALENDAR_TYPE ?? 'gregorian') === 'hijri' ? 'hijri' : 'gregorian',
        timeFormat: settings.timeFormat ?? settings.TIME_FORMAT ?? 'HH:mm',
        timezone: settings.defaultTimezone ?? 'UTC',
        language: settings.defaultLanguage ?? 'en',
        emailVerification: settings.emailVerification === true || settings.emailVerification === 'true',
        currencySettings,
        formatCurrency: (amount: number | string, options = { showSymbol: true, showCode: false }) => {
            try {
                // Parse the amount
                let numAmount = typeof amount === 'string' ? parseFloat(amount) : amount;

                // Format the number with the specified decimal places
                const decimalPlaces = parseInt(currencySettings.decimalFormat);

                // Handle float number setting
                if (!currencySettings.floatNumber) {
                    numAmount = Math.floor(numAmount);
                }

                // Format the number with the specified separators
                const parts = numAmount.toFixed(decimalPlaces).split('.');

                // Format the integer part with thousands separator
                if (currencySettings.thousandsSeparator !== 'none') {
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, currencySettings.thousandsSeparator);
                }

                // Join with decimal separator
                let formattedNumber = parts.join(currencySettings.decimalSeparator);

                // Add currency symbol with proper positioning and spacing
                if (options.showSymbol) {
                    const space = currencySettings.currencySymbolSpace ? ' ' : '';

                    if (currencySettings.currencySymbolPosition === 'before') {
                        formattedNumber = `${currencySettings.currencySymbol}${space}${formattedNumber}`;
                    } else {
                        formattedNumber = `${formattedNumber}${space}${currencySettings.currencySymbol}`;
                    }
                }

                // Add currency code if requested
                if (options.showCode) {
                    formattedNumber = `${formattedNumber} ${currencySettings.currencyCode}`;
                }

                return formattedNumber;
            } catch (error) {
                return amount.toString();
            }
        },
        formatCurrencyWithSuperAdminSettings: (amount: number | string, options = { showSymbol: true, showCode: false }) => {
            try {
                // Parse the amount
                let numAmount = typeof amount === 'string' ? parseFloat(amount) : amount;

                // Use super admin currency settings with fallbacks
                const decimalPlaces = parseInt(settings.superAdminDecimalFormat || '2');
                const thousandsSeparator = settings.superAdminThousandsSeparator || ',';
                const currencySymbolSpace = settings.superAdminCurrencySymbolSpace === true;
                const currencySymbolPosition = settings.superAdminCurrencySymbolPosition || 'before';
                const currencySymbol = settings.superAdminCurrencySymbol || '$';

                // Format the number
                let formattedNumber = numAmount.toFixed(decimalPlaces);
                
                // Add thousands separator
                const parts = formattedNumber.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);
                formattedNumber = parts.join('.');

                // Add currency symbol
                if (options.showSymbol) {
                    const space = currencySymbolSpace ? ' ' : '';
                    if (currencySymbolPosition === 'after') {
                        formattedNumber = `${formattedNumber}${space}${currencySymbol}`;
                    } else {
                        formattedNumber = `${currencySymbol}${space}${formattedNumber}`;
                    }
                }

                return formattedNumber;
            } catch (error) {
                return '$' + (typeof amount === 'number' ? amount.toFixed(2) : amount);
            }
        },
        formatDateTime: (date: string | Date, includeTime: boolean = true) => {
            if (!date) return null;

            try {
                const dateObj = typeof date === 'string' ? new Date(date) : date;
                const locale = (settings.defaultLanguage ?? settings.DEFAULT_LANGUAGE ?? 'en') as string;
                const datePart = translatedFormat(dateObj, TABLE_DATE_FORMAT, locale, settings);
                if (!includeTime) return datePart;
                const phpFormat = settings.timeFormat ?? settings.TIME_FORMAT ?? 'H:i';
                const timePart = convertPhpFormat(phpFormat, dateObj);
                return `${datePart} ${timePart}`;
            } catch (error) {
                return date.toString();
            }
        },
        formatDate: (date: string | Date) => {
            if (!date) return null;

            try {
                const dateObj = typeof date === 'string' ? new Date(date) : date;
                const locale = (settings.defaultLanguage ?? settings.DEFAULT_LANGUAGE ?? 'en') as string;
                return translatedFormat(dateObj, TABLE_DATE_FORMAT, locale, settings);
            } catch (error) {
                return date.toString();
            }
        },
        formatTime: (time: string | Date) => {
            if (!time) return null;

            try {
                const dateObj = typeof time === 'string' ? new Date(time) : time;
                const phpFormat = settings.timeFormat ?? 'H:i';
                return convertPhpFormat(phpFormat, dateObj);
            } catch (error) {
                return time.toString();
            }
        }
    };
    
    // Dynamic PHP to JS format conversion function
    function convertPhpFormat(phpFormat: string, dateObj: Date): string {
        const months = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];
        const monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const daysShort = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        return phpFormat.replace(/[a-zA-Z]/g, (match) => {
            switch (match) {
                case 'D': return daysShort[dateObj.getDay()];
                case 'l': return days[dateObj.getDay()];
                case 'M': return monthsShort[dateObj.getMonth()];
                case 'F': return months[dateObj.getMonth()];
                case 'j': return dateObj.getDate().toString();
                case 'd': return String(dateObj.getDate()).padStart(2, '0');
                case 'Y': return dateObj.getFullYear().toString();
                case 'y': return dateObj.getFullYear().toString().slice(-2);
                case 'm': return String(dateObj.getMonth() + 1).padStart(2, '0');
                case 'n': return (dateObj.getMonth() + 1).toString();
                case 'G': return String(dateObj.getHours());
                case 'H': return String(dateObj.getHours()).padStart(2, '0');
                case 'g': return String(dateObj.getHours() % 12 || 12);
                case 'h': return String(dateObj.getHours() % 12 || 12).padStart(2, '0');
                case 'i': return String(dateObj.getMinutes()).padStart(2, '0');
                case 's': return String(dateObj.getSeconds()).padStart(2, '0');
                case 'a': return dateObj.getHours() >= 12 ? 'pm' : 'am';
                case 'A': return dateObj.getHours() >= 12 ? 'PM' : 'AM';
                default: return match;
            }
        });
    }
}


export { };