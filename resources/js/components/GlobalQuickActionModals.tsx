import { useEffect, useMemo, useState } from 'react';
import { CrudFormModal } from '@/components/CrudFormModal';
import { useAxios } from '@/hooks/use-axios';
import { router } from '@inertiajs/react';
import { toast } from '@/components/custom-toast';
import { useTranslation } from 'react-i18next';
import { PhoneInput, defaultCountries } from 'react-international-phone';

type ModalKey = 'cases' | 'clients' | 'tasks';

interface CaseFormData {
  caseTypes: Array<{ id: number; name: string | Record<string, string> }>;
  caseStatuses: Array<{ id: number; name: string | Record<string, string> }>;
  clients: Array<{ id: number; name: string }>;
  courts: Array<{ id: number; name: string }>;
}

interface ClientFormData {
  clientTypes: Array<{ id: number; name: string | Record<string, string> }>;
  countries: Array<{ id: number; name: string | Record<string, string> }>;
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

export function GlobalQuickActionModals() {
  const { t, i18n } = useTranslation();
  const currentLocale = i18n.language || 'en';
  const axios = useAxios();

  const [activeModal, setActiveModal] = useState<ModalKey | null>(null);
  const [caseData, setCaseData] = useState<CaseFormData | null>(null);
  const [clientData, setClientData] = useState<ClientFormData | null>(null);
  const [taskData, setTaskData] = useState<TaskFormData | null>(null);
  const [isLoading, setIsLoading] = useState(false);

  const resolveName = (name: string | Record<string, string>) => {
    if (typeof name === 'string') {
      return name;
    }
    return name[currentLocale] || name.en || name.ar || '';
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
      }
    };

    window.addEventListener('quickAction:openModal', handleQuickAction);
    return () => {
      window.removeEventListener('quickAction:openModal', handleQuickAction);
    };
  }, [caseData, clientData, taskData, isLoading]);

  const caseFormConfig = useMemo(() => {
    if (!caseData) return null;
    return {
      fields: [
        { name: 'title', label: t('Case Title'), type: 'text', required: true },
        {
          name: 'client_id',
          label: t('Client'),
          type: 'select',
          required: true,
          options: caseData.clients.map((client) => ({
            value: client.id.toString(),
            label: client.name,
          })),
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
          name: 'court_id',
          label: t('Court'),
          type: 'select',
          required: true,
          options: caseData.courts.map((court) => ({
            value: court.id.toString(),
            label: court.name,
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
          name: 'attributes',
          label: t('Attributes'),
          type: 'radio',
          options: [
            { value: 'petitioner', label: t('Petitioner') },
            { value: 'respondent', label: t('Respondent') },
          ],
        },
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

    return {
      fields: [
        { name: 'name', label: t('Client Name'), type: 'text', required: true },
        {
          name: 'country_id',
          label: t('Phone Country'),
          type: 'text',
          defaultValue: clientData.defaultCountryId ? clientData.defaultCountryId.toString() : undefined,
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
                    handleChange('country_id', String(selectedCountry.value));
                  }
                }}
              />
            );
          },
        },
        { name: 'email', label: t('Email'), type: 'email', required: true },
        { name: 'password', label: t('Password'), type: 'password', required: true },
        {
          name: 'business_type',
          label: t('Business Type'),
          type: 'select',
          required: true,
          options: [
            { value: 'b2c', label: t('B2C') },
            { value: 'b2b', label: t('B2B') },
          ],
        },
        {
          name: 'client_type_id',
          label: t('Client Type'),
          type: 'select',
          options: clientData.clientTypes.map((type) => ({
            value: type.id.toString(),
            label: resolveName(type.name),
          })),
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
      ],
      modalSize: 'xl',
    };
  }, [clientData, t, currentLocale]);

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

  const handleSubmit = (routeName: string) => (formData: any) => {
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
        initialData={null}
        title={t('Add New Case')}
        mode="create"
      />

      <CrudFormModal
        isOpen={activeModal === 'clients'}
        onClose={closeModal}
        onSubmit={handleSubmit('clients.store')}
        formConfig={(clientFormConfig as any) || { fields: [], modalSize: 'xl' }}
        initialData={null}
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
    </>
  );
}
