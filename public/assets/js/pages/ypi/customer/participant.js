$(document).ready(function () {
    // console.log("all tasksJS file");

    // ************************************************** task venues

    // QID Image Modal Handler
    $("body").on("click", ".qid-image-link", function (e) {
        e.preventDefault();
        console.log("QID image link clicked");
        var imageUrl = $(this).data("image-url");
        var qid = $(this).data("qid");
        console.log("Image URL:", imageUrl);
        
        $("#qidImagePreview").attr("src", imageUrl);
        $("#qidImageModalLabel").text("QID Document - " + qid);
        $("#qidImageModal").modal("show");
    });

    // Participant Name Click Handler
    $("body").on("click", ".participant-name-link", function (e) {
        e.preventDefault();
        console.log("Participant name link clicked");
        var participantId = $(this).data("participant-id");
        console.log("Participant ID:", participantId);
        
        // Show loading state
        $("#participantDetailsModalLabel").text("Loading...");
        $("#participantDetailsModal").modal("show");
        
        // Fetch participant details
        $.ajax({
            url: "/ypi/customer/guardian/" + participantId + "/details",
            method: "GET",
            dataType: "json",
            success: function (response) {
                console.log("Participant details:", response);
                var p = response.participant;
                
                // Update modal title
                $("#participantDetailsModalLabel").text("Participant Details - " + p.full_name);
                
                // Update status badge
                $("#detail-status").text(p.status).removeClass().addClass("badge badge-phoenix fs-1 badge-phoenix-" + p.status_color);
                
                // Update all fields
                $("#detail-full-name").text(p.full_name || "-");
                $("#detail-qid").text(p.qid || "-");
                $("#detail-dob").text(p.date_of_birth || "-");
                $("#detail-gender").text(p.gender || "-");
                $("#detail-nationality").text(p.nationality || "-");
                $("#detail-school").text(p.school_name || "-");
                $("#detail-event").text(p.event || "-");
                $("#detail-type").text(p.participant_type || "-");
                $("#detail-venue").text(p.assigned_venue || "-");
                $("#detail-match").text(p.assigned_match || "-");
                
                // Sizes
                $("#detail-pants").text(p.pants_size || "-");
                $("#detail-jersey").text(p.jersey_size || "-");
                $("#detail-jacket").text(p.jacket_size || "-");
                $("#detail-shoe").text(p.shoe_size || "-");
                
                // Medical
                $("#detail-food-allergy").text(p.food_allergy || "-");
                var allergyType = p.food_allergy_type || "";
                if (allergyType === "Others" && p.food_allergy_others) {
                    allergyType += " - " + p.food_allergy_others;
                }
                $("#detail-food-allergy-type").text(allergyType);
                $("#detail-health-issues").text(p.health_issues || "-");
                $("#detail-health-issues-details").text(p.health_issues_details || "");
                
                // Guardian
                $("#detail-guardian-name").text(p.guardian_name || "-");
                $("#detail-guardian-email").text(p.guardian_email || "-");
                $("#detail-guardian-phone").text(p.guardian_phone || "-");
            },
            error: function (xhr) {
                console.error("Error fetching participant details:", xhr);
                $("#participantDetailsModalLabel").text("Error Loading Details");
                if (window.toastr) {
                    toastr.error("Failed to load participant details.");
                }
            }
        });
    });

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

    const fpAdd = document.querySelector("#add_date_of_birth")._flatpickr;

    fpAdd.config.onChange.push(function (selectedDates) {
        if (!selectedDates.length) return;

        console.log("selectedDates", selectedDates);
        const age = calculateAge(selectedDates[0]);
        console.log("age", age);
        $("#add_participant_age").val(age);
    });

    const fpEdit = document.querySelector("#edit_date_of_birth")._flatpickr;

    fpEdit.config.onChange.push(function (selectedDates) {
        if (!selectedDates.length) return;

        console.log("selectedDates", selectedDates);
        const age = calculateAge(selectedDates[0]);
        console.log("age", age);
        $("#edit_participant_age").val(age);
    });

    $("body").on("click", "#offcanvas-add-participant", function () {
        console.log("inside #offcanvas-add-participant");
        // $("#add_edit_form").get(0).reset()
        // console.log(window.choices.removeActiveItems())
        $("#cover-spin").show();
        $("#offcanvas-add-participant-modal").offcanvas("show");
        $("#cover-spin").hide();
    });

    $("body").on("click", "#edit_participant_offcanvas", function () {
        console.log("inside #change_participant_status");
        $("#cover-spin").show();
        var id = $(this).data("id");
        var table = $(this).data("table");
        console.log("id", id);
        console.log("table", table);

        const input = document.querySelector("#edit_date_of_birth");
        const dobPicker = input._flatpickr;
        // const dobPicker = flatpickr("#edit_date_of_birth", {
        //     dateFormat: "Y-m-d",
        //     allowInput: true,
        // });

        $.ajax({
            url: "/ypi/customer/guardian/get/" + id,
            method: "GET",
            async: true,
            success: function (response) {
                const hasFoodAllergy = response.op.food_allergy == 1;
                const hasHealthIssues = response.op.health_issues == 1;
                const dob = response.op.date_of_birth;

                console.log("dob", dob);

                if (dob) {
                    dobPicker.setDate(dob, true, "Y-m-d");
                } else {
                    dobPicker.clear();
                }

                $("#edit_participant_type").val(
                    response.op.participant_type_id
                );
                $("#edit_gender").val(response.op.gender_id);
                $("#edit_participant_id").val(response.op.id);
                $("#edit_participant_name").val(response.op.full_name);
                $("#edit_date_of_birth").val(response.op.date_of_birth_dmy);
                $("#edit_nationality").val(response.op.nationality_id);
                $("#edit_school_name").val(response.op.school_name);
                $("#edit_jacket_size").val(response.op.jacket_size_id);
                $("#edit_shoe_size").val(response.op.shoe_size_id);
                $("#edit_pants_size").val(response.op.pants_size_id);
                $("#edit_jersey_size").val(response.op.jersey_size_id);
                $("#edit_qid").val(response.op.qid);

                $("#edit_has_food_allergy").val(response.op.food_allergy);
                $("#edit_has_food_allergy").prop("checked", hasFoodAllergy);
                $("#edit_food_allergy_details_wrap").toggleClass(
                    "d-none",
                    !hasFoodAllergy
                );
                $("#edit_food_allergy_details").val(
                    response.op.food_allergy_details
                );

                $("#edit_has_health_issues").val(response.op.health_issues);
                $("#edit_has_health_issues").prop("checked", hasHealthIssues);
                $("#edit_health_issues_details").val(
                    response.op.health_issues_details
                );
                $("#edit_health_issues_details_wrap").toggleClass(
                    "d-none",
                    !hasHealthIssues
                );

                $("#edit_participant_table").val(table);

                // ✅ preload docs via EventPond
                console.log("Window EventPondEdit:", window.EventPondEdit);
                if (window.EventPondEdit) {
                    console.log(
                        "Preloading docs into EventPondEdit:",
                        response.event_docs
                    );
                    window.EventPondEdit.preload(response.event_docs);
                }

                $("#offcanvas-edit-participant-modal").offcanvas("show");
                $("#cover-spin").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
                $("#cover-spin").hide();
            },
        });
    });

    document
        .getElementById("has_food_allergy")
        .addEventListener("change", function () {
            document
                .getElementById("food_allergy_details_wrap")
                .classList.toggle("d-none", !this.checked);
        });

    document
        .getElementById("edit_has_food_allergy")
        .addEventListener("change", function () {
            document
                .getElementById("edit_food_allergy_details_wrap")
                .classList.toggle("d-none", !this.checked);
        });

    document
        .getElementById("has_health_issues")
        .addEventListener("change", function () {
            document
                .getElementById("health_issues_details_wrap")
                .classList.toggle("d-none", !this.checked);
        });

    document
        .getElementById("edit_has_health_issues")
        .addEventListener("change", function () {
            document
                .getElementById("edit_health_issues_details_wrap")
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
                    url: "/ypi/customer/guardian/delete/" + id,
                    type: "DELETE",
                    headers: {
                        // "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"),
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
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
});
