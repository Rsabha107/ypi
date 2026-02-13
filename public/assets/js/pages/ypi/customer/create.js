$(document).ready(function () {
    console.log("customer create.js loaded");

    // ************************************************** task venues
    function toggleSpecifyField() {
        let selectedText = $("#food_allergy_id option:selected")
            .text()
            .trim()
            .replace(/\s+/g, " ");
        console.log("selected", selectedText);

        if (selectedText === "Others") {
            console.log("show specify field");
            $("#food_allergy_other_wrap").slideDown(200);
        } else {
            $("#food_allergy_other_wrap").slideUp(200);
            $('input[name="specify_food_allergy"]').val("");
        }
    }

    // On change
    $("#food_allergy_id").on("change", function () {
        toggleSpecifyField();
    });

    toggleSpecifyField(); // on page load

    function calculateAge(dob) {
        if (!dob) return null;

        const birthDate = new Date(dob);
        const today = new Date();

        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();

        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        return age;
    }

    const fpAdd = document.querySelector("#date_of_birth")._flatpickr;

    fpAdd.config.onChange.push(function (selectedDates) {
        if (!selectedDates.length) return;

        valid = true;

        console.log("selectedDates", selectedDates);
        const dob = selectedDates[0];
        const age = calculateAge(dob);
        const participant_type = $("#participant_type_id").val();
        const participant_label = $("#participant_type_id option:selected")
            .text()
            .trim();
        console.log("age", age);

        $("#participant_age").val(age);

        if (participant_label === "Player Escort kid (Ages 6-11)") {
            if (age < 6 || age > 11) {
                valid = false;
                errorMsg =
                    "Player Escort Kid age must be between 6 and 11 years.";
            }
        }

        if (participant_label === "Flag Bearer (Ages 11-16)") {
            if (age < 11 || age > 16) {
                valid = false;
                errorMsg = "Flag Bearer age must be between 11 and 16 years.";
            }
        }

        if (participant_label === "Ball Crew (Ages 12-16)") {
            if (age < 12 || age > 16) {
                valid = false;
                errorMsg = "Ball Crew age must be between 12 and 16 years.";
            }
        }

        if (participant_label === "Official Match Ball Carrier (Ages 8-16)") {
            if (age < 8 || age > 16) {
                valid = false;
                errorMsg = "Official Match Ball Carrier age must be between 8 and 16 years.";
            }
        }

        if (participant_label === "Referee walk out escort child (Ages 6-11)") {
            if (age < 6 || age > 11) {
                valid = false;
                errorMsg = "Referee walk out escort child age must be between 6 and 11 years.";
            }
        }

        if (!valid) {
            console.log("errorMsg", errorMsg);
            toastr.error(errorMsg);

            // reset DOB + age
            fpAdd.clear();
            $("#participant_age").val("");

            $("#participant_type_id").on("change", function () {
                if (fpAdd.selectedDates.length) {
                    fpAdd.config.onChange[0](fpAdd.selectedDates);
                }
            });

            return;
        }
    });

    document
        .getElementById("has_food_allergy")
        .addEventListener("change", function () {
            document
                .getElementById("food_allergy_details_wrap")
                .classList.toggle("d-none", !this.checked);
        });

    document
        .getElementById("has_health_issues")
        .addEventListener("change", function () {
            document
                .getElementById("health_issues_details_wrap")
                .classList.toggle("d-none", !this.checked);
        });

    // delete participant
    $("body").on("click", "#deleteParticipant", function (e) {
        var id = $(this).data("id");
        var tableID = $(this).data("table");
        e.preventDefault();
        // console.log('in deleteBooking '+id);
        // console.log('in deleteBooking '+tableID);
        var link = $(this).attr("href");
        Swal.fire({
            title: "Are you sure?",
            text: "Delete This Data?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                // console.log('inside confirmed')
                $.ajax({
                    url: "/ypi/admin/participant/delete/" + id,
                    type: "DELETE",
                    headers: {
                        // "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content",
                        ),
                    },
                    dataType: "json",
                    success: function (result) {
                        // alert(result)
                        if (!result["error"]) {
                            toastr.success(result["message"]);
                            $("#" + tableID).bootstrapTable("refresh");
                            // Swal.fire(
                            //     'Deleted!',
                            //     'Your file has been deleted.',
                            //     'success'
                            //   )
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr.status);
                        console.log(thrownError);
                    },
                });
            }
        });
    });

    document
        .getElementById("qid_clear")
        ?.addEventListener("click", function () {
            const input = document.getElementById("qid_file");
            if (input) input.value = "";
        });

    FilePond.registerPlugin(
        FilePondPluginImagePreview,
        FilePondPluginFileValidateType,
        FilePondPluginFileValidateSize,
    );

    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const input = document.querySelector("#qid_files");
    const saveParticipantBtn = document.getElementById("saveParticipantBtn");
    const serverIdInput = document.getElementById("qid_server_id");

    if (!input) return;

    const setButtonDisabled = (disabled) => {
        saveParticipantBtn.disabled = disabled;
        saveParticipantBtn.classList.toggle("is-disabled", disabled);
        saveParticipantBtn.dataset.originalText ??=
            saveParticipantBtn.innerText;
        saveParticipantBtn.innerText = disabled
            ? "Uploading…"
            : saveParticipantBtn.dataset.originalText;
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
            'Drag & Drop QID/Passport Image or <span class="filepond--label-action">Browse</span>',
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

    // On form submit -> validate
    const form = document.querySelector("#spinner-form"); // your form id

    form.addEventListener("submit", function (e) {
        // optional: count only files that are actually in the pond
        const count = pond.getFiles().length;

        if (count === 0) {
            e.preventDefault();

            toastr.error(
                "Please upload participant QID File before submitting.",
            );

            // nice UX: highlight + scroll
            input
                .closest(".filepond--wrapper")
                ?.scrollIntoView({ behavior: "smooth", block: "center" });

            return false;
        }
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
});
