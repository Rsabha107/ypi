$(document).ready(function () {
    // $("#find_fa_requests").focus();

    // $("#print_receipt_btn").prop("disabled", true);

    $(".js-example-basic-single").select2({
        // dropdownParent: $('#form-1'),
        width: "100%",
        placeholder: "Select an option",
        allowClear: true,

        // theme: 'bootstrap-5'
    });

    function getSelectedIds() {
        let selected = $("#bookings_table").bootstrapTable("getSelections");
        return selected.map((r) => r.id);
    }

    $("#print_receipt_btn").on("click", function () {
        let fa_id = $("#rfc_request_filter").val();
        // console.log("Functional Area ID:", fa_id);
        let ids = getSelectedIds();

        if (ids.length === 0) {
            alert("Please select at least one row.");
            return;
        }

        console.log("Selected IDs:", ids);
        console.log("Functional Area ID:", fa_id);

        // Create a hidden form for POST
        var form = $("<form>", {
            method: "POST",
            action: "/generate-pdf",
            target: "_blank",
        });

        // Add CSRF token
        form.append(
            $("<input>", {
                type: "hidden",
                name: "_token",
                value: $('meta[name="csrf-token"]').attr("content"),
            })
        );

        // Add Functional Area ID
        form.append(
            $("<input>", {
                type: "hidden",
                name: "fa_id",
                value: fa_id,
            })
        );

        // Add ids[]
        ids.forEach(function (id) {
            form.append(
                $("<input>", {
                    type: "hidden",
                    name: "ids[]",
                    value: id,
                })
            );
        });

        $("body").append(form);
        form.submit();
    });

    $("#mark_as_collected_btn").on("click", function (e) {
        e.preventDefault();
        // console.log("Print Receipt clicked");
        let fa_id = $("#rfc_request_filter").val();
        // console.log("Functional Area ID:", fa_id);
        let rfc_ids = getSelectedIds();
        if (rfc_ids.length === 0) {
            toastr.warning("No bookings selected.");
            return;
        }
        console.log("Selected IDs:", rfc_ids);
        console.log("Functional Area ID:", fa_id);
        // console.log("join ids:", ids.join(","));

        // let rfc_ids = ids.join(",");
        $("#cover-spin").show();
        $.ajax({
            url: "/mark-as-collected/",
            method: "POST",
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
                fa_id: fa_id,
                ids: rfc_ids,
            },
            dataType: "json",
            // async: true,
            success: function (response) {
                if (response.error) {
                    toastr.error(response.message || "An error occurred.");
                } else {
                    toastr.success(
                        response.message || "Marked as collected successfully."
                    );
                    // Optionally, you can refresh the table or perform other actions here
                    $("#bookings_table").bootstrapTable("refresh");
                }
                $("#cover-spin").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                toastr.error("An error occurred while marking as collected.");
                console.log(xhr.status);
                console.log(thrownError);
                $("#cover-spin").hide();
            },
        });
        $("#cover-spin").hide();
    });
});

function toggleBulkButton() {
    let selected = $("#bookings_table").bootstrapTable("getSelections");
    $("#print_receipt_btn").prop("disabled", selected.length === 0);
    $("#mark_as_collected_btn").prop("disabled", selected.length === 0);
}

$("#bookings_table").on(
    "check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table",
    function () {
        toggleBulkButton();
    }
);

let selections = [];

$("#bookings_table")
    .on("check.bs.table", function (e, row) {
        // Single row checked
        selections.push(row.fa_id);
        console.log("Checked:", row);
    })
    .on("uncheck.bs.table", function (e, row) {
        // Single row unchecked
        selections = selections.filter((id) => id !== row.id);
        console.log("Unchecked:", row);
    })
    .on("check-all.bs.table", function (e, rows) {
        // All rows checked
        rows.forEach((r) => {
            if (!selections.includes(r.id)) selections.push(r.id);
        });
        console.log("All checked:", rows);
    })
    .on("uncheck-all.bs.table", function (e, rows) {
        // All rows unchecked
        rows.forEach((r) => {
            selections = selections.filter((id) => id !== r.id);
        });
        console.log("All unchecked:", rows);
    });

$("#rfc_request_filter").on("change", function (e) {
    e.preventDefault();
    // console.log("tasks.js on change");
    $("#bookings_table").bootstrapTable("refresh");
});

$("body").on("click", "#rfc-request-status", function () {
    console.log("Change request status clicked");
    $("#cover-spin").show();
    var id = $(this).data("id");
    var tableID = $(this).data("table");
    // console.log("inside #payroll_timesheet_check " + id);

    $.ajax({
        url: "/vapp/operator/rfc/status/",
        method: "POST",
        data: {
            _token: $("meta[name='csrf-token']").attr("content"),
            id: id,
        },
        dataType: "json",
        async: true,
        success: function (response) {
            toastr.success(response["message"]);
            $("#" + tableID).bootstrapTable("refresh");
            $("#cover-spin").hide();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // console.log(xhr.status);
            // console.log(thrownError);
            $("#cover-spin").hide();
        },
    });
    $("#cover-spin").hide();
});
