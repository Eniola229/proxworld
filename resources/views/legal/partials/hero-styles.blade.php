<style>
    html { scroll-behavior: smooth; }

    /* ============================================
       REFUND PAGE — LIGHT & DARK MODE COMPATIBLE
    ============================================ */

    /* Hero */
    .refund-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .refund-hero-title,
    .refund-hero-icon {
        color: #ffffff !important;
        text-shadow: 0 1px 4px rgba(0,0,0,0.3);
    }
    .refund-hero-subtitle {
        color: rgba(255,255,255,0.92) !important;
        text-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .refund-hero-meta {
        color: rgba(255,255,255,0.7) !important;
    }

    /* Decorative circles */
    .refund-hero-circle {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.12);
        pointer-events: none;
        z-index: 1;
    }

    /* Icon circles in summary card */
    .refund-icon-circle { border-radius: 50%; }
    .refund-icon-success { background-color: rgba( 25, 135,  84, 0.12); }
    .refund-icon-primary { background-color: rgba( 13, 110, 253, 0.12); }
    .refund-icon-info    { background-color: rgba( 13, 202, 240, 0.12); }

    /* Soft neutral card (replaces bg-light) */
    .refund-soft-card {
        background-color: var(--bs-tertiary-bg, rgba(0,0,0,0.04));
        color: var(--bs-body-color);
    }

    /* Coloured tint cards */
    .refund-colored-card { color: var(--bs-body-color); }
    .refund-colored-success { background-color: rgba( 25, 135,  84, 0.12); }
    .refund-colored-danger  { background-color: rgba(220,  53,  69, 0.12); }
    .refund-colored-warning { background-color: rgba(255, 193,   7, 0.12); }
    .refund-colored-info    { background-color: rgba( 13, 202, 240, 0.12); }
    .refund-colored-primary { background-color: rgba( 13, 110, 253, 0.12); }

    /* Alert boxes */
    .refund-alert {
        padding: 0.85rem 1rem;
        border-radius: 0.5rem;
        color: var(--bs-body-color);
    }
    .refund-alert-info    { background-color: rgba( 13, 202, 240, 0.15); }
    .refund-alert-warning { background-color: rgba(255, 193,   7, 0.15); }
    .refund-alert-danger  { background-color: rgba(220,  53,  69, 0.15); }
    .refund-alert-primary { background-color: rgba( 13, 110, 253, 0.15); }
    .refund-alert-success { background-color: rgba( 25, 135,  84, 0.15); }

    /* Accordion — fully theme-aware */
    .refund-accordion-item {
        background-color: var(--bs-card-bg, var(--bs-body-bg)) !important;
        border-color: var(--bs-border-color) !important;
    }
    .refund-accordion-item .accordion-button {
        background-color: var(--bs-card-bg, var(--bs-body-bg)) !important;
        color: var(--bs-body-color) !important;
        transition: all 0.3s ease;
    }
    .refund-accordion-item .accordion-button:not(.collapsed) {
        background-color: var(--bs-tertiary-bg, rgba(0,0,0,0.03)) !important;
        color: var(--bs-body-color) !important;
        box-shadow: none !important;
    }
    .refund-accordion-item .accordion-button:focus {
        box-shadow: none !important;
    }
    .refund-accordion-item .accordion-body {
        background-color: var(--bs-card-bg, var(--bs-body-bg)) !important;
        color: var(--bs-body-color) !important;
    }
    .accordion-icon {
        transition: transform 0.3s ease;
    }
    .accordion-button:not(.collapsed) .accordion-icon {
        transform: rotate(90deg);
    }

    /* CTA card */
    .refund-cta-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .refund-cta-icon,
    .refund-cta-title,
    .refund-cta-text {
        color: #ffffff !important;
    }
</style>