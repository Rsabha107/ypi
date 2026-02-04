
document.addEventListener('DOMContentLoaded', () => {
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  FilePond.registerPlugin(FilePondPluginImagePreview);

  let pond = null;
  const idsInput = document.getElementById('ypi_temp_upload_ids');

  function pushId(id) {
    const ids = JSON.parse(idsInput.value || "[]");
    if (!ids.includes(Number(id))) ids.push(Number(id));
    idsInput.value = JSON.stringify(ids);
  }

  function removeId(id) {
    const ids = JSON.parse(idsInput.value || "[]").filter(x => x !== Number(id));
    idsInput.value = JSON.stringify(ids);
  }

  const modalEl = document.getElementById('ypiUploadCertificateModal');

  modalEl.addEventListener('shown.bs.modal', () => {
    if (pond) return;

    pond = FilePond.create(document.querySelector('#ypi_certificate'), {
      allowMultiple: false,
      maxFiles: 1,
      acceptedFileTypes: ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'],
      maxFileSize: '5MB',

      server: {
        process: {
          url: '/uploads/process',
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrf },
          onload: (serverId) => {
            // serverId must be TempUpload ID
            pushId(serverId);
            return serverId;
          }
        },
        revert: (serverId, load) => {
          // DELETE temp
          fetch('/uploads/revert', {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'text/plain' },
            body: serverId
          }).finally(() => {
            removeId(serverId);
            load();
          });
        }
      }
    });
  });

  // optional: clear pond when closing modal
  modalEl.addEventListener('hidden.bs.modal', () => {
    // keep uploads + ids if you want to attach after closing
    // if you want to reset each time:
    // if (pond) pond.removeFiles();
    // idsInput.value = "[]";
  });

  document.getElementById('btnYpiUploadDone').addEventListener('click', () => {
    // You can do extra validation here if needed
    bootstrap.Modal.getInstance(modalEl).hide();
    // Example toast:
    toastr.success('Images uploaded and ready to attach.');
  });
});

