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

// ============================================================================
// Offline Sync Queue Manager (IndexedDB)
// ============================================================================
class OfflineQueue {
    constructor() {
        this.dbName = 'scimanager-offline';
        this.storeName = 'sync-queue';
        this.db = null;
    }

    async open() {
        if (this.db) return this.db;
        return new Promise((resolve, reject) => {
            const req = indexedDB.open(this.dbName, 1);
            req.onupgradeneeded = (e) => {
                const db = e.target.result;
                if (!db.objectStoreNames.contains(this.storeName)) {
                    const store = db.createObjectStore(this.storeName, { keyPath: 'id', autoIncrement: true });
                    store.createIndex('status', 'status', { unique: false });
                    store.createIndex('createdAt', 'createdAt', { unique: false });
                }
            };
            req.onsuccess = (e) => { this.db = e.target.result; resolve(this.db); };
            req.onerror = () => reject(req.error);
        });
    }

    async enqueue(entry) {
        const db = await this.open();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(this.storeName, 'readwrite');
            const req = tx.objectStore(this.storeName).add({
                ...entry, status: 'pending', createdAt: Date.now(), attempts: 0, lastError: null,
            });
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    async getPending() {
        const db = await this.open();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(this.storeName, 'readonly');
            const req = tx.objectStore(this.storeName).index('status').getAll('pending');
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    async getAll() {
        const db = await this.open();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(this.storeName, 'readonly');
            const req = tx.objectStore(this.storeName).getAll();
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    async update(id, changes) {
        const db = await this.open();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(this.storeName, 'readwrite');
            const store = tx.objectStore(this.storeName);
            const getReq = store.get(id);
            getReq.onsuccess = () => {
                const record = getReq.result;
                if (!record) return reject(new Error('Not found'));
                Object.assign(record, changes);
                const putReq = store.put(record);
                putReq.onsuccess = () => resolve();
                putReq.onerror = () => reject(putReq.error);
            };
        });
    }

    async delete(id) {
        const db = await this.open();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(this.storeName, 'readwrite');
            tx.objectStore(this.storeName).delete(id);
            tx.oncomplete = () => resolve();
            tx.onerror = () => reject(tx.error);
        });
    }

    async count() {
        const db = await this.open();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(this.storeName, 'readonly');
            const req = tx.objectStore(this.storeName).index('status').count('pending');
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }
}

window._offlineQueue = new OfflineQueue();

// ============================================================================
// FormData Serialization Helpers
// ============================================================================
function serializeFormData(formData) {
    const parts = [];
    for (const [name, value] of formData.entries()) {
        if (value instanceof File && value.size > 0) {
            parts.push({ name, fileName: value.name, type: value.type, blob: value });
        } else if (!(value instanceof File)) {
            parts.push({ name, value: String(value) });
        }
    }
    return parts;
}

function rebuildFormData(parts) {
    const fd = new FormData();
    for (const part of parts) {
        if (part.blob) {
            fd.append(part.name, part.blob, part.fileName);
        } else {
            fd.append(part.name, part.value);
        }
    }
    return fd;
}

// ============================================================================
// Offline-Aware Form Submission
// ============================================================================
async function queueRequest(form, bodyParts, description, onQueued) {
    const filtered = bodyParts.filter(p => p.name !== '_token');
    await window._offlineQueue.enqueue({
        url: form.action,
        method: 'POST',
        bodyParts: filtered,
        formOrigin: window.location.href,
        description: description || 'Formulaire en attente',
    });
    window.dispatchEvent(new CustomEvent('offline-queue-changed'));
    window.toast('Hors ligne : donnees enregistrees localement. Elles seront envoyees automatiquement.', 'info', 6000);
    onQueued && onQueued();
    return { queued: true };
}

async function offlineAwareSubmit(form, { onSuccess, onValidationError, onError, onQueued, description }) {
    const formData = new FormData(form);
    const csrfToken = document.querySelector('meta[name=csrf-token]').content;
    const bodyParts = serializeFormData(formData);

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: formData,
        });

        if (response.status === 503) {
            const data = await response.json();
            if (data._offline_queued) {
                return await queueRequest(form, bodyParts, description, onQueued);
            }
        }

        const result = await response.json();

        if (response.ok && result.success) {
            onSuccess && onSuccess(result);
        } else if (response.status === 422 && result.errors) {
            onValidationError && onValidationError(result);
        } else {
            onError && onError(result.message || 'Une erreur est survenue.');
        }
        return { queued: false, response, result };
    } catch (err) {
        if (!navigator.onLine) {
            return await queueRequest(form, bodyParts, description, onQueued);
        }
        onError && onError('Erreur de connexion. Veuillez reessayer.');
        return { queued: false, error: err };
    }
}

window.offlineAwareSubmit = offlineAwareSubmit;

// ============================================================================
// Sync Manager (replays queue when back online)
// ============================================================================
class SyncManager {
    constructor() { this.syncing = false; }

