import { useState, useEffect, useMemo, useCallback } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Save, Edit } from 'lucide-react';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { PhoneInput, defaultCountries } from 'react-international-phone';

const defaultOfficeFields = {
  address: '',
  consultation_fees: '',
  office_hours: '',
  success_rate: '',
  name: '',
  registration_number: '',
  establishment_date: '',
  cr: '',
  tax_number: '',
  company_size: 'solo',
  business_type: 'PROFESSIONAL_COMPANY',
  default_setup: '',
  services_offered: '',
  description: '',
};

export default function CompanyProfiles() {
  const { t } = useTranslation();
  const {
    companyProfile,
    officeSizeOptions = [],
    businessTypeOptions = [],
    phoneCountries = [],
    defaultCountry = '',
    registrationDomain = '',
    tenantCity = '',
    accountUser,
  } = usePage().props as any;

  const phoneCountriesByCode = useMemo(
    () => new Map((phoneCountries || []).map((country: any) => [String(country.code).toLowerCase(), country])),
    [phoneCountries],
  );
  const phoneCountryCodes = (phoneCountries || [])
    .map((country: any) => String(country.code || '').toLowerCase())
    .filter((code: string) => code);
  const allowedPhoneCountries = phoneCountryCodes.length
    ? defaultCountries.filter((country) => phoneCountryCodes.includes(String(country[1]).toLowerCase()))
    : defaultCountries;
  const defaultPhoneCountry =
    phoneCountriesByCode.get(String(defaultCountry).toLowerCase()) || phoneCountriesByCode.get('sa') || (phoneCountries || [])[0];

  const buildFormState = useCallback(() => {
    const office = companyProfile
      ? {
          address: companyProfile.address || '',
          consultation_fees: companyProfile.consultation_fees || '',
          office_hours: companyProfile.office_hours || '',
          success_rate: companyProfile.success_rate || '',
          name: companyProfile.name || '',
          registration_number: companyProfile.registration_number || '',
          establishment_date: companyProfile.establishment_date ? companyProfile.establishment_date.split('T')[0] : '',
          cr: companyProfile.cr || '',
          tax_number: companyProfile.tax_number || '',
          company_size: companyProfile.company_size || 'solo',
          business_type: companyProfile.business_type || 'PROFESSIONAL_COMPANY',
          default_setup: companyProfile.default_setup || '',
          services_offered: companyProfile.services_offered || '',
          description: companyProfile.description || '',
        }
      : { ...defaultOfficeFields };

    return {
      full_name: accountUser?.name ?? '',
      account_email: accountUser?.email ?? '',
      account_phone: accountUser?.phone ?? '',
      account_city: tenantCity ?? '',
      ...office,
    };
  }, [companyProfile, accountUser, tenantCity]);

  const [isEditing, setIsEditing] = useState(!companyProfile);
  const [formData, setFormData] = useState(buildFormState);

  useEffect(() => {
    setFormData(buildFormState());
  }, [buildFormState]);

  const handleChange = (name: string, value: any) => {
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const submitData = {
      full_name: formData.full_name,
      account_email: formData.account_email,
      account_phone: formData.account_phone,
      account_city: formData.account_city,
      name: formData.name,
      registration_number: formData.registration_number,
      establishment_date: formData.establishment_date,
      business_type: formData.business_type || 'PROFESSIONAL_COMPANY',
      cr: formData.cr,
      tax_number: formData.tax_number,
      address: formData.address,
      office_hours: formData.office_hours,
      company_size: formData.company_size || 'solo',
      consultation_fees: formData.consultation_fees,
      success_rate: formData.success_rate,
      services_offered: formData.services_offered,
      description: formData.description,
      default_setup: formData.default_setup,
    };

    if (companyProfile) {
      toast.loading(t('Updating advocate profile...'));
      router.put(route('advocate.company-profiles.update', companyProfile?.id || 1), submitData, {
        onSuccess: () => {
          toast.dismiss();
          toast.success(t('Advocate profile updated successfully'));
          setIsEditing(false);
        },
        onError: (errors) => {
          toast.dismiss();
          if (typeof errors === 'object' && errors !== null) {
            const errorMessages = Object.values(errors).flat().join(', ');
            toast.error(errorMessages);
          } else {
            toast.error(t('Failed to update advocate profile'));
          }
        },
      });
    } else {
      toast.loading(t('Creating advocate profile...'));
      router.post(route('advocate.company-profiles.store'), submitData, {
        onSuccess: () => {
          toast.dismiss();
          toast.success(t('Advocate profile created successfully'));
          setIsEditing(false);
        },
        onError: (errors) => {
          toast.dismiss();
          if (typeof errors === 'object' && errors !== null) {
            const errorMessages = Object.values(errors).flat().join(', ');
            toast.error(errorMessages);
          } else {
            toast.error(t('Failed to create advocate profile'));
          }
        },
      });
    }
  };

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Settings'), href: route('advocate.company-profiles.index') },
    { title: t('Company Profile') },
  ];

  const toggleEdit = () => {
    if (isEditing) {
      setFormData(buildFormState());
    }
    setIsEditing(!isEditing);
  };

  const pageActions = [
    {
      label: isEditing ? t('Cancel') : t('Edit Company Profile'),
      icon: isEditing ? null : <Edit className="h-4 w-4 mr-2" />,
      variant: (isEditing ? 'outline' : 'default') as 'outline' | 'default',
      onClick: toggleEdit,
    },
  ];

  const disabled = !isEditing;
  const domainDisplay = registrationDomain || '—';

  return (
    <PageTemplate title={t('Company Profile')} url="/advocate/company-profiles" actions={pageActions} breadcrumbs={breadcrumbs}>
      <form onSubmit={handleSubmit} className="space-y-6">
        <Card>
          <CardHeader>
            <CardTitle className="text-lg font-semibold">{t('Account information')}</CardTitle>
            <CardDescription className="text-sm text-muted-foreground">{t('Account information subtitle')}</CardDescription>
          </CardHeader>
          <CardContent className="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div className="space-y-2">
              <Label htmlFor="full_name" className="text-sm font-medium">
                {t('Full Name')}
              </Label>
              <Input
                id="full_name"
                value={formData.full_name}
                onChange={(e) => handleChange('full_name', e.target.value)}
                disabled={disabled}
                placeholder={t('Full Name')}
                className="text-sm"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="registration_domain" className="text-sm font-medium">
                {t('Domain')}
              </Label>
              <Input id="registration_domain" value={domainDisplay} readOnly className="text-sm bg-muted" />
              <p className="text-xs text-muted-foreground">{t('Registration domain hint')}</p>
            </div>
            <div className="space-y-2">
              <Label htmlFor="account_phone" className="text-sm font-medium">
                {t('Phone Number')}
              </Label>
              <div className="phone-left-selector">
                <PhoneInput
                  defaultCountry={(defaultPhoneCountry?.code || '').toLowerCase() || undefined}
                  value={formData.account_phone}
                  countries={allowedPhoneCountries}
                  inputProps={{ name: 'account_phone', readOnly: disabled }}
                  className="w-full"
                  inputClassName="w-full !h-10 !border !border-input !bg-background !text-sm !text-foreground disabled:!opacity-100 disabled:!cursor-default"
                  countrySelectorStyleProps={{
                    buttonClassName: '!h-10 !border !border-input !bg-background disabled:!opacity-100',
                    dropdownStyleProps: {
                      className: '!bg-background !text-foreground phone-country-dropdown',
                    },
                  }}
                  onChange={(value) => handleChange('account_phone', value || '')}
                />
              </div>
            </div>
            <div className="space-y-2">
              <Label htmlFor="account_email" className="text-sm font-medium">
                {t('Email address')}
              </Label>
              <Input
                id="account_email"
                type="email"
                value={formData.account_email}
                onChange={(e) => handleChange('account_email', e.target.value)}
                disabled={disabled}
                placeholder={t('Email address')}
                className="text-sm"
              />
            </div>
            <div className="space-y-2 md:col-span-2">
              <Label htmlFor="account_city" className="text-sm font-medium">
                {t('City')}
              </Label>
              <Input
                id="account_city"
                value={formData.account_city}
                onChange={(e) => handleChange('account_city', e.target.value)}
                disabled={disabled}
                placeholder={t('City')}
                className="text-sm max-w-xl"
              />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="text-lg font-semibold">{t('Office information')}</CardTitle>
            <CardDescription className="text-sm text-muted-foreground">{t('Office information subtitle')}</CardDescription>
          </CardHeader>
          <CardContent className="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div className="space-y-2">
              <Label htmlFor="name" className="text-sm font-medium">
                {t('Company or Office Name')}
              </Label>
              <Input
                id="name"
                value={formData.name}
                onChange={(e) => handleChange('name', e.target.value)}
                disabled={disabled}
                required={isEditing}
                placeholder={t('Company or Office Name')}
                className="text-sm"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="business_type" className="text-sm font-medium">
                {t('Business Type')}
              </Label>
              <Select value={formData.business_type || 'PROFESSIONAL_COMPANY'} onValueChange={(value) => handleChange('business_type', value)} disabled={disabled}>
                <SelectTrigger className="text-sm">
                  <SelectValue placeholder={t('Choose Activity Type')} />
                </SelectTrigger>
                <SelectContent>
                  {(businessTypeOptions as { value: string; labelKey: string }[]).map((option) => (
                    <SelectItem key={option.value} value={option.value} className="text-sm">
                      {t(option.labelKey)}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="registration_number" className="text-sm font-medium">
                {t('Registration Number')}
              </Label>
              <Input
                id="registration_number"
                value={formData.registration_number}
                onChange={(e) => handleChange('registration_number', e.target.value)}
                disabled={disabled}
                placeholder={t('Enter Registry Number')}
                className="text-sm"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="establishment_date" className="text-sm font-medium">
                {t('Establishment Date')}
              </Label>
              <Input
                id="establishment_date"
                type="date"
                value={formData.establishment_date}
                onChange={(e) => handleChange('establishment_date', e.target.value)}
                disabled={disabled}
                className="text-sm"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="cr" className="text-sm font-medium">
                {t('Commercial Register')}
              </Label>
              <Input
                id="cr"
                value={formData.cr}
                onChange={(e) => handleChange('cr', e.target.value)}
                disabled={disabled}
                placeholder={t('Commercial Register')}
                className="text-sm"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="tax_number" className="text-sm font-medium">
                {t('Tax Number')}
              </Label>
              <Input
                id="tax_number"
                value={formData.tax_number}
                onChange={(e) => handleChange('tax_number', e.target.value)}
                disabled={disabled}
                placeholder={t('Enter Tax Number')}
                className="text-sm"
              />
            </div>
            <div className="space-y-2 md:col-span-2">
              <Label htmlFor="address" className="text-sm font-medium">
                {t('Address')}
              </Label>
              <Textarea
                id="address"
                value={formData.address}
                onChange={(e) => handleChange('address', e.target.value)}
                disabled={disabled}
                rows={2}
                placeholder={t('Address')}
                className="text-sm"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="office_hours" className="text-sm font-medium">
                {t('Working Hours')}
              </Label>
              <Input
                id="office_hours"
                value={formData.office_hours}
                onChange={(e) => handleChange('office_hours', e.target.value)}
                disabled={disabled}
                placeholder={t('Working Hours')}
                className="text-sm"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="company_size" className="text-sm font-medium">
                {t('Office Size')}
              </Label>
              <Select value={formData.company_size || 'solo'} onValueChange={(value) => handleChange('company_size', value)} disabled={disabled}>
                <SelectTrigger className="text-sm">
                  <SelectValue placeholder={t('Choose Office Size')} />
                </SelectTrigger>
                <SelectContent>
                  {(officeSizeOptions as { value: string; labelKey: string }[]).map((option) => (
                    <SelectItem key={option.value} value={option.value} className="text-sm">
                      {t(option.labelKey)}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="consultation_fees" className="text-sm font-medium">
                {t('Consultation Fees (SAR)')}
              </Label>
              <Input
                id="consultation_fees"
                type="number"
                min="0"
                step="0.01"
                value={formData.consultation_fees}
                onChange={(e) => handleChange('consultation_fees', e.target.value)}
                disabled={disabled}
                placeholder={t('Consultation Fees (SAR)')}
                className="text-sm"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="success_rate" className="text-sm font-medium">
                {t('Success Rate (%)')}
              </Label>
              <Input
                id="success_rate"
                type="number"
                min={0}
                max={100}
                value={formData.success_rate}
                onChange={(e) => handleChange('success_rate', e.target.value)}
                disabled={disabled}
                placeholder={t('Success Rate (%)')}
                className="text-sm"
              />
            </div>
            <div className="space-y-2 md:col-span-2">
              <Label htmlFor="services_offered" className="text-sm font-medium">
                {t('Services Provided')}
              </Label>
              <Textarea
                id="services_offered"
                value={formData.services_offered}
                onChange={(e) => handleChange('services_offered', e.target.value)}
                disabled={disabled}
                rows={3}
                placeholder={t('Services Provided')}
                className="text-sm"
              />
            </div>
            <div className="space-y-2 md:col-span-2">
              <Label htmlFor="description" className="text-sm font-medium">
                {t('Description')}
              </Label>
              <Textarea
                id="description"
                value={formData.description}
                onChange={(e) => handleChange('description', e.target.value)}
                disabled={disabled}
                rows={3}
                placeholder={t('Description')}
                className="text-sm"
              />
            </div>
          </CardContent>
        </Card>

        {isEditing && (
          <div className="flex justify-end">
            <Button type="submit" className="flex items-center gap-2">
              <Save className="h-4 w-4" />
              {companyProfile ? t('Update Profile') : t('Create Profile')}
            </Button>
          </div>
        )}
      </form>
    </PageTemplate>
  );
}
