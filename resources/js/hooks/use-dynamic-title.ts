import { useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { useBrand } from '@/contexts/BrandContext';

export function useDynamicTitle() {
  const { titleTextEn, titleTextAr } = useBrand();
  const { i18n } = useTranslation();
  const lang = i18n.language?.split('-')[0] || 'en';
  const appTitle = lang === 'ar' ? titleTextAr : titleTextEn;

  useEffect(() => {
    const currentTitle = document.title;
    const parts = currentTitle.split(' - ');
    if (parts.length > 1) {
      document.title = `${parts[0]} - ${appTitle}`;
    } else {
      document.title = appTitle;
    }
  }, [appTitle]);
}