document.addEventListener("DOMContentLoaded", () => {
    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginFileValidateType,
        FilePondPluginFileValidateSize,
    );

    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const input = document.querySelector("#qid_files");
    const registerBtn = document.getElementById("registerBtn");
    const serverIdInput = document.getElementById("qid_server_id");

    if (!input) return;

    const setButtonDisabled = (disabled) => {
        registerBtn.disabled = disabled;
        registerBtn.classList.toggle("is-disabled", disabled);
        registerBtn.dataset.originalText ??= registerBtn.innerText;
        registerBtn.innerText = disabled
            ? "Uploading…"
            : registerBtn.dataset.originalText;
    };

    const pond = FilePond.create(document.querySelector("#qid_files"), {
        name: "qid_files[]",
        allowMultiple: true,
        maxFiles: 2,
        maxFileSize: "2MB",
        acceptedFileTypes: [
            "image/png",
            "image/jpeg",
            "image/jpg",
            "image/gif",
            "image/webp",
            "application/pdf",
        ],
        labelIdle:
            'Drag & Drop QID Image or <span class="filepond--label-action">Browse</span>',
        server: {
            process: {
                url: "/uploads/process",
                method: "POST",
                headers: { "X-CSRF-TOKEN": csrf },
            },
            revert: {
                url: "/uploads/revert",
                method: "DELETE",
                headers: { "X-CSRF-TOKEN": csrf },
            },
        },
    });

    const anyUploading = () =>
        pond
            .getFiles()
            .some((f) => f.status === FilePond.FileStatus.PROCESSING);

    // Disable immediately when upload starts
    pond.on("processfilestart", () => setButtonDisabled(true));

    // Re-enable when upload completes and nothing else uploading
    pond.on("processfile", () => {
        if (!anyUploading()) setButtonDisabled(false);
    });

    // If upload is aborted or errors out
    pond.on("processfileabort", () => setButtonDisabled(false));
    pond.on("processfileerror", () => setButtonDisabled(false));

    // Removing file should re-enable (unless another upload still running)
    pond.on("removefile", () => {
        if (!anyUploading()) setButtonDisabled(false);
    });

        // On form submit -> validate
    const form = document.querySelector("#spinner-form"); // your form id

    form.addEventListener("submit", function (e) {
        // optional: count only files that are actually in the pond
        const count = pond.getFiles().length;

        if (count === 0) {
            e.preventDefault();

            toastr.error("Please upload participant QID File before submitting.");

            // nice UX: highlight + scroll
            input
                .closest(".filepond--wrapper")
                ?.scrollIntoView({ behavior: "smooth", block: "center" });

            return false;
        }
    });
});
