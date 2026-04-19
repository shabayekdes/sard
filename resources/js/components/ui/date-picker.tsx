"use client"

import { format } from "date-fns"

import { GregorianHijriDateField } from "@/components/GregorianHijriDateField"

interface DatePickerProps {
  selected?: Date
  onSelect?: (date: Date | undefined) => void
  onChange?: (date: Date | undefined) => void
  placeholder?: string
  disabled?: boolean
}

function ymdToLocalDate(ymd: string): Date | undefined {
  if (!ymd) return undefined
  const m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(ymd.trim())
  if (!m) return undefined
  const y = parseInt(m[1]!, 10)
  const mo = parseInt(m[2]!, 10) - 1
  const d = parseInt(m[3]!, 10)
  const dt = new Date(y, mo, d)
  if (dt.getFullYear() !== y || dt.getMonth() !== mo || dt.getDate() !== d) return undefined
  return dt
}

export function DatePicker({
  selected,
  onSelect,
  onChange,
  placeholder = "Pick a date",
  disabled = false,
}: DatePickerProps) {
  const ymd = selected ? format(selected, 'yyyy-MM-dd') : ''

  const emit = (date: Date | undefined) => {
    if (onSelect) onSelect(date)
    if (onChange) onChange(date)
  }

  const handleGregorianChange = (g: string) => {
    if (!g) {
      emit(undefined)
      return
    }
    const d = ymdToLocalDate(g)
    emit(d)
  }

  return (
    <div className="w-full max-w-md">
      <GregorianHijriDateField
        value={ymd}
        onChange={handleGregorianChange}
        mode="date"
        disabled={disabled}
      />
      {!selected && placeholder && (
        <span className="sr-only">{placeholder}</span>
      )}
    </div>
  )
}
