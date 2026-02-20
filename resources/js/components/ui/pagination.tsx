/**
 * Pagination component with dark mode support
 */
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { cn } from '@/lib/utils';
import { useTranslation } from 'react-i18next';

interface PaginationProps {
  from?: number;
  to?: number;
  total?: number;
  links?: any[];
  currentPage?: number;
  lastPage?: number;
  entityName?: string;
  onPageChange?: (url: string) => void;
  perPage?: string | number;
  currentPerPage?: string | number;
  perPageOptions?: number[];
  onPerPageChange?: (value: string) => void;
  className?: string;
}

export function Pagination({
  from = 0,
  to = 0,
  total = 0,
  links = [],
  currentPage,
  lastPage,
  entityName = 'items',
  onPageChange,
  perPage,
  currentPerPage,
  perPageOptions = [10, 25, 50, 100],
  onPerPageChange,
  className = '',
}: PaginationProps) {
  const effectivePerPage = perPage ?? currentPerPage ?? perPageOptions[0];
  const { t } = useTranslation();
  const isRtl = document.documentElement.dir === 'rtl' || document.body?.dir === 'rtl';

  const handlePageChange = (url: string) => {
    if (onPageChange) {
      onPageChange(url);
    } else if (url) {
      window.location.href = url;
    }
  };

  return (
    <div className={cn(
      "p-4 border-t dark:border-gray-700 flex flex-col gap-4 items-center sm:flex-row sm:items-center sm:justify-between sm:gap-0 dark:bg-gray-900",
      className
    )}>
      {/* Pagination buttons — on mobile: row 1 centered; on sm+: left side */}
      <div className="flex gap-1 justify-center sm:justify-start">
        {links && links.length > 0 ? (
          links.map((link: any, i: number) => {
            // Check if the link is "Next" or "Previous" to use text instead of icon
            const isTextLink = link.label === "&laquo; Previous" || link.label === "Next &raquo;";
            const rawLabel = link.label.replace("&laquo; ", "").replace(" &raquo;", "");
            const label =
              rawLabel === "Previous"
                ? t("Previous", { defaultValue: "السابق" })
                : rawLabel === "Next"
                  ? t("Next", { defaultValue: "التالي" })
                  : rawLabel;

            return (
              <Button
                key={`pagination-${i}-${link.label}`}
                variant={link.active ? 'default' : 'outline'}
                size={isTextLink ? "sm" : "icon"}
                className={isTextLink ? "px-3" : "h-8 w-8"}
                disabled={!link.url}
                onClick={() => link.url && handlePageChange(link.url)}
              >
                {isTextLink ? label : <span dangerouslySetInnerHTML={{ __html: link.label }} />}
              </Button>
            );
          })
        ) : (
          // Simple pagination if links are not available
          currentPage && lastPage && lastPage > 1 && (
            <>
              <Button
                variant="outline"
                size="sm"
                disabled={currentPage <= 1}
                onClick={() => handlePageChange(`?page=${currentPage - 1}`)}
              >
                {t("Previous", { defaultValue: "السابق" })}
              </Button>
              <span className="px-3 py-1 dark:text-white">
                {currentPage} {t("of")} {lastPage}
              </span>
              <Button
                variant="outline"
                size="sm"
                disabled={currentPage >= lastPage}
                onClick={() => handlePageChange(`?page=${currentPage + 1}`)}
              >
                {t("Next", { defaultValue: "التالي" })}
              </Button>
            </>
          )
        )}
      </div>
      {/* "Showing X to Y of Z" + per page — on mobile: row 2 centered; on sm+: right side */}
      <div className="flex flex-wrap items-center justify-center gap-x-3 gap-y-2 sm:flex-nowrap sm:justify-end">
        <span className="text-sm text-muted-foreground dark:text-gray-300 min-w-0">
          {t("Showing")} <span className="font-medium dark:text-white">{from}</span> {t("to")}{" "}
          <span className="font-medium dark:text-white">{to}</span> {t("of")}{" "}
          <span className="font-medium dark:text-white">{total}</span> {entityName}
        </span>
        {onPerPageChange && (
          <div className="flex items-center gap-2">
            <Label className="text-xs text-muted-foreground dark:text-gray-300 shrink-0">
              {t("Per Page", { defaultValue: "لكل صفحة" })}
            </Label>
            <Select value={String(effectivePerPage)} onValueChange={onPerPageChange}>
              <SelectTrigger className="h-8 w-16">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                {perPageOptions.map((option) => (
                  <SelectItem key={option} value={option.toString()}>
                    {option}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        )}
      </div>
    </div>
  );
}