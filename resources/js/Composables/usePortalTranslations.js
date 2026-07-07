import { usePage } from '@inertiajs/vue3';

export function usePortalTranslations() {
    const page = usePage();

    const t = (key, replacements = {}) => {
        const parts = key.split('.');
        let value = page.props.portal?.translations;

        for (const part of parts) {
            value = value?.[part];
        }

        if (typeof value !== 'string') {
            return key;
        }

        return Object.entries(replacements).reduce(
            (text, [placeholder, replacement]) => text.replace(`:${placeholder}`, String(replacement)),
            value,
        );
    };

    const paymentStatusLabel = (status) => t(`payment_status.${status}`);
    const invoiceStatusLabel = (status) => t(`invoice_status.${status}`);
    const ticketStatusLabel = (status) => t(`ticket_status.${status}`);
    const ticketPriorityLabel = (priority) => t(`ticket_priority.${priority}`);

    return { t, paymentStatusLabel, invoiceStatusLabel, ticketStatusLabel, ticketPriorityLabel };
}
