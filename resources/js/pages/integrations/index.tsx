import { ConfirmDialog } from '@/components/ConfirmDialog';
import { PageTemplate } from '@/components/page-template';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Link, router, usePage } from '@inertiajs/react';
import type { PageProps } from '@/types/page-props';
import { hasAnyPermission } from '@/utils/permissions';
import axios from 'axios';
import { Loader2, Settings } from 'lucide-react';
import { useCallback, useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { toast } from 'sonner';

type GoogleCalendarListItem = {
    id: string;
    summary: string;
    primary: boolean;
};

export default function IntegrationsIndex() {
    const { t } = useTranslation();
    const { googleCalendarEnabled = false, googleCalendarId = '', flash } = usePage<PageProps>().props;
    const canManageGoogleCalendar = hasAnyPermission(['manage-google-calendar-integration']);
    const [disconnectDialogOpen, setDisconnectDialogOpen] = useState(false);
    const [calendarSettingsOpen, setCalendarSettingsOpen] = useState(false);
    const [calendars, setCalendars] = useState<GoogleCalendarListItem[]>([]);
    const [calendarsLoading, setCalendarsLoading] = useState(false);
    const [calendarsError, setCalendarsError] = useState<string | null>(null);
    const [selectedCalendarId, setSelectedCalendarId] = useState('');

    useEffect(() => {
        if (flash?.success) {
            toast.success(flash.success);
        }
    }, [flash?.success]);

    useEffect(() => {
        if (flash?.error) {
            toast.error(flash.error);
        }
    }, [flash?.error]);

    useEffect(() => {
        if (!calendarSettingsOpen || !googleCalendarEnabled || !canManageGoogleCalendar) {
            return;
        }

        setSelectedCalendarId(googleCalendarId ?? '');
        setCalendarsError(null);
        setCalendars([]);

        let cancelled = false;
        setCalendarsLoading(true);

        void axios
            .get<{ success: boolean; calendars?: GoogleCalendarListItem[]; message?: string }>(
                route('google-calendar.calendars'),
            )
            .then(({ data }) => {
                if (cancelled) {
                    return;
                }
                if (data.success && data.calendars) {
                    setCalendars(data.calendars);
                    setSelectedCalendarId((prev) => {
                        if (prev && data.calendars!.some((c) => c.id === prev)) {
                            return prev;
                        }
                        const primary = data.calendars!.find((c) => c.primary);
                        return primary?.id ?? data.calendars![0]?.id ?? '';
                    });
                } else {
                    setCalendarsError(data.message ?? t('Failed to load Google calendars'));
                }
            })
            .catch(() => {
                if (!cancelled) {
                    setCalendarsError(t('Failed to load Google calendars'));
                }
            })
            .finally(() => {
                if (!cancelled) {
                    setCalendarsLoading(false);
                }
            });

        return () => {
            cancelled = true;
        };
    }, [calendarSettingsOpen, googleCalendarEnabled, googleCalendarId, canManageGoogleCalendar, t]);

    const handleDisconnectConfirm = useCallback(() => {
        setDisconnectDialogOpen(false);
        router.post(route('integrations.google-calendar.disconnect'), {}, { preserveScroll: true });
    }, []);

    const handleSaveCalendarSelection = useCallback(() => {
        if (!selectedCalendarId) {
            toast.error(t('Select a calendar'));
            return;
        }
        router.post(
            route('integrations.google-calendar.calendar'),
            { google_calendar_id: selectedCalendarId },
            { preserveScroll: true, onSuccess: () => setCalendarSettingsOpen(false) },
        );
    }, [selectedCalendarId, t]);

    const authUrl = route('google.redirect');

    const breadcrumbs = [
        { title: t('Dashboard'), href: route('dashboard') },
        { title: t('Electronic Integrations') },
    ];

    return (
        <PageTemplate
            title={t('Electronic Integrations')}
            url="/integrations"
            breadcrumbs={breadcrumbs}
            noPadding
        >
            <div className="grid grid-cols-12 gap-6">
                <Card className="col-span-12 overflow-hidden border border-border/60 shadow-md lg:col-span-6">
                    <CardContent className="flex flex-col gap-5 p-5">
                        <div className="flex items-start gap-4">
                            <div className="shrink-0">
                                <img
                                    src="/images/google-calendar.svg"
                                    alt=""
                                    className="h-20 w-20 object-contain sm:h-24 sm:w-24"
                                    width={96}
                                    height={96}
                                />
                            </div>
                            <div className="min-w-0 flex-1 space-y-2 text-start">
                                <div className="flex w-full flex-wrap items-center justify-between gap-2">
                                    <h2 className="min-w-0 flex-1 text-base font-semibold text-foreground">
                                        {t('Google Calendar')}
                                    </h2>
                                    <Badge
                                        variant={googleCalendarEnabled ? 'success' : 'destructive'}
                                        className={
                                            !googleCalendarEnabled
                                                ? 'shrink-0 bg-rose-100 text-rose-800 hover:bg-rose-100 dark:bg-rose-950/50 dark:text-rose-200'
                                                : 'shrink-0'
                                        }
                                    >
                                        {googleCalendarEnabled ? t('Connected') : t('Disconnected')}
                                    </Badge>
                                </div>
                                <p className="text-sm leading-relaxed text-muted-foreground">
                                    {t('Google Calendar integration card description')}
                                </p>
                            </div>
                        </div>
                        {googleCalendarEnabled ? (
                            <>
                                {canManageGoogleCalendar ? (
                                    <div className="flex flex-wrap gap-2">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            className="w-fit border-destructive/50 text-destructive hover:bg-destructive/10 hover:text-destructive"
                                            onClick={() => setDisconnectDialogOpen(true)}
                                        >
                                            {t('Disconnect')}
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="outline"
                                            size="icon"
                                            className="shrink-0"
                                            aria-label={t('Google Calendar integration settings')}
                                            title={t('Google Calendar integration settings')}
                                            onClick={() => setCalendarSettingsOpen(true)}
                                        >
                                            <Settings className="h-4 w-4" />
                                        </Button>
                                    </div>
                                ) : (
                                    <p className="text-sm text-muted-foreground">
                                        {t(
                                            'You can view integration status but do not have permission to connect, disconnect, or change settings.',
                                        )}
                                    </p>
                                )}
                                {canManageGoogleCalendar ? (
                                    <Dialog open={calendarSettingsOpen} onOpenChange={setCalendarSettingsOpen}>
                                        <DialogContent className="sm:max-w-md">
                                            <DialogHeader>
                                                <DialogTitle>{t('Google Calendar integration settings')}</DialogTitle>
                                            </DialogHeader>
                                            {calendarsLoading ? (
                                                <div className="flex items-center justify-center gap-2 py-8 text-muted-foreground">
                                                    <Loader2 className="h-5 w-5 animate-spin" />
                                                    <span className="text-sm">{t('Loading...')}</span>
                                                </div>
                                            ) : null}
                                            {calendarsError ? (
                                                <p className="text-sm text-destructive">{calendarsError}</p>
                                            ) : null}
                                            {!calendarsLoading && !calendarsError ? (
                                                <div className="space-y-3 py-2">
                                                    <Label htmlFor="integration-google-calendar-select">
                                                        {t('Select a calendar')}
                                                    </Label>
                                                    <Select
                                                        value={selectedCalendarId}
                                                        onValueChange={setSelectedCalendarId}
                                                    >
                                                        <SelectTrigger
                                                            id="integration-google-calendar-select"
                                                            className="w-full"
                                                        >
                                                            <SelectValue placeholder={t('Select a calendar')} />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            {calendars.map((cal) => (
                                                                <SelectItem key={cal.id} value={cal.id}>
                                                                    {cal.summary}
                                                                    {cal.primary ? ` (${t('Primary')})` : ''}
                                                                </SelectItem>
                                                            ))}
                                                        </SelectContent>
                                                    </Select>
                                                </div>
                                            ) : null}
                                            <DialogFooter className="gap-2 sm:gap-0">
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    onClick={() => setCalendarSettingsOpen(false)}
                                                >
                                                    {t('Cancel')}
                                                </Button>
                                                <Button
                                                    type="button"
                                                    onClick={handleSaveCalendarSelection}
                                                    disabled={
                                                        calendarsLoading ||
                                                        calendarsError !== null ||
                                                        calendars.length === 0
                                                    }
                                                >
                                                    {t('Save Changes')}
                                                </Button>
                                            </DialogFooter>
                                        </DialogContent>
                                    </Dialog>
                                ) : null}
                                {canManageGoogleCalendar ? (
                                    <ConfirmDialog
                                        open={disconnectDialogOpen}
                                        onOpenChange={setDisconnectDialogOpen}
                                        title={t('Disconnect Google Calendar?')}
                                        description={t('Google Calendar disconnect warning')}
                                        onConfirm={handleDisconnectConfirm}
                                        confirmText={t('Disconnect')}
                                        cancelText={t('Cancel')}
                                        variant="destructive"
                                    />
                                ) : null}
                            </>
                        ) : (
                            <div className="flex w-full flex-col gap-3">
                                {canManageGoogleCalendar ? (
                                    <Button asChild variant="default" className="w-fit self-start">
                                        <a href={authUrl}>{t('Connect integration')}</a>
                                    </Button>
                                ) : (
                                    <p className="text-sm text-muted-foreground">
                                        {t(
                                            'You can view integration status but do not have permission to connect, disconnect, or change settings.',
                                        )}
                                    </p>
                                )}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </PageTemplate>
    );
}
