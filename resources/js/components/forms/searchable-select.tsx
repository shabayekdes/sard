import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { Check, ChevronDown } from 'lucide-react';
import { useMemo, useState } from 'react';

export interface SearchableSelectOption {
    id: string;
    name: string;
}

export interface SearchableSelectProps {
    value: string;
    onValueChange: (value: string) => void;
    options: SearchableSelectOption[];
    placeholder: string;
    searchPlaceholder: string;
    emptyMessage?: string;
    disabled?: boolean;
    className?: string;
    error?: string;
    /** e.g. false when first item is a placeholder (id '', name 'Select...') */
    showPlaceholderInList?: boolean;
}

/**
 * ID + label select with a filter box (searchable) for long option lists.
 */
export function SearchableSelect({
    value,
    onValueChange,
    options,
    placeholder,
    searchPlaceholder,
    emptyMessage = 'No results',
    disabled = false,
    className,
    error,
    showPlaceholderInList = false,
}: SearchableSelectProps) {
    const [open, setOpen] = useState(false);
    const [q, setQ] = useState('');

    const dataOptions = useMemo(
        () =>
            showPlaceholderInList
                ? options
                : options.filter((o) => o.id !== ''),
        [options, showPlaceholderInList],
    );

    const filtered = useMemo(() => {
        const t = q.trim().toLowerCase();
        if (!t) return dataOptions;
        return dataOptions.filter((o) => o.name.toLowerCase().includes(t));
    }, [dataOptions, q]);

    const selected = options.find((o) => o.id === value);
    const label = selected && selected.id !== '' ? selected.name : null;

    return (
        <div className={cn('space-y-1', className)}>
            <Popover
                open={open}
                onOpenChange={(o) => {
                    setOpen(o);
                    if (!o) setQ('');
                }}
            >
                <PopoverTrigger asChild>
                    <Button
                        type="button"
                        variant="outline"
                        role="combobox"
                        aria-expanded={open}
                        disabled={disabled}
                        className={cn(
                            'h-10 w-full justify-between font-normal',
                            !label && 'text-muted-foreground',
                            error && 'border-destructive',
                        )}
                    >
                        <span className="truncate text-left">{label || placeholder}</span>
                        <ChevronDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                    </Button>
                </PopoverTrigger>
                <PopoverContent className="w-full min-w-[220px] max-w-lg p-0" align="start">
                    <div className="border-b p-2">
                        <Input
                            value={q}
                            onChange={(e) => setQ(e.target.value)}
                            placeholder={searchPlaceholder}
                            className="h-9"
                            autoFocus
                        />
                    </div>
                    <ul className="max-h-60 overflow-y-auto p-1" role="listbox">
                        {dataOptions.length === 0 || filtered.length === 0 ? (
                            <li className="px-2 py-3 text-center text-sm text-muted-foreground">{emptyMessage}</li>
                        ) : (
                            filtered.map((o) => {
                                const isSelected = o.id === value;
                                return (
                                    <li key={o.id || 'empty'}>
                                        <button
                                            type="button"
                                            className={cn(
                                                'flex w-full items-center justify-between gap-2 rounded-sm px-2 py-1.5 text-left text-sm outline-none hover:bg-accent',
                                                isSelected && 'bg-accent/70',
                                            )}
                                            onClick={() => {
                                                onValueChange(o.id);
                                                setOpen(false);
                                            }}
                                        >
                                            <span className="min-w-0 flex-1 truncate">{o.name}</span>
                                            {isSelected && <Check className="h-4 w-4 shrink-0" />}
                                        </button>
                                    </li>
                                );
                            })
                        )}
                    </ul>
                </PopoverContent>
            </Popover>
            {error ? <p className="text-sm text-destructive">{error}</p> : null}
        </div>
    );
}
