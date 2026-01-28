$(document).ready(function () {
    console.log("customer create.js loaded");

    // ************************************************** task venues

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

        console.log("selectedDates", selectedDates);
        const age = calculateAge(selectedDates[0]);
        console.log("age", age);
        $("#participant_age").val(age);
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

    const csrf = $('meta[name="csrf-token"]').attr("content");

    FilePond.create(document.querySelector("#qid_files"), {
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
});
