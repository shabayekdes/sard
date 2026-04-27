import React, { useState, useEffect, useRef } from 'react';
import { useTranslation } from 'react-i18next';
import { Button } from '@/components/ui/button';
import MediaPicker from '@/components/MediaPicker';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Plus, Trash2, GripVertical } from 'lucide-react';
import { cn } from '@/lib/utils';
import { useLayout } from '@/contexts/LayoutContext';
import { GregorianHijriDateField } from '@/components/GregorianHijriDateField';
import { toDatetimeLocalInputValue } from '@/utils/datetimeLocal';

/** Tailwind `md:col-span-*` for stacked grid (12-column row); values must be literal for JIT. */
const STACKED_MD_COL_SPAN: Record<number, string> = {
    1: 'md:col-span-1',
    2: 'md:col-span-2',
    3: 'md:col-span-3',
    4: 'md:col-span-4',
    5: 'md:col-span-5',
    6: 'md:col-span-6',
    7: 'md:col-span-7',
    8: 'md:col-span-8',
    9: 'md:col-span-9',
    10: 'md:col-span-10',
    11: 'md:col-span-11',
    12: 'md:col-span-12',
};

function stackedColSpanClass(span: number | undefined): string | undefined {
    if (span == null || span < 1 || span > 12) return undefined;
    return STACKED_MD_COL_SPAN[span];
}

export type RepeaterCustomRenderArgs = {
  value: any;
  onChange: (value: any) => void;
  itemIndex: number;
  field: RepeaterField;
};

export interface RepeaterField {
  name: string;
  label: string;
  type:
    | 'text'
    | 'textarea'
    | 'number'
    | 'email'
    | 'password'
    | 'file'
    | 'media-picker'
    | 'select'
    | 'switch'
    | 'date'
    | 'time'
    | 'datetime-local'
    | 'custom';
  placeholder?: string;
  required?: boolean;
  options?: { value: string | number; label: string }[];
  accept?: string; // for file inputs
  min?: string | number;
  max?: string | number;
  step?: string | number;
  defaultValue?: any;
  className?: string;
  disabled?: boolean;
  /** When type is `custom`, render the cell content (presets, composed inputs, etc.). */
  render?: (args: RepeaterCustomRenderArgs) => React.ReactNode;
  /** Extra grid cell classes when `layout="stacked"` (e.g. `min-h-[5rem]`). */
  stackedCellClassName?: string;
  /**
   * When `layout="stacked"` and the grid uses 12 columns (`md:grid-cols-12`), how many columns this field spans from `md` up (1–12).
   * If omitted, `stackedFieldDefaultColSpan` on `Repeater` is used when set.
   */
  stackedColSpan?: number;
}

export type RepeaterLayout = 'table' | 'stacked';

export interface RepeaterProps {
  fields: RepeaterField[];
  value?: any[];
  onChange?: (value: any[]) => void;
  minItems?: number;
  maxItems?: number;
  addButtonText?: string;
  removeButtonText?: string;
  className?: string;
  itemClassName?: string;
  showItemNumbers?: boolean;
  allowReorder?: boolean;
  emptyMessage?: string;
  getFieldError?: (itemIndex: number, fieldName: string) => string | undefined;
  /**
   * `table` — horizontal table (default, best for few narrow columns).
   * `stacked` — one card per row with a responsive field grid and a label above each control (better for many/wide fields).
   */
  layout?: RepeaterLayout;
  /** Tailwind grid classes for the field grid when `layout="stacked"` (default: 1 → 2 → 3 → 4 columns by breakpoint). */
  stackedGridClassName?: string;
  /**
   * When `layout="stacked"`, default `md:col-span-*` (1–12) for fields that do not set `stackedColSpan`.
   * Use with e.g. `stackedGridClassName="… md:grid-cols-12"`.
   */
  stackedFieldDefaultColSpan?: number;
}

