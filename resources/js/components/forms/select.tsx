/**
 * Form select with built-in placeholder option.
 *
 * Use for dropdowns that need a "no selection" option (e.g. "Select Client").
 * The parent uses '' for "no selection"; the component uses an internal sentinel
 * value because Radix Select does not allow empty string on SelectItem.
 *
 * Self-contained: uses @radix-ui/react-select directly (no dependency on ui/select).
 */

import * as React from 'react'
import * as SelectPrimitive from '@radix-ui/react-select'
import { Check, ChevronDown, ChevronUp } from 'lucide-react'

import { Label } from '@/components/forms/label'
import { cn } from '@/lib/utils'

/** Sentinel value for the placeholder option (Radix forbids empty string on SelectItem). */
export const SELECT_PLACEHOLDER_VALUE = '__placeholder__'

const Root = SelectPrimitive.Root
const Value = SelectPrimitive.Value

const Trigger = React.forwardRef<
  React.ElementRef<typeof SelectPrimitive.Trigger>,
  React.ComponentPropsWithoutRef<typeof SelectPrimitive.Trigger>
>(({ className, children, ...props }, ref) => (
  <SelectPrimitive.Trigger
    ref={ref}
    className={cn(
      'flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&>span]:line-clamp-1',
      className
    )}
    {...props}
  >
    {children}
    <SelectPrimitive.Icon asChild>
      <ChevronDown className="h-4 w-4 shrink-0 opacity-50" />
    </SelectPrimitive.Icon>
  </SelectPrimitive.Trigger>
))
Trigger.displayName = SelectPrimitive.Trigger.displayName

const ScrollUpButton = React.forwardRef<
  React.ElementRef<typeof SelectPrimitive.ScrollUpButton>,
  React.ComponentPropsWithoutRef<typeof SelectPrimitive.ScrollUpButton>
>(({ className, ...props }, ref) => (
  <SelectPrimitive.ScrollUpButton
    ref={ref}
    className={cn('flex cursor-default items-center justify-center py-1', className)}
    {...props}
  >
    <ChevronUp className="h-4 w-4" />
  </SelectPrimitive.ScrollUpButton>
))

const ScrollDownButton = React.forwardRef<
  React.ElementRef<typeof SelectPrimitive.ScrollDownButton>,
  React.ComponentPropsWithoutRef<typeof SelectPrimitive.ScrollDownButton>
>(({ className, ...props }, ref) => (
  <SelectPrimitive.ScrollDownButton
    ref={ref}
    className={cn('flex cursor-default items-center justify-center py-1', className)}
    {...props}
  >
    <ChevronDown className="h-4 w-4" />
  </SelectPrimitive.ScrollDownButton>
))

const Content = React.forwardRef<
  React.ElementRef<typeof SelectPrimitive.Content>,
  React.ComponentPropsWithoutRef<typeof SelectPrimitive.Content>
>(({ className, children, position = 'popper', ...props }, ref) => (
  <SelectPrimitive.Portal>
    <SelectPrimitive.Content
      ref={ref}
      className={cn(
        'relative z-50 max-h-96 min-w-[8rem] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2',
        position === 'popper' &&
          'data-[side=bottom]:translate-y-1 data-[side=left]:-translate-x-1 data-[side=right]:translate-x-1 data-[side=top]:-translate-y-1',
        className
      )}
      position={position}
      {...props}
    >
      <ScrollUpButton />
      <SelectPrimitive.Viewport
        className={cn(
          'p-1',
          position === 'popper' &&
            'h-[var(--radix-select-trigger-height)] w-full min-w-[var(--radix-select-trigger-width)]'
        )}
      >
        {children}
      </SelectPrimitive.Viewport>
      <ScrollDownButton />
    </SelectPrimitive.Content>
  </SelectPrimitive.Portal>
))
Content.displayName = SelectPrimitive.Content.displayName

const Item = React.forwardRef<
  React.ElementRef<typeof SelectPrimitive.Item>,
  React.ComponentPropsWithoutRef<typeof SelectPrimitive.Item>
