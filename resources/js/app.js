import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Global toast notification function.
 * Usage: window.toast('Message', 'error') — types: success, error, warning, info
 */
window.toast = function (message, type = 'error', duration = 0) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const icons = {
        success: `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`,
        error: `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`,
        warning: `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>`,
        info: `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
    };

    const colors = {
        success: 'text-green-500 bg-green-100',
        error: 'text-red-500 bg-red-100',
        warning: 'text-orange-500 bg-orange-100',
        info: 'text-blue-500 bg-blue-100',
    };

    const el = document.createElement('div');
    el.className = 'pointer-events-auto flex items-start w-full max-w-sm p-4 bg-white rounded-lg shadow-lg border border-gray-100 transition-all duration-300 opacity-0 translate-x-8';
    el.innerHTML = `
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 ${colors[type] || colors.error} rounded-lg">${icons[type] || icons.error}</div>
        <div class="ms-3 text-sm font-normal text-gray-800 flex-1">${message}</div>
        <button class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 transition shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;

    container.appendChild(el);

    // Animate in
    requestAnimationFrame(() => {
        el.classList.remove('opacity-0', 'translate-x-8');
        el.classList.add('opacity-100', 'translate-x-0');
    });

    const dismiss = () => {
        el.classList.remove('opacity-100', 'translate-x-0');
        el.classList.add('opacity-0', 'translate-x-8');
        setTimeout(() => el.remove(), 300);
    };

    el.querySelector('button').addEventListener('click', dismiss);

    // Auto-dismiss: success 5s, warning 6s, info 5s, error only if duration specified
    const autoDuration = duration || { success: 5000, warning: 6000, info: 5000, error: 0 }[type];
    if (autoDuration > 0) {
        setTimeout(dismiss, autoDuration);
    }
};

Alpine.data('wizardModal', () => ({
    show: false,
    loading: false,
    errors: {},
    currentStep: 0,
    totalSteps: 0,
    steps: [],

    initSteps(el) {
        try {
            this.steps = JSON.parse(el.dataset.steps || '[]');
        } catch (e) {
            this.steps = [];
        }
        this.totalSteps = this.steps.length;
    },

    open() {
        this.errors = {};
        this.loading = false;
        this.currentStep = 0;
        this.show = true;
    },

    close() {
        this.show = false;
        this.errors = {};
        this.currentStep = 0;
    },

    nextStep() {
        if (this.currentStep < this.totalSteps - 1) {
            this.currentStep++;
        }
    },

    prevStep() {
        if (this.currentStep > 0) {
            this.currentStep--;
        }
    },

    isLastStep() {
        return this.currentStep === this.totalSteps - 1;
    },

    async submit(e) {
        e.preventDefault();
        this.loading = true;
        this.errors = {};

        const form = e.target;
        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: formData,
            });

            const result = await response.json();

            if (response.ok && result.success) {
                this.close();
                window.location.reload();
            } else if (response.status === 422 && result.errors) {
                this.errors = result.errors;
                const errorFields = Object.keys(result.errors);
                const messages = errorFields.flatMap(f => result.errors[f]);
                window.toast(messages.join('<br>'), 'error');
                if (errorFields.length > 0) {
                    const firstErrorField = form.querySelector('[name="' + errorFields[0] + '"], [name="' + errorFields[0] + '[]"]');
                    if (firstErrorField) {
                        const stepEl = firstErrorField.closest('[data-step]');
                        if (stepEl) {
                            this.currentStep = parseInt(stepEl.dataset.step);
                        }
                    }
                }
            } else {
                const msg = result.message || 'Une erreur est survenue.';
                this.errors = { _general: [msg] };
                window.toast(msg, 'error');
            }
        } catch (err) {
            const msg = 'Erreur de connexion. Veuillez reessayer.';
            this.errors = { _general: [msg] };
            window.toast(msg, 'error');
        } finally {
            this.loading = false;
        }
    }
}));