export function Repeater({
  fields,
  value = [],
  onChange,
  minItems = 0,
  maxItems = 10,
  addButtonText = 'Add Item',
  removeButtonText = 'Remove',
  className,
  itemClassName,
  showItemNumbers = true,
  allowReorder = false,
  emptyMessage = 'No items added yet.',
  getFieldError,
  layout = 'table',
  stackedGridClassName = 'grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4',
  stackedFieldDefaultColSpan,
}: RepeaterProps) {
  const { t } = useTranslation();
  const { isRtl } = useLayout();
  const [items, setItems] = useState<any[]>(value.length > 0 ? value : []);
  // Generate a unique ID for this repeater instance to ensure unique keys across multiple repeaters
  const repeaterIdRef = useRef(`repeater_${Math.random().toString(36).substr(2, 9)}_${Date.now()}`);

  useEffect(() => {
    if (value && JSON.stringify(value) !== JSON.stringify(items)) {
      setItems(value);
    }
  }, [value]);

  const createEmptyItem = () => {
    const emptyItem: any = {};
    fields.forEach(field => {
      emptyItem[field.name] = field.defaultValue || (field.type === 'switch' ? false : '');
    });
    return emptyItem;
  };

  const addItem = () => {
    if (maxItems === -1 || items.length < maxItems) {
      const newItems = [...items, createEmptyItem()];
      setItems(newItems);
      onChange?.(newItems);
    }
  };

  const removeItem = (index: number) => {
    if (items.length > minItems) {
      const newItems = items.filter((_, i) => i !== index);
      setItems(newItems);
      onChange?.(newItems);
    }
  };

  const updateItem = (index: number, fieldName: string, fieldValue: any) => {
    const newItems = [...items];
    newItems[index] = { ...newItems[index], [fieldName]: fieldValue };
    setItems(newItems);
    onChange?.(newItems);
  };

  const moveItem = (fromIndex: number, toIndex: number) => {
    if (!allowReorder) return;

    const newItems = [...items];
    const [movedItem] = newItems.splice(fromIndex, 1);
    newItems.splice(toIndex, 0, movedItem);
    setItems(newItems);
    onChange?.(newItems);
  };

  const renderField = (field: RepeaterField, value: any, onChange: (value: any) => void, itemIndex: number) => {
    const fieldId = `${field.name}_${itemIndex}`;
    const errorMessage = getFieldError?.(itemIndex, field.name);
    const errorClassName = errorMessage ? 'border-red-500 focus-visible:ring-red-500' : undefined;
    let fieldNode: React.ReactNode;

    switch (field.type) {
      case 'custom':
        fieldNode = field.render ? field.render({ value, onChange, itemIndex, field }) : null;
        break;

      case 'textarea':
        fieldNode = (
          <Textarea
            id={fieldId}
            placeholder={field.placeholder}
            value={value || ''}
            onChange={(e) => onChange(e.target.value)}
            required={field.required}
            disabled={field.disabled}
            className={cn(field.className, errorClassName)}
          />
        );
        break;

      case 'number':
        fieldNode = (
          <Input
            id={fieldId}
            type="number"
            placeholder={field.placeholder}
            value={value || ''}
            onChange={(e) => onChange(e.target.value)}
            required={field.required}
            disabled={field.disabled}
            min={field.min}
            max={field.max}
            step={field.step}
            className={cn(field.className, errorClassName)}
          />
        );
        break;

      case 'file':
        fieldNode = (
          <Input
            id={fieldId}
            type="file"
            accept={field.accept}
            onChange={(e) => onChange(e.target.files?.[0] || null)}
            required={field.required}
            disabled={field.disabled}
            className={cn(field.className, errorClassName)}
          />
        );
        break;

      case 'media-picker':
        fieldNode = (
          <MediaPicker
            value={value || ''}
            onChange={onChange}
            placeholder={field.placeholder ? t(field.placeholder) : t('Select {{label}}', { label: field.label })}
            showPreview={true}
            multiple={false}
          />
        );
        break;

      case 'select':
        {
          const selectId = `${repeaterIdRef.current}_${itemIndex}_${field.name}`;
          const seenValues = new Set<string>();
          const uniqueOptions = (field.options || []).filter((option) => {
            const v = String(option.value);
            if (seenValues.has(v)) return false;
            seenValues.add(v);
            return true;
          });

          fieldNode = (
            <Select
              value={value ? String(value) : ''}
              onValueChange={onChange}
              disabled={field.disabled}
            >
              <SelectTrigger className={cn(field.className, errorClassName)}>
                <SelectValue placeholder={field.placeholder} />
              </SelectTrigger>
              <SelectContent className="z-[9999]">
                {uniqueOptions.map((option, optionIndex) => (
                  <SelectItem
                    key={`${selectId}_${String(option.value)}_${optionIndex}`}
                    value={String(option.value)}
                  >
                    {option.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          );
        }
        break;

      case 'switch':
        fieldNode = (
          <div className="flex items-center space-x-2">
            <Switch
              id={fieldId}
              checked={Boolean(value)}
              onCheckedChange={onChange}
              disabled={field.disabled}
            />
            <Label htmlFor={fieldId} className="text-sm">
              {field.placeholder || 'Enable'}
            </Label>
          </div>
        );
        break;

      case 'date':
        fieldNode = (
          <GregorianHijriDateField
            id={fieldId}
            value={value || ''}
            onChange={onChange}
            mode="date"
            required={field.required}
            disabled={field.disabled}
            min={field.min != null ? String(field.min) : undefined}
            max={field.max != null ? String(field.max) : undefined}
            className={field.className}
            error={Boolean(errorMessage)}
            helperText={errorMessage || undefined}
          />
        );
        break;

      case 'time':
        fieldNode = (
          <Input
            id={fieldId}
            type={field.type}
            value={value || ''}
            onChange={(e) => onChange(e.target.value)}
            required={field.required}
            disabled={field.disabled}
            min={field.min}
            max={field.max}
            className={cn(field.className, errorClassName)}
          />
        );
        break;

      case 'datetime-local':
        fieldNode = (
          <GregorianHijriDateField
            id={fieldId}
            value={toDatetimeLocalInputValue(value)}
            onChange={onChange}
            mode="datetime-local"
            required={field.required}
            disabled={field.disabled}
            min={field.min != null ? String(field.min) : undefined}
            max={field.max != null ? String(field.max) : undefined}
            className={field.className}
            error={Boolean(errorMessage)}
            helperText={errorMessage || undefined}
          />
        );
        break;

      default: // text, email, password
        fieldNode = (
          <Input
            id={fieldId}
            type={field.type}
            placeholder={field.placeholder}
            value={value || ''}
            onChange={(e) => onChange(e.target.value)}
            required={field.required}
            disabled={field.disabled}
            className={cn(field.className, errorClassName)}
          />
        );
        break;
    }

    return (
      <div className="space-y-1">
        {fieldNode}
        {errorMessage && <p className="text-xs text-red-600">{errorMessage}</p>}
      </div>
    );
  };

  return (
    <div className={cn('space-y-4', className)}>
      {items.length === 0 ? (
        <div className="space-y-3">
          <div className="text-center py-8 text-gray-500 dark:text-gray-400 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-lg">
            {t(emptyMessage)}
          </div>
          {(maxItems === -1 || items.length < maxItems) && (
            <div className="flex items-center justify-between">
              <Button
                type="button"
                variant="outline"
                onClick={addItem}
                className="border-dashed border-2 hover:border-primary hover:bg-primary/5 dark:border-gray-700 dark:hover:border-primary dark:text-gray-200"
              >
                <Plus className="h-4 w-4 mr-2" />
                {addButtonText}
              </Button>
            </div>
          )}
        </div>
      ) : (
        <div className="space-y-3">
          {layout === 'stacked' ? (
            <>
              {items.map((item, index) => (
                <div
                  key={index}
                  className={cn(
                    'rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800/50',
                    itemClassName,
                  )}
                >
                  {/* dir=ltr so remove stays physically left and # on the right under page RTL */}
                  <div dir="ltr" className="mb-4 flex w-full flex-wrap items-center justify-between gap-2">
                    {items.length > minItems ? (
                      <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        onClick={() => removeItem(index)}
                        className="inline-flex gap-2 text-red-600 hover:bg-red-50 hover:text-red-700 dark:border-gray-600 dark:text-red-400 dark:hover:bg-red-900/20 dark:hover:text-red-300"
                      >
                        <Trash2 className="h-4 w-4 shrink-0" />
                        {removeButtonText}
                      </Button>
                    ) : (
                      <span />
                    )}
                    {showItemNumbers ? (
                      <span className="text-sm font-semibold text-gray-800 dark:text-gray-200">#{index + 1}</span>
                    ) : (
                      <span />
                    )}
                  </div>
                  <div className={cn(stackedGridClassName)}>
                    {fields.map((field) => {
                      const fieldId = `${field.name}_${index}`;
                      const colSpan = field.stackedColSpan ?? stackedFieldDefaultColSpan;
                      return (
                        <div
                          key={field.name}
                          dir={isRtl ? 'rtl' : 'ltr'}
                          className={cn(
                            'min-w-0 space-y-1.5',
                            stackedColSpanClass(colSpan),
                            field.stackedCellClassName,
                          )}
                        >
                          <Label
                            htmlFor={fieldId}
                            className={cn(
                              'block text-xs font-medium text-gray-600 dark:text-gray-300',
                              isRtl && 'text-right',
                            )}
                          >
                            {field.label}
                            {field.required ? <span className="text-red-500 dark:text-red-400"> *</span> : null}
                          </Label>
                          {renderField(
                            field,
                            item[field.name],
                            (v) => updateItem(index, field.name, v),
                            index,
                          )}
                        </div>
                      );
                    })}
                  </div>
                </div>
              ))}
            </>
          ) : (
            <div className="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead className="bg-gray-50 dark:bg-gray-800">
                <tr>
                  {allowReorder && <th className="w-10 px-3 py-3"></th>}
                  {showItemNumbers && (
                    <th className="w-12 px-3 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300">#</th>
                  )}
                  {fields.map((field) => (
                    <th
                      key={field.name}
                      className="px-3 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300"
                    >
                      {field.label}
                      {field.required && <span className="text-red-500 dark:text-red-400 ml-1">*</span>}
                    </th>
                  ))}
                  <th className="w-24 px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                    {removeButtonText}
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                {items.map((item, index) => (
                  <tr key={index} className={cn(itemClassName)}>
                    {allowReorder && (
                      <td className="px-3 py-3 align-top text-gray-400">
                        <GripVertical className="h-4 w-4" />
                      </td>
                    )}
                    {showItemNumbers && (
                      <td className="px-3 py-3 align-top text-sm text-gray-600 dark:text-gray-300">{index + 1}</td>
                    )}
                    {fields.map((field) => (
                      <td key={field.name} className="px-3 py-3 align-top">
                        {renderField(
                          field,
                          item[field.name],
                          (value) => updateItem(index, field.name, value),
                          index
                        )}
                      </td>
                    ))}
                    <td className="px-3 py-3 align-top">
                      {items.length > minItems && (
                        <Button
                          type="button"
                          variant="outline"
                          size="sm"
                          onClick={() => removeItem(index)}
                          className="text-red-600 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/20 dark:border-gray-600"
                        >
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          )}
          {(maxItems === -1 || items.length < maxItems) && (
            <div className="flex items-center justify-between">
              <Button
                type="button"
                variant="outline"
                onClick={addItem}
                className="border-dashed border-2 hover:border-primary hover:bg-primary/5 dark:border-gray-700 dark:hover:border-primary dark:text-gray-200"
              >
                <Plus className="h-4 w-4 mr-2" />
                {addButtonText}
              </Button>
            </div>
          )}
        </div>
      )}
    </div>
  );
}