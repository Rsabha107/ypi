"use strict";

$(document).ready(function () {

// Create Hosted By
    $("#form_submit_event").on("submit", function(e) {
        e.preventDefault(); // prevent default form submission
        var form = $(this);

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: form.serialize(),
            headers: { "X-CSRF-TOKEN": $('input[name="_token"]').val() },
            dataType: "json",
            success: function(response) {
                if (!response.error) {
                    toastr.success(response.message);
                    $("#create_airlines_modal").modal("hide");
                    $("#airline_table").bootstrapTable("refresh"); // refresh your table if exists
                    form[0].reset(); // clear form
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                toastr.error("Something went wrong while creating Airline.");
            }
        });
    });

 // ================= Edit airline =================
$("body").on("click", "#editHosted_by", function () {
    var id = $(this).data("id");

    $.ajax({
        url: "/gms/setting/airline/get/" + id,
        type: "GET",
        dataType: "json",
        success: function(response) {
            if(response.op) {
                $("#edit_airlines_id").val(response.op.id);
                $("#edit_airlines_name").val(response.op.name);
                $("#edit_airlines_carrier_code").val(response.op.carrier_code);
                $("#edit_airlines_country").val(response.op.country);
                $("#edit_airlines_iata_code").val(response.op.iata_code);
                $("#edit_airlines_icao_code").val(response.op.icao_code);
                $("#edit_airlines_founded_year").val(response.op.founded_year);
                $("#edit_airlines_website").val(response.op.website);
                $("#edit_airlines_modal").modal("show");
            } else {
                toastr.error("Airline data not found.");
            }
        },
        error: function(xhr) {
            console.log(xhr.responseText);
            toastr.error("Failed to fetch airline data.");
        }
    });
});


// ================= Submit Edit Form =================
$("#edit_form_submit_event").on("submit", function (e) {
    e.preventDefault();
    var form = $(this);

    $.ajax({
        url: form.attr("action"),
        type: "POST",
        data: form.serialize(),
        dataType: "json",
        success: function (response) {
            if (!response.error) {
                toastr.success(response.message);
                $("#edit_airlines_modal").modal("hide");
                $("#airlines_table").bootstrapTable("refresh"); // updates table without page reload
            } else {
                toastr.error(response.message);
            }
        },
        error: function (xhr) {
            console.log(xhr.responseText);
            toastr.error("Something went wrong while updating.");
        }
    });
});


    // ================= Delete airline =================
    $("body").on("click", "#deleteHosted_by", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
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
                $.ajax({
                    url: "/gms/setting/airline/delete/" + id,
                    type: "DELETE",
                    headers: { "X-CSRF-TOKEN": $('input[name="_token"]').val() },
                    dataType: "json",
                    success: function (result) {
                        if (!result.error) {
                            toastr.success(result.message);
                            $("#" + tableID).bootstrapTable("refresh");
                        } else {
                            toastr.error(result.message);
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        toastr.error("Something went wrong while deleting.");
                    },
                });
            }
        });
    });

});

// ================= Bootstrap Table Query Params =================
function queryParams(p) {
    return {
        page: p.offset / p.limit + 1,
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
    };
}

// ================= Icons =================
window.icons = {
    refresh: "bx-refresh",
    toggleOn: "bx-toggle-right",
    toggleOff: "bx-toggle-left",
    fullscreen: "bx-fullscreen",
    columns: "bx-list-ul",
    export_data: "bx-list-ul",
};

// ================= Loading Template =================
function loadingTemplate(message) {
    return '<i class="bx bx-loader-alt bx-spin bx-flip-vertical"></i>';
}

// ================= Actions Formatter =================
function actionsFormatter(value, row, index) {
    return [
        `<a href="javascript:void(0);" id="editHosted_by" data-id="${row.id}" title="${label_update}" class="text-primary mx-1">
            <i class="bx bx-edit"></i>
        </a>`,
        `<button type="button" id="deleteHosted_by" data-id="${row.id}" title="${label_delete}" class="btn btn-link text-danger p-0 mx-1">
            <i class="bx bx-trash"></i>
        </button>`
    ].join('');
}

