import QRCodeStyling from 'qr-code-styling';

/**
 * Styled QR (rounded modules / corners) for ticket cards — must match design (qr-code-styling).
 */
function initTicketQrs() {
    document.querySelectorAll('.vtx-pass__qr-canvas[data-qr]').forEach((el) => {
        const text = el.getAttribute('data-qr');
        if (!text) {
            return;
        }

        const isNarrow = window.matchMedia('(max-width: 767.98px)').matches;
        /* Plus grand à l’intérieur du cadre (desktop) — le cadre CSS reste inchangé */
        const size = isNarrow ? 150 : 204;

        const qr = new QRCodeStyling({
            width: size,
            height: size,
            type: 'svg',
            data: text,
            margin: 6,
            qrOptions: {
                errorCorrectionLevel: 'M',
            },
            dotsOptions: {
                color: '#0f172a',
                type: 'rounded',
            },
            cornersSquareOptions: {
                color: '#0f172a',
                type: 'extra-rounded',
            },
            cornersDotOptions: {
                color: '#0f172a',
                type: 'dot',
            },
            backgroundOptions: {
                color: '#ffffff',
            },
        });

        el.replaceChildren();
        qr.append(el);
    });
}

document.addEventListener('DOMContentLoaded', initTicketQrs);