    async refreshCsrfToken() {
        try {
            const res = await fetch('/csrf-token', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
            if (res.ok) {
                const data = await res.json();
                document.querySelector('meta[name=csrf-token]').content = data.token;
                return data.token;
            }
        } catch (e) { /* still offline */ }
        return null;
    }

    async processQueue() {
        if (this.syncing || !navigator.onLine) return;
        this.syncing = true;

        try {
            const csrfToken = await this.refreshCsrfToken();
            if (!csrfToken) { this.syncing = false; return; }

            const pending = await window._offlineQueue.getPending();
            if (pending.length === 0) { this.syncing = false; return; }

            let successCount = 0, failCount = 0;

            for (const entry of pending) {
                await window._offlineQueue.update(entry.id, { status: 'syncing' });
                window.dispatchEvent(new CustomEvent('offline-queue-changed'));

                try {
                    const formData = rebuildFormData(entry.bodyParts);
                    const response = await fetch(entry.url, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: formData,
                        credentials: 'same-origin',
                    });

                    if (response.ok) {
                        await window._offlineQueue.delete(entry.id);
                        successCount++;
                    } else if (response.status === 422) {
                        const result = await response.json();
                        await window._offlineQueue.update(entry.id, {
                            status: 'failed', attempts: entry.attempts + 1,
                            lastError: Object.values(result.errors || {}).flat().join(', ') || result.message || 'Erreur de validation',
                        });
                        failCount++;
                    } else if (response.status === 419) {
                        await window._offlineQueue.update(entry.id, { status: 'pending' });
                        break;
                    } else {
                        await window._offlineQueue.update(entry.id, {
                            status: 'failed', attempts: entry.attempts + 1,
                            lastError: 'Erreur serveur (' + response.status + ')',
                        });
                        failCount++;
                    }
                } catch (err) {
                    await window._offlineQueue.update(entry.id, { status: 'pending' });
                    break;
                }
            }

            window.dispatchEvent(new CustomEvent('offline-queue-changed'));

            if (successCount > 0) {
                window.toast(successCount + ' element(s) synchronise(s) avec succes.', 'success', 5000);
            }
            if (failCount > 0) {
                window.toast(failCount + ' element(s) en erreur de synchronisation.', 'warning', 8000);
            }
        } finally {
            this.syncing = false;
        }
    }
}

window._syncManager = new SyncManager();

window.addEventListener('online', () => {
    setTimeout(() => window._syncManager.processQueue(), 2000);
});

if ('serviceWorker' in navigator) {
    navigator.serviceWorker.addEventListener('message', (event) => {
        if (event.data && event.data.type === 'PROCESS_SYNC') {
            window._syncManager.processQueue();
        }
    });
}

// ============================================================================
// Connection Status Alpine Component
// ============================================================================
Alpine.data('connectionStatus', () => ({
    online: navigator.onLine,
    pendingCount: 0,
    failedCount: 0,
    showDetails: false,
    queueItems: [],

    async init() {
        window.addEventListener('online', () => { this.online = true; this.refreshCount(); });
        window.addEventListener('offline', () => { this.online = false; });
        window.addEventListener('offline-queue-changed', () => this.refreshCount());
        await this.refreshCount();
        setInterval(() => this.refreshCount(), 30000);
    },

    async refreshCount() {
        try {
            const all = await window._offlineQueue.getAll();
            this.pendingCount = all.filter(i => i.status === 'pending' || i.status === 'syncing').length;
            this.failedCount = all.filter(i => i.status === 'failed').length;
            this.queueItems = all;
        } catch (e) {
            this.pendingCount = 0;
            this.failedCount = 0;
        }
    },

    async retryFailed() {
        const failed = this.queueItems.filter(i => i.status === 'failed');
        for (const item of failed) {
            await window._offlineQueue.update(item.id, { status: 'pending', lastError: null });
        }
        window.dispatchEvent(new CustomEvent('offline-queue-changed'));
        window._syncManager.processQueue();
    },

    async discardItem(id) {
        await window._offlineQueue.delete(id);
        window.dispatchEvent(new CustomEvent('offline-queue-changed'));
    },

    async syncNow() {
        if (!navigator.onLine) { window.toast('Pas de connexion internet.', 'warning'); return; }
        window._syncManager.processQueue();
    },

    get totalPending() { return this.pendingCount + this.failedCount; },
}));

// ============================================================================
// Global Form Interceptor (for raw POST forms when offline)
// ============================================================================
document.addEventListener('submit', async function (e) {
    const form = e.target;
    if (form.method.toUpperCase() !== 'POST') return;
    if (form.hasAttribute('data-no-offline')) return;
    if (form.querySelector('input[name="_method"][value="DELETE"]')) return;

    const skipPaths = ['/login', '/logout', '/register', '/switch-sci', '/password', '/email/verification'];
    const action = form.action || '';
    if (skipPaths.some(p => action.includes(p))) return;

    if (navigator.onLine) return;

    e.preventDefault();
    const formData = new FormData(form);
    const bodyParts = serializeFormData(formData).filter(p => p.name !== '_token');
    const title = form.closest('[x-data]')?.querySelector('h3,h2')?.textContent?.trim() || 'Formulaire en attente';

    await window._offlineQueue.enqueue({
        url: form.action, method: 'POST', bodyParts,
        formOrigin: window.location.href, description: title,
    });

    window.dispatchEvent(new CustomEvent('offline-queue-changed'));
    window.toast('Hors ligne : donnees enregistrees localement.', 'info', 6000);
    window.dispatchEvent(new CustomEvent('close-modal'));
}, true);

// ============================================================================
// Alpine Components
// ============================================================================
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
        const modalTitle = this.$el.dataset.modalTitle || 'Formulaire';

        await offlineAwareSubmit(form, {
            description: modalTitle,
            onSuccess: () => {
                this.close();
                window.location.reload();
            },
            onValidationError: (result) => {
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
            },
            onError: (msg) => {
                this.errors = { _general: [msg] };
                window.toast(msg, 'error');
            },
            onQueued: () => {
                this.close();
            },
        });

        this.loading = false;
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
