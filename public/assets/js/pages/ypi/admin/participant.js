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

    $("body").on("click", "#offcanvas-add-participant", function () {
        console.log("inside #offcanvas-add-participant");
        $("#cover-spin").show();
        $("#offcanvas-add-participant-modal").offcanvas("show");
        $("#cover-spin").hide();
    });

    $("body").on("click", "#ypiUploadCertificate", function () {
        console.log("inside #ypiUploadCertificate");
        $("#cover-spin").show();
        var $participantId = $(this).data("id");
        var tableID = $(this).data("table");
        console.log("participant id for cert upload: ", $participantId);
        $("#participantIdForCert").val($participantId);
        $("#ypiUploadCertModal").data("table", tableID);
        $("#ypiUploadCertModal").modal("show");
        $("#cover-spin").hide();
    });

    $(document).on("click", ".js-remove-cert", function () {
        console.log("inside .js-remove-cert");
        var docId = $(this).data("doc-id");
        var tableID = $(this).data("table");

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
                    url: "/ypi/admin/participant/certificate/delete/" + docId,
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

        // if (!confirm("Remove certificate?")) return;

        // $.ajax({
        //     url: "/ypi/admin/participant/certificate/delete/" + docId, // adjust if route differs
        //     type: "DELETE",
        //     headers: {
        //         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        //     },
        //     success: function () {
        //         toastr.success("Certificate removed");
        //         location.reload(); // or redraw your table
        //     },
        //     error: function (xhr) {
        //         toastr.error(
        //             xhr.responseJSON?.message || "Failed to remove certificate",
        //         );
        //     },
        // });
    });

    $("body").on("click", "#change_participant_status", function () {
        console.log("inside #change_participant_status");
        $("#cover-spin").show();
        var id = $(this).data("id");
        var status_id = $(this).data("status_id");
        var event_id = $(this).data("event_id");
        var table = $(this).data("table");
        console.log("id", id);
        console.log("event_id", event_id);
        console.log("table", table);
        $("#participant_id").val(id);
        $("#editStatusSelection").val(status_id);
        
        // Store event_id for later use when loading matches
        $("#venue_id").data("event_id", event_id);
        
        // Load venues filtered by event
        loadVenuesByEvent(event_id);
        
        $("#change-participant-status-modal").modal("show");
        $("#cover-spin").hide();
    });


    const $venue = $('#venue_id');
    const $match = $('#match_id');

    function resetVenues(placeholder = 'Select') {
        $venue
            .html('<option value="">' + placeholder + '</option>');
        resetMatches('Select');
    }

    function resetMatches(placeholder = 'Select') {
        $match
            .prop('disabled', true)
            .html('<option value="">' + placeholder + '</option>');
    }

    function loadVenuesByEvent(eventId) {
        if (!eventId) {
            resetVenues('Select');
            return;
        }

        resetVenues('Loading...');

        const url = "/events/{event_id}/venues".replace('{event_id}', eventId);

        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function (venues) {
                console.log('Venues loaded:', venues);
                let html = '<option value="">Select</option>';

                $.each(venues, function (_, venue) {
                    html += '<option value="' + venue.id + '">' + venue.title + '</option>';
                });

                $venue.html(html);
            },
            error: function () {
                resetVenues('Select');
                if (window.toastr) toastr.error('Failed to load venues for this event.');
            }
        });
    }

    function loadMatchesByVenue(venueId, preselectId = null) {
        if (!venueId) {
            resetMatches('Select');
            return;
        }

        resetMatches('Loading...');

        // Get event_id from stored data
        const eventId = $('#venue_id').data('event_id');
        
        // Build URL with both venue and event
        let url;
        if (eventId) {
            url = "/venues/{venue_id}/events/{event_id}/matches"
                .replace('{venue_id}', venueId)
                .replace('{event_id}', eventId);
        } else {
            // Fallback to venue-only if no event (shouldn't happen)
            url = "/venues/{venue_id}/matches".replace('{venue_id}', venueId);
        }

        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function (items) {
                console.log('Matches loaded:', items);
                let html = '<option value="">Select</option>';

                $.each(items, function (_, it) {
                    html += '<option value="' + it.id + '">' + it.text + '</option>';
                });

                $match.html(html).prop('disabled', false);

                // Edit mode preselect
                if (preselectId) {
                    $match.val(preselectId).trigger('change');
                }
            },
            error: function () {
                resetMatches('Select');
                if (window.toastr) toastr.error('Failed to load matches for this venue.');
            }
        });
    }

    // When venue changes, reload matches
    // $("ody").on("change", "#venue_id", function () {
    //     const venueId = $(this).val();
    //     console.log("Selected venue ID:", venueId);
    //     // if you store existing selected match in data-selected
    // });

    $venue.on('change', function () {
        console.log("Selected venue ID:", $(this).val());
        const venueId = $(this).val();
        // if you store existing selected match in data-selected
        const preselectId = $match.data('selected') || null;

        // Clear old selection before loading
        $match.removeData('selected');

        loadMatchesByVenue(venueId, preselectId);
    });

    // Initial load (edit mode / modal opened with existing values)
    const initialVenueId = $venue.val();
    const initialMatchId = $match.data('selected') || $match.val() || null;

    if (initialVenueId) {
        loadMatchesByVenue(initialVenueId, initialMatchId);
    } else {
        resetMatches('Select');
    }

    // Edit participant handler
    $("body").on("click", "#edit_guest_offcanv", function () {
        console.log("inside #edit_guest_offcanv");
        $("#cover-spin").show();
        var id = $(this).data("id");
        var table = $(this).data("table");
        console.log("id", id);
        console.log("table", table);
        $.ajax({
            url: "/ypi/admin/participant/mv/get/" + id,
            method: "GET",
            async: true,
            success: function (response) {
                var g_response = response.view;
                $("#global-edit-participant-content")
                    .empty("")
                    .append(g_response);
                $("#edit_participant_table").val(table);
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




    // $("body").on("click", "#edit_participant_offcanvas", function () {
    //     console.log("inside #edit_participant_offcanvas");
    //     $("#cover-spin").show();
    //     var id = $(this).data("id");
    //     var table = $(this).data("table");
    //     console.log("id", id);
    //     console.log("table", table);

    //     const input = document.querySelector("#edit_date_of_birth");
    //     const dobPicker = input._flatpickr;
    //     // const dobPicker = flatpickr("#edit_date_of_birth", {
    //     //     dateFormat: "Y-m-d",
    //     //     allowInput: true,
    //     // });

    //     $.ajax({
    //         url: "/ypi/customer/guardian/get/" + id,
    //         method: "GET",
    //         async: true,
    //         success: function (response) {
    //             const hasFoodAllergy = response.op.food_allergy == 1;
    //             const hasHealthIssues = response.op.health_issues == 1;
    //             const dob = response.op.date_of_birth;

    //             console.log("dob", dob);

    //             if (dob) {
    //                 dobPicker.setDate(dob, true, "Y-m-d");
    //             } else {
    //                 dobPicker.clear();
    //             }

    //             $("#edit_participant_type").val(
    //                 response.op.participant_type_id,
    //             );
    //             $("#edit_gender").val(response.op.gender_id);
    //             $("#edit_participant_id").val(response.op.id);
    //             $("#edit_participant_name").val(response.op.full_name);
    //             $("#edit_date_of_birth").val(response.op.date_of_birth_dmy);
    //             $("#edit_nationality").val(response.op.nationality_id);
    //             $("#edit_school_name").val(response.op.school_name);
    //             $("#edit_jacket_size").val(response.op.jacket_size_id);
    //             $("#edit_shoe_size").val(response.op.shoe_size_id);
    //             $("#edit_pants_size").val(response.op.pants_size_id);
    //             $("#edit_jersey_size").val(response.op.jersey_size_id);
    //             $("#edit_qid").val(response.op.qid);

    //             $("#edit_has_food_allergy").val(response.op.food_allergy);
    //             $("#edit_has_food_allergy").prop("checked", hasFoodAllergy);
    //             $("#edit_food_allergy_details_wrap").toggleClass(
    //                 "d-none",
    //                 !hasFoodAllergy,
    //             );
    //             $("#edit_food_allergy_details").val(
    //                 response.op.food_allergy_details,
    //             );

    //             $("#edit_has_health_issues").val(response.op.health_issues);
    //             $("#edit_has_health_issues").prop("checked", hasHealthIssues);
    //             $("#edit_health_issues_details").val(
    //                 response.op.health_issues_details,
    //             );
    //             $("#edit_health_issues_details_wrap").toggleClass(
    //                 "d-none",
    //                 !hasHealthIssues,
    //             );

    //             $("#edit_participant_table").val(table);

    //             // ✅ preload docs via EventPond
    //             console.log("Window EventPondEdit:", window.EventPondEdit);
    //             if (window.EventPondEdit) {
    //                 console.log(
    //                     "Preloading docs into EventPondEdit:",
    //                     response.event_docs,
    //                 );
    //                 window.EventPondEdit.preload(response.event_docs);
    //             }

    //             $("#change-participant-status-modal").modal("show");
    //             $("#cover-spin").hide();
    //         },
    //         error: function (xhr, ajaxOptions, thrownError) {
    //             console.log(xhr.status);
    //             console.log(thrownError);
    //             $("#cover-spin").hide();
    //         },
    //     });
    // });

    // document
    //     .getElementById("has_food_allergy")
    //     .addEventListener("change", function () {
    //         document
    //             .getElementById("food_allergy_details_wrap")
    //             .classList.toggle("d-none", !this.checked);
    //     });

    // document
    //     .getElementById("edit_has_food_allergy")
    //     .addEventListener("change", function () {
    //         document
    //             .getElementById("edit_food_allergy_details_wrap")
    //             .classList.toggle("d-none", !this.checked);
    //     });

    // document
    //     .getElementById("has_health_issues")
    //     .addEventListener("change", function () {
    //         document
    //             .getElementById("health_issues_details_wrap")
    //             .classList.toggle("d-none", !this.checked);
    //     });

    // document
    //     .getElementById("edit_has_health_issues")
    //     .addEventListener("change", function () {
    //         document
    //             .getElementById("edit_health_issues_details_wrap")
    //             .classList.toggle("d-none", !this.checked);
    //     });

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
});