Alpine.data('signaturePad', (saveUrl = '', existingSignature = '') => ({
    drawing: false,
    signed: false,
    saving: false,
    lastX: 0,
    lastY: 0,

    init() {
        const canvas = this.$refs.canvas;
        const ctx = canvas.getContext('2d');

        // Set canvas resolution to match display size
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * 2;
        canvas.height = rect.height * 2;
        ctx.scale(2, 2);

        ctx.strokeStyle = '#1a1a2e';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        // If there's an existing signature, draw it
        if (existingSignature) {
            const img = new Image();
            img.onload = () => {
                ctx.drawImage(img, 0, 0, rect.width, rect.height);
            };
            img.src = existingSignature;
            this.signed = true;
        }
    },

    getPos(e) {
        const canvas = this.$refs.canvas;
        const rect = canvas.getBoundingClientRect();
        const touch = e.touches ? e.touches[0] : e;
        return {
            x: touch.clientX - rect.left,
            y: touch.clientY - rect.top,
        };
    },

    startDraw(e) {
        e.preventDefault();
        this.drawing = true;
        const pos = this.getPos(e);
        this.lastX = pos.x;
        this.lastY = pos.y;
    },

    draw(e) {
        if (!this.drawing) return;
        e.preventDefault();
        const canvas = this.$refs.canvas;
        const ctx = canvas.getContext('2d');
        const pos = this.getPos(e);

        ctx.beginPath();
        ctx.moveTo(this.lastX, this.lastY);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();

        this.lastX = pos.x;
        this.lastY = pos.y;
        this.signed = true;
    },

    stopDraw() {
        this.drawing = false;
    },

    clear() {
        const canvas = this.$refs.canvas;
        const ctx = canvas.getContext('2d');
        const rect = canvas.getBoundingClientRect();
        ctx.clearRect(0, 0, rect.width, rect.height);
        this.signed = false;
    },

    toDataURL() {
        return this.$refs.canvas.toDataURL('image/png');
    },

    async saveAndPrint() {
        if (!this.signed) {
            window.toast('Veuillez signer avant d\'imprimer.', 'warning');
            return;
        }

        this.saving = true;
        const dataUrl = this.toDataURL();

        try {
            const response = await fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({ signature_data: dataUrl }),
            });

            const result = await response.json();

            if (result.success) {
                // Update the print signature image
                const signImg = document.getElementById('signature-print-img');
                if (signImg) {
                    signImg.src = dataUrl;
                    signImg.style.display = 'block';
                }
                window.toast('Signature enregistree.', 'success', 2000);
                setTimeout(() => window.print(), 500);
            } else {
                window.toast('Erreur lors de l\'enregistrement.', 'error');
            }
        } catch (err) {
            window.toast('Erreur de connexion.', 'error');
        } finally {
            this.saving = false;
        }
    },
}));

Alpine.data('moneyInput', (initialValue = '', min = null, max = null) => ({
    display: '',
    rawValue: initialValue,

    init() {
        if (this.rawValue !== '' && this.rawValue !== null) {
            // Parse as float then round to integer (FCFA has no decimals)
            // Fixes values like "300000.00" being treated as "30000000"
            this.rawValue = String(Math.round(parseFloat(this.rawValue) || 0));
            this.display = this.format(this.rawValue);
        }
    },

    format(value) {
        const num = String(value).replace(/[^0-9]/g, '');
        return num.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    },

    onInput(e) {
        const cursorPos = e.target.selectionStart;
        const oldLength = this.display.length;

        const raw = e.target.value.replace(/[^0-9]/g, '');
        this.rawValue = raw;
        this.display = this.format(raw);

        this.$nextTick(() => {
            const newLength = this.display.length;
            const diff = newLength - oldLength;
            e.target.setSelectionRange(cursorPos + diff, cursorPos + diff);
        });
    },
}));

Alpine.start();