>(({ className, children, ...props }, ref) => (
  <SelectPrimitive.Item
    ref={ref}
    className={cn(
      'relative flex w-full cursor-default select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50',
      className
    )}
    {...props}
  >
    <span className="absolute left-2 flex h-3.5 w-3.5 items-center justify-center">
      <SelectPrimitive.ItemIndicator>
        <Check className="h-4 w-4" />
      </SelectPrimitive.ItemIndicator>
    </span>
    <SelectPrimitive.ItemText>{children}</SelectPrimitive.ItemText>
  </SelectPrimitive.Item>
))
Item.displayName = SelectPrimitive.Item.displayName

export interface SelectOption {
  id: string | number | null
  name: string
}

export interface SelectExtraOption {
  value: string
  label: string
}

/** Options as id => name map (e.g. { '': 'Select Client', '1': 'Name 1' }). */
export type SelectOptionsRecord = Record<string, string>

/** Option shape passed to renderOption (includes extra fields when option is an object). */
export type SelectOptionWithExtras = SelectOption & Record<string, unknown>

export interface SelectProps {
  value: string | null | undefined
  onValueChange: (value: string) => void
  placeholder: string
  /** Options: array of { id, name }, [id, name] tuple, or string; or record key = id, value = name. */
  options: (SelectOption | [string | number | null, string] | string)[] | SelectOptionsRecord
  extraOptions?: SelectExtraOption[]
  /** Custom render for each option (e.g. icon + label for currency SAR). Receives option with id, name, and any extra fields. */
  renderOption?: (option: SelectOptionWithExtras) => React.ReactNode
  label?: string
  required?: boolean
  disabled?: boolean
  triggerClassName?: string
  contentClassName?: string
}

/**
 * Select that supports a placeholder option (e.g. first option with id null from backend).
 * Normalizes value/onChange so the parent uses '' for "no selection".
 */
export function Select({
  value,
  onValueChange,
  placeholder,
  options,
  extraOptions,
  renderOption,
  label,
  required,
  disabled,
  triggerClassName,
  contentClassName,
}: SelectProps) {
  const dir = (typeof document !== 'undefined' && document.documentElement.getAttribute('dir') === 'rtl')
    ? 'rtl'
    : 'ltr'
  const normalizedValue = value != null && value !== '' ? String(value) : SELECT_PLACEHOLDER_VALUE
  const handleChange = (v: string) => onValueChange(v === SELECT_PLACEHOLDER_VALUE ? '' : v)

  const optionsArray = Array.isArray(options) ? options : Object.entries(options).map(([id, name]) => (id === '' ? [null, name] as [null, string] : [id, name]))

  return (
    <div className="space-y-2">
      {label != null && (
        <Label required={required}>{label}</Label>
      )}
      <Root value={normalizedValue} onValueChange={handleChange} disabled={disabled}>
        <Trigger dir={dir} className={triggerClassName}>
          <Value placeholder={placeholder} />
        </Trigger>
        <Content dir={dir} className={contentClassName}>
          {optionsArray.map((o) => {
            const id = typeof o === 'string' ? null : Array.isArray(o) ? (o[0] ?? null) : o.id
            const name = typeof o === 'string' ? o : Array.isArray(o) ? o[1] : o.name
            const isPlaceholder = id == null || id === ''
            const optionValue = isPlaceholder ? SELECT_PLACEHOLDER_VALUE : String(id)
            const optionObj: SelectOptionWithExtras = typeof o === 'string' ? { id: null, name: o } : Array.isArray(o) ? { id: o[0] ?? null, name: o[1] } : { ...o }
            return (
              <Item
                key={isPlaceholder ? 'placeholder' : String(id)}
                value={optionValue}
              >
                {renderOption ? renderOption(optionObj) : name}
              </Item>
            )
          })}
          {extraOptions?.map((opt) => (
            <Item key={opt.value} value={opt.value}>
              {opt.label}
            </Item>
          ))}
        </Content>
      </Root>
    </div>
  )
}
