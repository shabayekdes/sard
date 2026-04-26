import * as React from 'react';
import { router } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import { Badge } from '@/components/ui/badge';

export function resolveClientName(val: unknown, locale: string): string {
  if (val == null) return '';
  if (typeof val === 'string') return val;
  if (typeof val === 'object' && val !== null && ('en' in val || 'ar' in val)) {
    const o = val as Record<string, string>;
    return o[locale] || o.en || o.ar || '';
  }
  return String(val);
}

export type ClientTableCellClient = {
  id: number;
  name?: unknown;
  phone?: string | null;
  deleted_at?: string | null;
  /** b2c = individual (فرد), b2b = business (شركة / tax entity) */
  business_type?: 'b2c' | 'b2b' | null;
} | null | undefined;

type ClientTableCellProps = {
  client: ClientTableCellClient;
  locale: string;
  /** When there is no client, render this instead of "-" */
  fallback?: React.ReactNode;
};

export function ClientTableCell({ client, locale, fallback = '-' }: ClientTableCellProps) {
  const { t } = useTranslation();

  if (!client) {
    return <>{fallback}</>;
  }

  const clientName = resolveClientName(client.name, locale) || '-';
  const isClientDeleted = Boolean(client.deleted_at);
  const businessTypeLabel =
    client.business_type === 'b2b' || client.business_type === 'b2c'
      ? client.business_type === 'b2b'
        ? t('Business')
        : t('Individual')
      : null;

  const inner = (
    <>
      <span className="inline-flex min-w-0 flex-row flex-wrap items-center gap-1.5" dir="ltr">
        <span className="min-w-0 text-start">{clientName}</span>
        {isClientDeleted && (
          <Badge variant="outline" className="shrink-0 font-normal text-muted-foreground" dir="auto">
            {t('Deleted')}
          </Badge>
        )}
      </span>
      {businessTypeLabel ? (
        <span className="text-start text-sm text-gray-500 dark:text-gray-400" dir="auto">
          {businessTypeLabel}
        </span>
      ) : null}
      {client.phone ? (
        <span
          dir="ltr"
          className="inline-block max-w-full text-start text-sm tabular-nums text-gray-500 dark:text-gray-400"
        >
          {client.phone}
        </span>
      ) : null}
    </>
  );

  if (isClientDeleted) {
    return <div className="flex min-w-0 flex-col items-start gap-0.5 text-start text-muted-foreground">{inner}</div>;
  }

  return (
    <button
      type="button"
      onClick={() => router.get(route('clients.show', client.id))}
      className="flex min-w-0 flex-col items-start gap-0.5 text-start text-primary hover:text-primary/80 hover:underline focus:outline-none cursor-pointer"
    >
      {inner}
    </button>
  );
}
