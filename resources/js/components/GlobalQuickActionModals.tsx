import { useEffect, useMemo, useState } from 'react';
import { CrudFormModal } from '@/components/CrudFormModal';
import { useAxios } from '@/hooks/use-axios';
import { router } from '@inertiajs/react';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { PhoneInput, defaultCountries } from 'react-international-phone';
import { Repeater, RepeaterField } from '@/components/ui/repeater';

type ModalKey = 'cases' | 'clients' | 'tasks' | 'hearings';

interface CaseFormData {
  caseTypes: Array<{ id: number; name: string | Record<string, string> }>;
  caseStatuses: Array<{ id: number; name: string | Record<string, string> }>;
  caseCategories: Array<{ id: number; name: string | Record<string, string>; name_translations?: Record<string, string> }>;
  clients: Array<{ id: number; name: string }>;
  courts: Array<{ id: number; name: string }>;
  countries: Array<{ value: number; label: string }>;
  googleCalendarEnabled?: boolean;
  currentUser?: { id: number; name: string };
}

interface ClientFormData {
  clientTypes: Array<{ id: number; name: string | Record<string, string>; name_translations?: Record<string, string> }>;
  countries: Array<{ value: number; label: string | Record<string, string>; code?: string }>;
  phoneCountries?: Array<{ value: number; label: string | Record<string, string>; code: string }>;
  defaultTaxRate?: string;
  defaultCountryId?: number | null;
  defaultCountry?: string;
}

interface TaskFormData {
  taskTypes: Array<{ id: number; name: string | Record<string, string> }>;
  taskStatuses: Array<{ id: number; name: string | Record<string, string> }>;
  users: Array<{ id: number; name: string }>;
  cases: Array<{ id: number; case_id?: string | null; title?: string | null }>;
  googleCalendarEnabled?: boolean;
}

interface HearingFormData {
  cases: Array<{ id: number; case_id?: string | null; title?: string | null; file_number?: string | null }>;
  courts: Array<{
    id: number;
    name: string;
    court_type?: { name: string | Record<string, string> };
    circle_type?: { name: string | Record<string, string> };
  }>;
  judges: Array<{ id: number; name: string }>;
  hearingTypes: Array<{ id: number; name: string | Record<string, string> }>;
  googleCalendarEnabled?: boolean;
}

