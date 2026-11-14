<div id="dashboard-approval-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-gray-900/60 p-4" aria-hidden="true">
    <section role="dialog" aria-modal="true" aria-labelledby="dashboard-approval-title" class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-5 flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"><i class="bi bi-calendar-check text-lg"></i></span>
            <div>
                <h2 id="dashboard-approval-title" class="text-lg font-bold text-gray-900">Review appointment before approval</h2>
                <p class="mt-1 text-sm text-gray-500">Check the appointment details before confirming.</p>
            </div>
        </div>
        <dl class="space-y-3 rounded-xl bg-gray-50 p-4 text-sm">
            <div><dt class="font-semibold text-gray-500">Pet</dt><dd id="dashboard-approval-pet" class="mt-0.5 text-gray-900"></dd></div>
            <div><dt class="font-semibold text-gray-500">Owner</dt><dd id="dashboard-approval-owner" class="mt-0.5 text-gray-900"></dd></div>
            <div><dt class="font-semibold text-gray-500">Selected services</dt><dd id="dashboard-approval-service" class="mt-0.5 text-gray-900"></dd></div>
            <div><dt class="font-semibold text-gray-500">Date and time</dt><dd id="dashboard-approval-datetime" class="mt-0.5 text-gray-900"></dd></div>
        </dl>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" id="dashboard-approval-cancel" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</button>
            <button type="button" id="dashboard-approval-confirm" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Confirm approval</button>
        </div>
    </section>
</div>
<script>
(() => {
    const modal = document.getElementById('dashboard-approval-modal');
    if (!modal) return;
    let pendingForm = null;
    let confirmedForm = null;
    const fields = {
        pet: document.getElementById('dashboard-approval-pet'),
        owner: document.getElementById('dashboard-approval-owner'),
        service: document.getElementById('dashboard-approval-service'),
        datetime: document.getElementById('dashboard-approval-datetime'),
    };
    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
        pendingForm = null;
    };
    document.querySelectorAll('form.dashboard-approval-form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (confirmedForm === form) {
                confirmedForm = null;
                return;
            }
            event.preventDefault();
            pendingForm = form;
            Object.keys(fields).forEach((key) => { fields[key].textContent = form.dataset[key] || '—'; });
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            document.getElementById('dashboard-approval-confirm').focus();
        });
    });
    document.getElementById('dashboard-approval-confirm').addEventListener('click', () => {
        if (!pendingForm) return;
        const form = pendingForm;
        closeModal();
        confirmedForm = form;
        form.requestSubmit();
    });
    document.getElementById('dashboard-approval-cancel').addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => { if (event.target === modal) closeModal(); });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal(); });
})();
</script>
