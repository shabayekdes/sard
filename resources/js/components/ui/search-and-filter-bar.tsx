import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DatePicker } from '@/components/ui/date-picker';
import { Filter, Search, List, LayoutGrid } from 'lucide-react';
import { cn } from '@/lib/utils';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';

interface FilterOption {
  name: string;
  label: string;
  type: 'select' | 'date';
  options?: { value: string; label: string }[];
  value: string | Date | undefined;
  onChange: (value: any) => void;
}

interface SearchAndFilterBarProps {
  searchTerm: string;
  onSearchChange: (value: string) => void;
  onSearch: (e: React.FormEvent) => void;
  filters?: FilterOption[];
  showFilters: boolean;
  setShowFilters: (show: boolean) => void;
  hasActiveFilters: () => boolean;
  activeFilterCount: () => number;
  onResetFilters: () => void;
  onApplyFilters?: () => void;
  currentPerPage?: string;
  onPerPageChange?: (value: string) => void;
  // View toggle props
  showViewToggle?: boolean;
  activeView?: 'list' | 'grid';
  onViewChange?: (view: 'list' | 'grid') => void;
}

export function SearchAndFilterBar({
  searchTerm,
  onSearchChange,
  onSearch,
  filters = [],
  showFilters,
  setShowFilters,
  hasActiveFilters,
  activeFilterCount,
  onResetFilters,
  onApplyFilters,
  // View toggle props
  showViewToggle = false,
  activeView = 'list',
  onViewChange,
}: SearchAndFilterBarProps) {
  const { t } = useTranslation();
  return (
    <div className="w-full">
      <div className="flex flex-wrap items-center gap-2">
        <div className="flex min-w-0 flex-1 items-center gap-2">
          <form onSubmit={onSearch} className="flex min-w-0 flex-1 gap-2">
            <div className="relative min-w-0 flex-1 sm:w-64 sm:flex-none">
              <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
              <Input
                placeholder={t("Search...")}
                value={searchTerm}
                onChange={(e) => onSearchChange(e.target.value)}
                className="w-full pl-9"
              />
            </div>
            <Button type="submit" size="sm" className="w-9 px-0 sm:w-auto sm:px-3">
              <Search className="h-4 w-4 sm:mr-1.5" />
              <span className="sr-only sm:not-sr-only">{t("Search")}</span>
            </Button>
          </form>
        </div>

        <div className="flex items-center gap-2 whitespace-nowrap">
          {showViewToggle && onViewChange && (
            <div className="mr-2 rounded-md border p-0.5">
              <Button
                size="sm"
                variant={activeView === 'list' ? "default" : "ghost"}
                className="h-7 px-2"
                onClick={() => onViewChange('list')}
              >
                <List className="h-4 w-4" />
              </Button>
              <Button
                size="sm"
                variant={activeView === 'grid' ? "default" : "ghost"}
                className="h-7 px-2"
                onClick={() => onViewChange('grid')}
              >
                <LayoutGrid className="h-4 w-4" />
              </Button>
            </div>
          )}

          {filters.length > 0 && (
            <Button
              variant="outline"
              size="sm"
              className="relative h-9 gap-1.5 rounded-lg px-3"
              onClick={() => setShowFilters(!showFilters)}
            >
              <Filter className="h-3.5 w-3.5" />
              <span className="hidden sm:inline">{showFilters ? t('Hide Filters') : t('Filters')}</span>
              {hasActiveFilters() && (
                <span className="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-primary-foreground text-[10px] text-primary">
                  {activeFilterCount()}
                </span>
              )}
            </Button>
          )}
        </div>
      </div>

      {showFilters && filters.length > 0 && (
        <div className="mt-3 w-full rounded-xl bg-white p-4 dark:bg-gray-900">
          <div className="flex flex-wrap gap-4 items-end">
            {filters.map((filter) => (
              <div key={filter.name} className="space-y-2">
                <Label>{filter.label}</Label>
                {filter.type === 'select' && filter.options && (
                  <Select
                    value={filter.value as string}
                    onValueChange={filter.onChange}
                  >
                    <SelectTrigger className="w-40">
                      <SelectValue placeholder={t(`All ${filter.label}`)} />
                    </SelectTrigger>
                    <SelectContent>
                      {filter.options.map((option) => (
                        <SelectItem key={option.value} value={option.value}>
                          {option.label}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                )}
                {filter.type === 'date' && (
                  <DatePicker
                    selected={filter.value as Date | undefined}
                    onSelect={filter.onChange}
                    onChange={filter.onChange}
                  />
                )}
              </div>
            ))}

            <div className="flex gap-2">
              {onApplyFilters && (
                <Button
                  variant="default"
                  size="sm"
                  className="h-9"
                  onClick={onApplyFilters}
                >
                  {t("Apply Filters")}
                </Button>
              )}

              <Button
                variant="outline"
                size="sm"
                className="h-9"
                onClick={onResetFilters}
                disabled={!hasActiveFilters()}
              >
                {t("Reset Filters")}
              </Button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}