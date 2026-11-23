<script>
    (function () {
        function setSubmittingState(form, submitter) {
            form.dataset.submitting = 'true';

            const buttons = form.querySelectorAll('button[type="submit"], button:not([type]), input[type="submit"], [data-submit-button]');
            buttons.forEach(button => {
                button.disabled = true;
            });

            const button = submitter || buttons[0];
            if (button && 'disabled' in button) {
                button.disabled = true;
            }
            if (button && button.tagName === 'BUTTON') {
                const label = button.dataset.loadingLabel || 'Memproses...';
                button.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>${label}`;
                button.setAttribute('aria-busy', 'true');
            }

            const overlay = document.getElementById('loading-overlay');
            if (overlay) {
                overlay.style.display = 'flex';
            }
        }

        window.submitFormWithFeedback = function (form, submitter) {
            if (form.dataset.submitting === 'true' || !form.reportValidity()) {
                return false;
            }

            setSubmittingState(form, submitter);
            HTMLFormElement.prototype.submit.call(form);

            return true;
        };

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('form[onsubmit]').forEach(form => {
                form.removeAttribute('onsubmit');
            });
        });

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            if (form.dataset.submitting === 'true') {
                event.preventDefault();
                return;
            }

            const isDeleteForm = form.querySelector('input[name="_method"][value="DELETE"]');
            if (isDeleteForm) {
                event.preventDefault();
                if (form.dataset.confirmingDelete === 'true') {
                    return;
                }

                form.dataset.confirmingDelete = 'true';
                const submitter = event.submitter;
                const confirmation = window.Swal
                    ? window.Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: 'Data yang dihapus tidak dapat dikembalikan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    })
                    : Promise.resolve({ isConfirmed: window.confirm('Yakin ingin menghapus data ini?') });

                confirmation.then(result => {
                    form.dataset.confirmingDelete = 'false';
                    if (!result.isConfirmed || !form.isConnected) {
                        return;
                    }

                    setSubmittingState(form, submitter);
                    HTMLFormElement.prototype.submit.call(form);
                });

                return;
            }

            setSubmittingState(form, event.submitter);
        }, true);
    })();
</script>
