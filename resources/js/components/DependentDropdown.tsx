import { useLayout } from '@/contexts/LayoutContext';
import { usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

interface DropdownField {
    name: string;
    label: string;
    options?: { value: string; label: string }[];
    dependencies?: Record<string, { value: string; label: string }[]>;
    apiEndpoint?: string;
}

interface DependentDropdownProps {
    fields: DropdownField[];
    values: Record<string, string>;
    onChange: (fieldName: string, value: string, formData?: any, additionalData?: any) => void;
    disabled?: boolean;
    errors?: Record<string, string>;
    /** Layout: 'row' = fields in one row (grid), 'column' = stacked vertically (default) */
    layout?: 'row' | 'column';
}

export default function DependentDropdown({ fields, values, onChange, disabled = false, errors = {}, layout = 'column' }: DependentDropdownProps) {
    const { t } = useTranslation();
    const { isRtl } = useLayout();
    const { base_url } = usePage().props as any;
    const fieldDir = isRtl ? 'rtl' : 'ltr';

    const [availableOptions, setAvailableOptions] = useState<Record<string, { value: string; label: string }[]>>(() => {
        const initial: Record<string, { value: string; label: string }[]> = {};
        fields.forEach((field, index) => {
            if (index === 0) {
                initial[field.name] = field.options || [];
            } else {
                initial[field.name] = [];
            }
        });
        return initial;
    });
    const [loading, setLoading] = useState<Record<string, boolean>>({});

    // Load options from API
    const loadOptionsFromAPI = async (field: DropdownField, parentValue?: string) => {
        if (!field.apiEndpoint) return [];

        setLoading((prev) => ({ ...prev, [field.name]: true }));

        try {
            let endpoint = field.apiEndpoint;
            if (parentValue) {
                // Replace placeholder with actual value - sanitize parentValue
                const sanitizedValue = encodeURIComponent(String(parentValue));
                const parentFieldName = fields[fields.indexOf(field) - 1]?.name;
                if (parentFieldName) {
                    endpoint = endpoint.replace(`{${parentFieldName}}`, sanitizedValue);
                }
            }

            // Loading options from API endpoint
            const response = await fetch(`${base_url}${endpoint}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            // Transform API response to options format
            const options = Array.isArray(data)
                ? data.map((item) => ({
                      value: String(item.id || item.value || ''),
                      label: String(item.name || item.label || 'Unknown'),
                  }))
                : [];

            return options;
        } catch (error) {
            // Silent error handling - don't log user input
            return [];
        } finally {
            setLoading((prev) => ({ ...prev, [field.name]: false }));
        }
    };

    // Initialize available options for dependent fields
    useEffect(() => {
        const loadAllOptions = async () => {
            const newAvailableOptions: Record<string, { value: string; label: string }[]> = {};

            for (let index = 0; index < fields.length; index++) {
                const field = fields[index];

                if (index === 0) {
                    // First field - load from API or use static options
                    if (field.apiEndpoint) {
                        newAvailableOptions[field.name] = await loadOptionsFromAPI(field);
                    } else {
                        newAvailableOptions[field.name] = field.options || [];
                    }
                } else {
                    // Dependent fields need parent value
                    const parentField = fields[index - 1];
                    const parentValue = values[parentField.name];

                    if (parentValue) {
                        if (field.apiEndpoint) {
                            newAvailableOptions[field.name] = await loadOptionsFromAPI(field, parentValue);
                        } else if (field.dependencies) {
                            newAvailableOptions[field.name] = field.dependencies[parentValue] || [];
                        } else {
                            newAvailableOptions[field.name] = [];
                        }
                    } else {
                        newAvailableOptions[field.name] = [];
                    }
                }
            }

            setAvailableOptions(newAvailableOptions);
        };

        loadAllOptions();
    }, [fields]);

    const handleFieldChange = async (fieldName: string, value: string, fieldIndex: number) => {
        // Clear all dependent fields when parent changes
        fields.slice(fieldIndex + 1).forEach((dependentField) => {
            onChange(dependentField.name, '');
        });

        // Get selected field info for the callback
        const currentField = fields.find((f) => f.name === fieldName);
        const currentFieldOptions = availableOptions[fieldName] || currentField?.options || [];
        const selectedItem = currentFieldOptions.find((opt) => String(opt.value) === String(value));
        const selectedInfo = selectedItem ? { id: value, name: selectedItem.label } : null;
        
        // Load options for the next dependent field immediately
        const nextField = fields[fieldIndex + 1];
        if (nextField && value) {
            if (nextField.apiEndpoint) {
                const options = await loadOptionsFromAPI(nextField, value);
                setAvailableOptions((prev) => ({
                    ...prev,
                    [nextField.name]: options,
                }));

                // Pass selected info with loaded options
                onChange(fieldName, value, { selectedInfo, loadedOptions: options });
                return;
            } else if (nextField.dependencies) {
                const dependentOptions = nextField.dependencies[value] || [];
                setAvailableOptions((prev) => ({
                    ...prev,
                    [nextField.name]: dependentOptions,
                }));

                // Pass selected info with dependent options
                onChange(fieldName, value, { selectedInfo, loadedOptions: dependentOptions });
                return;
            }
        }

        // Update the field value with selected info (for fields without dependents)
        onChange(fieldName, value, { selectedInfo, loadedOptions: [] });
    };

    const gridCols = layout === 'row'
        ? (fields.length <= 2 ? 'grid-cols-2' : fields.length <= 3 ? 'grid-cols-3' : fields.length <= 4 ? 'grid-cols-4' : 'grid-cols-5')
        : 'grid-cols-1';
    const containerClass = layout === 'row'
        ? cn('grid w-full gap-4', gridCols)
        : 'space-y-4';

    return (
        <div className={containerClass}>
            {fields.map((field, index) => {
                const isFirstField = index === 0;
                const parentField = isFirstField ? null : fields[index - 1];
                const isDisabled = disabled || (!isFirstField && !values[parentField!.name]);
                const fieldOptions = availableOptions[field.name] || [];
                const isLoading = loading[field.name];
                
                return (
                    <div key={field.name} className="space-y-2">
                        <label className="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">{field.label}</label>
                        <select
                            value={values[field.name] || ''}
                            onChange={(e) => handleFieldChange(field.name, e.target.value, index)}
                            disabled={isDisabled || isLoading}
                            dir={fieldDir}
                            className={`flex h-10 w-full items-center justify-between rounded-md border bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 ${errors[field.name] ? 'border-red-500' : 'border-input'}`}
                        >
                            <option 
                                value=""
                                className="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none text-muted-foreground"
                            >
                                {isLoading ? t('Loading...') : t('Select {{label}}', { label: field.label })}
                            </option>
                            {fieldOptions.map((option, optionIndex) => (
                                <option 
                                    key={`${field.name}-${option.value}-${optionIndex}`} 
                                    value={option.value}
                                    className="relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50"
                                >
                                    {option.label}
                                </option>
                            ))}
                        </select>
                        {errors[field.name] && (
                            <p className="text-sm text-red-500 mt-1">{errors[field.name]}</p>
                        )}
                    </div>
                );
            })}
        </div>
    );
}
