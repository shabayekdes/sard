import { useState, useEffect } from 'react';
import { PageTemplate } from '@/components/page-template';
import { usePage, router } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Save, Edit, User } from 'lucide-react';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';

export default function CompanyProfiles() {
  const { t } = useTranslation();
  const { companyProfile } = usePage().props as any;
  const [isEditing, setIsEditing] = useState(!companyProfile);
  const [formData, setFormData] = useState({
    // Contact Details
    email: '',
    phone: '',
    address: '',

    // Business Details
    consultation_fees: '',
    office_hours: '',
    success_rate: '',

    // Company Details
    name: '',
    registration_number: '',
    establishment_date: '',
    cr: '',
    tax_number: '',
    company_size: 'solo',
    business_type: 'law_firm',
    default_setup: '',

    // Services
    services_offered: '',
    description: ''
  });

  useEffect(() => {
    if (companyProfile) {
      setFormData({
        email: companyProfile.email || '',
        phone: companyProfile.phone || '',
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
        business_type: companyProfile.business_type || 'law_firm',
        default_setup: companyProfile.default_setup || '',
        services_offered: companyProfile.services_offered || '',
        description: companyProfile.description || ''
      });
    }
  }, [companyProfile]);

  const handleChange = (name: string, value: any) => {
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    // Ensure required fields have values
    const submitData = {
      ...formData,
      company_size: formData.company_size || 'solo',
      business_type: formData.business_type || 'law_firm'
    };

    if (companyProfile) {
      // Update existing profile
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
        }
      });
    } else {
      // Create new profile
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
        }
      });
    }
  };

  const breadcrumbs = [
    { title: t('Dashboard'), href: route('dashboard') },
    { title: t('Settings'), href: route('advocate.company-profiles.index') },
    { title: t('Company Profile') }
  ];

  const pageActions = [
    {
      label: isEditing ? t('Cancel') : t('Edit Profile'),
      icon: isEditing ? null : <Edit className="h-4 w-4 mr-2" />,
      variant: isEditing ? 'outline' : 'default',
      onClick: () => setIsEditing(!isEditing)
    }
  ];

  return (
    <PageTemplate
      title={t("Advocate Profile")}
      url="/advocate/company-profiles"
      actions={pageActions}
      breadcrumbs={breadcrumbs}
    >
      <form onSubmit={handleSubmit} className="space-y-6">
        {/* Contact Details */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2 text-lg font-semibold">
              <User className="h-5 w-5" />
              {t('Contact Details')}
            </CardTitle>
            <CardDescription className="text-sm text-muted-foreground">{t('Primary company contact information')}</CardDescription>
          </CardHeader>
          <CardContent className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label htmlFor="email" className="text-sm font-medium">{t('Email')}</Label>
              <Input
                id="email"
                type="email"
                value={formData.email}
                onChange={(e) => handleChange('email', e.target.value)}
                disabled={!isEditing}
                className="text-sm"
              />
            </div>
            <div>
              <Label htmlFor="phone" className="text-sm font-medium">{t('Mobile')}</Label>
              <Input
                id="phone"
                value={formData.phone}
                onChange={(e) => handleChange('phone', e.target.value)}
                disabled={!isEditing}
                className="text-sm"
              />
            </div>
            <div className="md:col-span-2">
              <Label htmlFor="address" className="text-sm font-medium">{t('Address')}</Label>
              <Textarea
                id="address"
                value={formData.address}
                onChange={(e) => handleChange('address', e.target.value)}
                disabled={!isEditing}
                rows={2}
                className="text-sm"
              />
            </div>
          </CardContent>
        </Card>

        {/* Business Details */}
        <Card>
          <CardHeader>
            <CardTitle className="text-lg font-semibold">{t('Business Details')}</CardTitle>
            <CardDescription className="text-sm text-muted-foreground">{t('Practice and consultation information')}</CardDescription>
          </CardHeader>
          <CardContent className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label htmlFor="consultation_fees" className="text-sm font-medium">{t('Consultation Fees')}</Label>
              <Input
                id="consultation_fees"
                type="number"
                value={formData.consultation_fees}
                onChange={(e) => handleChange('consultation_fees', e.target.value)}
                disabled={!isEditing}
                className="text-sm"
              />
            </div>
            <div>
              <Label htmlFor="success_rate" className="text-sm font-medium">{t('Success Rate (%)')}</Label>
              <Input
                id="success_rate"
                type="number"
                max="100"
                value={formData.success_rate}
                onChange={(e) => handleChange('success_rate', e.target.value)}
                disabled={!isEditing}
                className="text-sm"
              />
            </div>
            <div>
              <Label htmlFor="office_hours" className="text-sm font-medium">{t('Office Hours')}</Label>
              <Input
                id="office_hours"
                value={formData.office_hours}
                onChange={(e) => handleChange('office_hours', e.target.value)}
                disabled={!isEditing}
                placeholder="e.g., Mon-Fri 9:00 AM - 6:00 PM"
                className="text-sm"
              />
            </div>
            <div>
              <Label htmlFor="company_size" className="text-sm font-medium">{t('Office Size')}</Label>
              <Select
                value={formData.company_size || 'solo'}
                onValueChange={(value) => handleChange('company_size', value)}
                disabled={!isEditing}
              >
                <SelectTrigger className="text-sm">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="solo" className="text-sm">{t('Solo Practice')}</SelectItem>
                  <SelectItem value="small" className="text-sm">{t('Small')}</SelectItem>
                  <SelectItem value="medium" className="text-sm">{t('Med')}</SelectItem>
                  <SelectItem value="large" className="text-sm">{t('Large')}</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div className="md:col-span-2">
              <Label htmlFor="services_offered" className="text-sm font-medium">{t('Service Offers')}</Label>
              <Textarea
                id="services_offered"
                value={formData.services_offered}
                onChange={(e) => handleChange('services_offered', e.target.value)}
                disabled={!isEditing}
                rows={3}
                className="text-sm"
              />
            </div>
          </CardContent>
        </Card>

        {/* Firm Details */}
        <Card>
          <CardHeader>
            <CardTitle className="text-lg font-semibold">{t('Firm Details')}</CardTitle>
            <CardDescription className="text-sm text-muted-foreground">{t('Legal firm registration and business information')}</CardDescription>
          </CardHeader>
          <CardContent className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label htmlFor="name" className="text-sm font-medium">{t('Company Name')} *</Label>
              <Input
                id="name"
                value={formData.name}
                onChange={(e) => handleChange('name', e.target.value)}
                disabled={!isEditing}
                required
                className="text-sm"
              />
            </div>
            <div>
              <Label htmlFor="registration_number" className="text-sm font-medium">{t('Registration Number')}</Label>
              <Input
                id="registration_number"
                value={formData.registration_number}
                onChange={(e) => handleChange('registration_number', e.target.value)}
                disabled={!isEditing}
                className="text-sm"
              />
            </div>
            <div>
              <Label htmlFor="establishment_date" className="text-sm font-medium">{t('Establishment Date')}</Label>
              <Input
                id="establishment_date"
                type="date"
                value={formData.establishment_date}
                onChange={(e) => handleChange('establishment_date', e.target.value)}
                disabled={!isEditing}
                className="text-sm"
              />
            </div>
            <div>
              <Label htmlFor="cr" className="text-sm font-medium">{t('CR')}</Label>
              <Input
                id="cr"
                value={formData.cr}
                onChange={(e) => handleChange('cr', e.target.value)}
                disabled={!isEditing}
                className="text-sm"
              />
            </div>
            <div>
              <Label htmlFor="tax_number" className="text-sm font-medium">{t('Tax Number')}</Label>
              <Input
                id="tax_number"
                value={formData.tax_number}
                onChange={(e) => handleChange('tax_number', e.target.value)}
                disabled={!isEditing}
                className="text-sm"
              />
            </div>
            <div>
              <Label htmlFor="business_type" className="text-sm font-medium">{t('Business Type')}</Label>
              <Select
                value={formData.business_type || 'law_firm'}
                onValueChange={(value) => handleChange('business_type', value)}
                disabled={!isEditing}
              >
                <SelectTrigger className="text-sm">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="law_firm" className="text-sm">{t('Law Firm')}</SelectItem>
                  <SelectItem value="corporate_legal" className="text-sm">{t('Corporate Legal')}</SelectItem>
                  <SelectItem value="government" className="text-sm">{t('Government')}</SelectItem>
                  <SelectItem value="other" className="text-sm">{t('Other')}</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div>
              <Label htmlFor="default_setup" className="text-sm font-medium">{t('Default Setup')}</Label>
              <Input
                id="default_setup"
                value={formData.default_setup}
                onChange={(e) => handleChange('default_setup', e.target.value)}
                disabled={!isEditing}
                className="text-sm"
              />
            </div>
            <div className="md:col-span-2">
              <Label htmlFor="description" className="text-sm font-medium">{t('Description')}</Label>
              <Textarea
                id="description"
                value={formData.description}
                onChange={(e) => handleChange('description', e.target.value)}
                disabled={!isEditing}
                rows={3}
                className="text-sm"
              />
            </div>
          </CardContent>
        </Card>

        {/* Save Button */}
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