$(document).ready(function () {
    const csrf = $('meta[name="csrf-token"]').attr("content");

    const $input = $("#certFile");
    const $btn = $("#certUploadBtn");
    const $msg = $("#certMsg");
    const $certPath = $("#certPath");
    const $closeBtn = $("#certCloseBtn");

    // enable/disable button + show selected name
    $input.on("change", function () {
        const hasFile = this.files && this.files.length > 0;
        $btn.prop("disabled", !hasFile);

        if (hasFile) {
            $msg.text("Selected: " + this.files[0].name).attr(
                "class",
                "small mt-2 text-muted",
            );
        } else {
            $msg.text("").attr("class", "small mt-2");
        }
    });

    // upload using jQuery AJAX
    $btn.on("click", function () {
        const file = $input[0].files && $input[0].files[0];
        const participantId = $("#participantIdForCert").val();
        const tableID = $("#ypiUploadCertModal").data("table");
        if (!file) return;

        $btn.prop("disabled", true);
        $closeBtn.prop("disabled", true);
        $msg.text("Uploading...").attr("class", "small mt-2 text-muted");

        console.log("uploading cert for participant id: ", participantId);
        console.log('table id: ', tableID);

        const fd = new FormData();
        fd.append("certificate", file);
        fd.append("participant_id", participantId);
        $.ajax({
            url: "/ypi/admin/participant/certificate/upload",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,
            headers: { "X-CSRF-TOKEN": csrf },

            success: function (data) {
                $certPath.val(data.path || "");
                $msg.text("Uploaded ✅").attr(
                    "class",
                    "small mt-2 text-success",
                );
                $("#ypiUploadCertModal").modal("hide");
                $("#" + tableID).bootstrapTable("refresh");
                toastr?.success?.("Certificate uploaded.");
            },

            error: function (xhr) {
                let message = "Upload failed";

                // Laravel validation errors: { errors: { certificate: [...] } }
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message)
                        message = xhr.responseJSON.message;

                    if (xhr.responseJSON.errors) {
                        const errs = [];
                        Object.values(xhr.responseJSON.errors).forEach(
                            (arr) => {
                                (arr || []).forEach((m) => errs.push(m));
                            },
                        );
                        if (errs.length) message = errs.join("<br>");
                    }
                }

                $msg.html(message).attr("class", "small mt-2 text-danger");
                toastr?.error?.(message);
            },

            complete: function () {
                $btn.prop("disabled", false);
                $closeBtn.prop("disabled", false);
            },
        });
    });

    // clear form when modal closes (any close method)
    $("#ypiUploadCertModal").on("hidden.bs.modal", function () {
        $input.val("");
        $certPath.val("");
        $msg.text("").attr("class", "small mt-2");
        $btn.prop("disabled", true);
        $closeBtn.prop("disabled", false);
    });
});