export function GlobalQuickActionModals() {
  const { t, i18n } = useTranslation();
  const currentLocale = i18n.language || 'en';
  const axios = useAxios();

  const [activeModal, setActiveModal] = useState<ModalKey | null>(null);
  const [caseData, setCaseData] = useState<CaseFormData | null>(null);
  const [clientData, setClientData] = useState<ClientFormData | null>(null);
  const [taskData, setTaskData] = useState<TaskFormData | null>(null);
  const [hearingData, setHearingData] = useState<HearingFormData | null>(null);
  const [isLoading, setIsLoading] = useState(false);

  const resolveName = (name: string | Record<string, string>) => {
    if (typeof name === 'string') {
      return name;
    }
    return name[currentLocale] || name.en || name.ar || '';
  };

  const getTranslatedValue = (value: any): string => {
    if (!value) return '-';
    if (typeof value === 'string') return value;
    if (typeof value === 'object' && value !== null) {
      return value[currentLocale] || value.en || value.ar || '-';
    }
    return '-';
  };

  const resolveCategoryName = (category: { name: string | Record<string, string>; name_translations?: Record<string, string> }) => {
    if (typeof category.name === 'string') {
      return category.name;
    }
    if (category.name_translations) {
      return category.name_translations[currentLocale] || category.name_translations.en || category.name_translations.ar || '';
    }
    return resolveName(category.name);
  };

  const closeModal = () => setActiveModal(null);

  const loadCaseData = async () => {
    if (caseData || isLoading) return;
    setIsLoading(true);
    try {
      const response = await axios.get(route('quick-actions.case-data'));
      setCaseData(response.data);
    } catch (error) {
      toast.error(t('Failed to load case form data'));
    } finally {
      setIsLoading(false);
    }
  };

  const loadClientData = async () => {
    if (clientData || isLoading) return;
    setIsLoading(true);
    try {
      const response = await axios.get(route('quick-actions.client-data'));
      setClientData(response.data);
    } catch (error) {
      toast.error(t('Failed to load client form data'));
    } finally {
      setIsLoading(false);
    }
  };

  const loadTaskData = async () => {
    if (taskData || isLoading) return;
    setIsLoading(true);
    try {
      const response = await axios.get(route('quick-actions.task-data'));
      setTaskData(response.data);
    } catch (error) {
      toast.error(t('Failed to load task form data'));
    } finally {
      setIsLoading(false);
    }
  };

  const loadHearingData = async () => {
    if (hearingData || isLoading) return;
    setIsLoading(true);
    try {
      const response = await axios.get(route('quick-actions.hearing-data'));
      setHearingData(response.data);
    } catch (error) {
      toast.error(t('Failed to load session form data'));
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    const handleQuickAction = (event: Event) => {
      const detail = (event as CustomEvent).detail as { key?: ModalKey } | undefined;
      if (!detail?.key) return;
      setActiveModal(detail.key);
      if (detail.key === 'cases') {
        loadCaseData();
      } else if (detail.key === 'clients') {
        loadClientData();
      } else if (detail.key === 'tasks') {
        loadTaskData();
      } else if (detail.key === 'hearings') {
        loadHearingData();
      }
    };

    window.addEventListener('quickAction:openModal', handleQuickAction);
    return () => {
      window.removeEventListener('quickAction:openModal', handleQuickAction);
    };
  }, [caseData, clientData, taskData, hearingData, isLoading]);

  const caseFormConfig = useMemo(() => {
    if (!caseData) return null;
    const clientOptions = [
      ...(caseData.clients || []).map((client) => ({
        value: client.id.toString(),
        label: client.name,
      })),
    ];

    if (caseData.currentUser) {
      clientOptions.push({
        value: caseData.currentUser.id.toString(),
        label: `${caseData.currentUser.name} (Me)`,
      });
    }

    return {
      fields: [
        {
          name: 'client_id',
          label: t('Client'),
          type: 'select',
          required: true,
          options: clientOptions,
        },
        {
          name: 'attributes',
          label: t('Attributes'),
          type: 'radio',
          required: true,
          defaultValue: 'petitioner',
          options: [
            { value: 'petitioner', label: t('Petitioner') },
            { value: 'respondent', label: t('Respondent') },
          ],
        },
        {
          name: 'opposite_parties',
          label: t('Opposite Party'),
          type: 'custom',
          render: (_field: any, formData: any, onChange: (name: string, value: any) => void) => {
            const repeaterFields: RepeaterField[] = [
              { name: 'name', label: t('Name'), type: 'text', required: true },
              { name: 'id_number', label: t('ID'), type: 'text' },
              {
                name: 'nationality_id',
                label: t('Nationality'),
                type: 'select',
                options: caseData.countries || [],
                placeholder: (caseData.countries || []).length > 0 ? t('Select Nationality') : t('No nationalities available'),
              },
              { name: 'lawyer_name', label: t('Lawyer Name'), type: 'text' },
            ];

            return (
              <Repeater
                fields={repeaterFields}
                value={formData.opposite_parties || []}
                onChange={(value) => onChange('opposite_parties', value)}
                minItems={1}
                maxItems={-1}
                addButtonText={t('Add Opposite Party')}
                removeButtonText={t('Remove')}
              />
            );
          },
        },
        { name: 'title', label: t('Case Title'), type: 'text', required: true },
        { name: 'case_number', label: t('Case Number'), type: 'text' },
        { name: 'file_number', label: t('File Number'), type: 'text' },
        {
          name: 'case_category_subcategory',
          type: 'dependent-dropdown',
          required: true,
          dependentConfig: [
            {
              name: 'case_category_id',
              label: t('Case Main Category'),
              options: caseData.caseCategories
                ? caseData.caseCategories.map((category) => ({
                  value: category.id.toString(),
                  label: resolveCategoryName(category),
                }))
                : [],
            },
            {
              name: 'case_subcategory_id',
              label: t('Case Sub Category'),
              apiEndpoint: '/case/case-categories/{case_category_id}/subcategories',
              showCurrentValue: true,
            },
          ],
        },
        {
          name: 'case_type_id',
          label: t('Case Type'),
          type: 'select',
          required: true,
          options: caseData.caseTypes.map((type) => ({
            value: type.id.toString(),
            label: resolveName(type.name),
          })),
        },
        {
          name: 'case_status_id',
          label: t('Case Status'),
          type: 'select',
          required: true,
          options: caseData.caseStatuses.map((status) => ({
            value: status.id.toString(),
            label: resolveName(status.name),
          })),
        },
        {
          name: 'priority',
          label: t('Priority'),
          type: 'select',
          required: true,
          options: [
            { value: 'low', label: t('Low') },
            { value: 'medium', label: t('Medium') },
            { value: 'high', label: t('High') },
          ],
          defaultValue: 'medium',
        },
        {
          name: 'court_id',
          label: t('Court'),
          type: 'select',
          options: caseData.courts.map((court) => ({
            value: court.id.toString(),
            label: court.name,
            key: `court-${court.id}`,
          })),
        },
        { name: 'filing_date', label: t('Filling Date'), type: 'date' },
        { name: 'expected_completion_date', label: t('Expecting Completion'), type: 'date' },
        { name: 'estimated_value', label: t('Estimated Value'), type: 'number' },
        { name: 'description', label: t('Description'), type: 'textarea' },
        {
          name: 'status',
          label: t('Status'),
          type: 'select',
          options: [
            { value: 'active', label: 'Active' },
            { value: 'inactive', label: 'Inactive' },
          ],
          defaultValue: 'active',
        },
        ...(caseData.googleCalendarEnabled
          ? [
            {
              name: 'sync_with_google_calendar',
              label: t('Synchronize in Google Calendar'),
              type: 'switch',
              defaultValue: false,
            },
          ]
          : []),
      ],
      modalSize: 'xl',
    };
  }, [caseData, t, currentLocale]);

  const clientFormConfig = useMemo(() => {
    if (!clientData) return null;
    const phoneCountries = clientData.phoneCountries || [];
    const phoneCountriesById = new Map(phoneCountries.map((country) => [String(country.value), country]));
    const phoneCountriesByCode = new Map(phoneCountries.map((country) => [String(country.code || '').toLowerCase(), country]));
    const phoneCountryCodes = phoneCountries
      .map((country) => String(country.code || '').toLowerCase())
      .filter((code) => code);
    const allowedPhoneCountries = phoneCountryCodes.length
      ? defaultCountries.filter((country) => phoneCountryCodes.includes(String(country[1]).toLowerCase()))
      : defaultCountries;
    const defaultPhoneCountry =
      phoneCountriesByCode.get(String(clientData.defaultCountry || '').toLowerCase()) ||
      phoneCountriesByCode.get('sa') ||
      phoneCountries[0];
    const countriesByCode = new Map((clientData.countries || []).map((country) => [String(country.code || '').toLowerCase(), country]));
    const defaultNationality = countriesByCode.get(String(clientData.defaultCountry || '').toLowerCase()) || (clientData.countries || [])[0];

    return {
      fields: [
        { name: 'name', label: t('Client Name'), type: 'text', required: true },
        {
          name: 'country_id',
          label: t('Phone Country'),
          type: 'text',
          defaultValue: defaultPhoneCountry?.value,
          conditional: () => false,
        },
        {
          name: 'phone',
          label: t('Phone Number'),
          type: 'text',
          required: true,
          render: (_: any, data: any, handleChange: (name: string, value: any) => void) => {
            const currentCountryId = data?.country_id || clientData.defaultCountryId || defaultPhoneCountry?.value;
            const currentCountry = phoneCountriesById.get(String(currentCountryId));
            const currentCountryCode = (currentCountry?.code || defaultPhoneCountry?.code || '').toLowerCase();

            return (
              <PhoneInput
                defaultCountry={currentCountryCode || undefined}
                value={data?.phone || ''}
                countries={allowedPhoneCountries}
                inputProps={{ name: 'phone', required: true }}
                className="w-full"
                inputClassName="w-full !h-10 !border !border-input !bg-background !text-sm !text-foreground"
                countrySelectorStyleProps={{
                  buttonClassName: '!h-10 !border !border-input !bg-background',
                  dropdownStyleProps: {
                    className: '!bg-background !text-foreground',
                  },
                }}
                onChange={(value, meta) => {
                  handleChange('phone', value || '');

                  const code = String(meta?.country?.iso2 || '').toLowerCase();
                  const selectedCountry = phoneCountriesByCode.get(code);
                  if (selectedCountry) {
                    handleChange('country_id', selectedCountry.value);
                  }
                }}
              />
            );
          },
        },
        { name: 'email', label: t('Email'), type: 'email', required: true },
        { name: 'password', label: t('Password'), type: 'password', required: true },
        {
          name: 'client_type_id',
          label: t('Client Type'),
          type: 'select',
          required: false,
          options: clientData.clientTypes
            ? clientData.clientTypes.map((type) => {
              const translations = type.name_translations || (typeof type.name === 'object' ? type.name : null);
              let displayName: string | Record<string, string> = type.name;
              if (translations && typeof translations === 'object') {
                displayName = translations[currentLocale] || translations.en || translations.ar || type.name || '';
              } else if (typeof type.name === 'object') {
                displayName = type.name[currentLocale] || type.name.en || type.name.ar || '';
              }
              return {
                value: type.id.toString(),
                label: displayName,
              };
            })
            : [],
        },
        {
          name: 'business_type',
          label: t('Business Type'),
          type: 'radio',
          required: true,
          colSpan: 12,
          options: [
            { value: 'b2c', label: t('Individual') },
            { value: 'b2b', label: t('Business') },
          ],
          defaultValue: 'b2c',
        },
        {
          name: 'nationality_id',
          label: t('Nationality'),
          type: 'select',
          required: false,
          options: clientData.countries || [],
          defaultValue: defaultNationality ? defaultNationality.value : '',
          conditional: (_: any, data: any) => data?.business_type === 'b2c',
        },
        {
          name: 'id_number',
          label: t('ID Number'),
          type: 'text',
          required: false,
          conditional: (_: any, data: any) => data?.business_type === 'b2c',
        },
        {
          name: 'gender',
          label: t('Gender'),
          type: 'select',
          required: false,
          options: [
            { value: 'male', label: t('Male') },
            { value: 'female', label: t('Female') },
          ],
          conditional: (_: any, data: any) => data?.business_type === 'b2c',
        },
        {
          name: 'date_of_birth',
          label: t('Date of Birth'),
          type: 'date',
          conditional: (_: any, data: any) => data?.business_type === 'b2c',
        },
        {
          name: 'unified_number',
          label: t('Unified Number'),
          type: 'text',
          required: false,
          conditional: (_: any, data: any) => data?.business_type === 'b2b',
        },
        {
          name: 'cr_number',
          label: t('CR Number'),
          type: 'text',
          required: false,
          conditional: (_: any, data: any) => data?.business_type === 'b2b',
        },
        {
          name: 'cr_issuance_date',
          label: t('CR Issuance Date'),
          type: 'date',
          required: false,
          conditional: (_: any, data: any) => data?.business_type === 'b2b',
        },
        {
          name: 'tax_id',
          label: t('Tax ID'),
          type: 'text',
          required: false,
          conditional: (_: any, data: any) => data?.business_type === 'b2b',
        },
        {
          name: 'address',
          label: t('Address'),
          type: 'textarea',
        },
        {
          name: 'tax_rate',
          label: t('Tax Rate') + ' (%)',
          type: 'number',
          step: '0.01',
          min: '0',
          max: '100',
          defaultValue: clientData.defaultTaxRate ? Number(clientData.defaultTaxRate) : 0,
        },
        { name: 'notes', label: t('Note'), type: 'textarea' },
        {
          name: 'status',
          label: t('Status'),
          type: 'select',
          options: [
            { value: 'active', label: t('Active') },
            { value: 'inactive', label: t('Inactive') },
          ],
          defaultValue: 'active',
        },
      ],
      modalSize: 'xl',
    };
  }, [clientData, t, currentLocale]);

  const clientInitialData = useMemo(() => {
    if (!clientData) return null;
    const countriesByCode = new Map((clientData.countries || []).map((country) => [String(country.code || '').toLowerCase(), country]));
    const defaultNationality = countriesByCode.get(String(clientData.defaultCountry || '').toLowerCase()) || (clientData.countries || [])[0];

    return {
      business_type: 'b2c',
      nationality_id: defaultNationality ? defaultNationality.value : '',
    };
  }, [clientData]);

  const clientModalKey = activeModal === 'clients'
    ? `clients-${clientData ? 'ready' : 'loading'}`
    : 'clients-closed';

  const taskFormConfig = useMemo(() => {
    if (!taskData) return null;
    return {
      fields: [
        { name: 'title', label: t('Title'), type: 'text', required: true },
        {
          name: 'priority',
          label: t('Priority'),
          type: 'select',
          required: true,
          options: [
            { value: 'critical', label: t('Critical') },
            { value: 'high', label: t('High') },
            { value: 'medium', label: t('Medium') },
            { value: 'low', label: t('Low') },
          ],
          defaultValue: 'medium',
        },
        {
          name: 'status',
          label: t('Status'),
          type: 'select',
          required: true,
          options: [
            { value: 'not_started', label: t('Not Started') },
            { value: 'in_progress', label: t('In Progress') },
            { value: 'completed', label: t('Completed') },
            { value: 'on_hold', label: t('On Hold') },
          ],
          defaultValue: 'not_started',
        },
        { name: 'due_date', label: t('Due Date'), type: 'date' },
        {
          name: 'case_id',
          label: t('Case'),
          type: 'select',
          options: taskData.cases.map((c) => ({
            value: c.id.toString(),
            label: c.title || c.case_id || `Case ${c.id}`,
          })),
        },
        {
          name: 'assigned_to',
          label: t('Assigned To'),
          type: 'select',
          options: taskData.users.map((user) => ({
            value: user.id.toString(),
            label: user.name,
          })),
        },
        {
          name: 'task_type_id',
          label: t('Task Type'),
          type: 'select',
          options: taskData.taskTypes.map((type) => ({
            value: type.id.toString(),
            label: resolveName(type.name),
          })),
        },
        {
          name: 'task_status_id',
          label: t('Task Status'),
          type: 'select',
          options: taskData.taskStatuses.map((status) => ({
            value: status.id.toString(),
            label: resolveName(status.name),
          })),
        },
        { name: 'notes', label: t('Notes'), type: 'textarea' },
        ...(taskData.googleCalendarEnabled
          ? [
            {
              name: 'sync_with_google_calendar',
              label: t('Synchronize in Google Calendar'),
              type: 'switch',
              defaultValue: false,
            },
          ]
          : []),
      ],
      modalSize: 'xl',
    };
  }, [taskData, t, currentLocale]);

  const hearingFormConfig = useMemo(() => {
    if (!hearingData) return null;
    const statusOptions = [
      { value: 'scheduled', label: t('Scheduled') },
      { value: 'in_progress', label: t('In Progress') },
      { value: 'completed', label: t('Completed') },
      { value: 'postponed', label: t('Postponed') },
      { value: 'cancelled', label: t('Cancelled') },
    ];

    return {
      fields: [
        {
          name: 'case_id',
          label: t('Case'),
          type: 'select',
          required: true,
          options: hearingData.cases
            ? hearingData.cases.map((c) => ({
              value: c.id.toString(),
              label: `${c.case_id || '-'} - ${c.title || '-'}`,
            }))
            : [],
        },
        {
          name: 'court_id',
          label: t('Court'),
          type: 'select',
          required: true,
          options: hearingData.courts
            ? hearingData.courts.map((c) => {
              const courtName = c.name || '';
              const courtType = c.court_type ? getTranslatedValue(c.court_type.name) : '';
              const circleType = c.circle_type ? getTranslatedValue(c.circle_type.name) : '';
              const parts = [courtName];
              if (courtType) parts.push(courtType);
              if (circleType) parts.push(circleType);
              return {
                value: c.id.toString(),
                label: parts.join(' + '),
              };
            })
            : [],
        },
        { name: 'circle_number', label: t('Circle Number'), type: 'text' },
        {
          name: 'judge_id',
          label: t('Judge'),
          type: 'select',
          options: [{ value: 'none', label: t('Select Judge') }, ...(hearingData.judges
            ? hearingData.judges.map((j) => ({
              value: j.id.toString(),
              label: j.name,
            }))
            : [])],
        },
        {
          name: 'hearing_type_id',
          label: t('Session Type'),
          type: 'select',
          required: true,
          options: [{ value: 'none', label: t('Select Type') }, ...(hearingData.hearingTypes
            ? hearingData.hearingTypes.map((ht) => ({
              value: ht.id.toString(),
              label: getTranslatedValue(ht.name),
            }))
            : [])],
        },
        { name: 'title', label: t('Title'), type: 'text', required: true },
        { name: 'description', label: t('Description'), type: 'textarea' },
        { name: 'hearing_date', label: t('Date'), type: 'date', required: true },
        { name: 'hearing_time', label: t('Time'), type: 'time', required: true },
        { name: 'duration_minutes', label: t('Duration (minutes)'), type: 'number', defaultValue: 60 },
        { name: 'url', label: t('URL'), type: 'text' },
        {
          name: 'status',
          label: t('Status'),
          type: 'select',
          options: statusOptions,
          defaultValue: 'scheduled',
        },
        { name: 'notes', label: t('Notes'), type: 'textarea' },
        ...(hearingData.googleCalendarEnabled
          ? [
            {
              name: 'sync_with_google_calendar',
              label: t('Synchronize in Google Calendar'),
              type: 'switch',
              defaultValue: false,
            },
          ]
          : []),
      ],
      modalSize: 'xl',
    };
  }, [hearingData, t, currentLocale]);

  const handleSubmit = (routeName: string) => (formData: any) => {
    if (routeName === 'cases.store') {
      if (formData.case_category_id === 'none' || formData.case_category_id === '') {
        formData.case_category_id = null;
      }
      if (formData.case_subcategory_id === 'none' || formData.case_subcategory_id === '') {
        formData.case_subcategory_id = null;
      }
    }

    router.post(route(routeName), formData, {
      preserveScroll: true,
      onSuccess: (page) => {
        const successMessage = (page.props as any)?.flash?.success;
        if (successMessage) {
          toast.success(successMessage);
        } else {
          toast.success(t('Saved successfully'));
        }
        closeModal();
      },
      onError: (errors) => {
        const errorMessage = (errors as any)?.error || Object.values(errors).join(', ') || t('Failed to save');
        toast.error(errorMessage);
      },
    });
  };

  return (
    <>
      <CrudFormModal
        isOpen={activeModal === 'cases'}
        onClose={closeModal}
        onSubmit={handleSubmit('cases.store')}
        formConfig={(caseFormConfig as any) || { fields: [], modalSize: 'xl' }}
        initialData={{ case_category_id: '', case_subcategory_id: '', opposite_parties: [] }}
        title={t('Add New Case')}
        mode="create"
      />

      <CrudFormModal
        key={clientModalKey}
        isOpen={activeModal === 'clients'}
        onClose={closeModal}
        onSubmit={handleSubmit('clients.store')}
        formConfig={(clientFormConfig as any) || { fields: [], modalSize: 'xl' }}
        initialData={clientInitialData}
        title={t('Add New Client')}
        mode="create"
      />

      <CrudFormModal
        isOpen={activeModal === 'tasks'}
        onClose={closeModal}
        onSubmit={handleSubmit('tasks.store')}
        formConfig={(taskFormConfig as any) || { fields: [], modalSize: 'xl' }}
        initialData={null}
        title={t('Add New Task')}
        mode="create"
      />

      <CrudFormModal
        isOpen={activeModal === 'hearings'}
        onClose={closeModal}
        onSubmit={handleSubmit('hearings.store')}
        formConfig={(hearingFormConfig as any) || { fields: [], modalSize: 'xl' }}
        initialData={null}
        title={t('Schedule New Session')}
        mode="create"
      />
    </>
  );
}
