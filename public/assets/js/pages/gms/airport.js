"use strict";

$(document).ready(function () {

    // ================= Create Airport =================
    $("#form_submit_event").on("submit", function(e) {
        e.preventDefault(); 
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
                    $("#create_airports_modal").modal("hide");
                    $("#airports_table").bootstrapTable("refresh");
                    form[0].reset(); 
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                toastr.error("Something went wrong while creating airport.");
            }
        });
    });

    // ================= Edit Airport =================
    $("body").on("click", "#editAirport", function () {
        var id = $(this).data("id");
        var table = $(this).data("table"); // <-- declare 'table' here

        $.ajax({
            url: "/gms/setting/airport/get/" + id,
            type: "GET",
            dataType: "json",
            success: function(response) {
                if(response.op) {
                    $("#edit_airports_id").val(response.op.id);
                    $("#edit_airports_airport_name").val(response.op.airport_name);
                    $("#edit_airports_airport_code").val(response.op.airport_code);
                    $("#edit_airports_city").val(response.op.city);
                    $("#edit_airports_country").val(response.op.country);
                    $("#edit_airports_table").val(table);
                    $("#edit_airports_modal").modal("show");
                } else {
                    toastr.error("airport data not found.");
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                toastr.error("Failed to fetch airport data.");
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
            headers: { "X-CSRF-TOKEN": $('input[name="_token"]').val() },
            success: function (response) {
                if (!response.error) {
                    toastr.success(response.message);
                    $("#edit_airports_modal").modal("hide");
                    $("#airports_table").bootstrapTable("refresh");
                    form[0].reset();
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

    // ================= Delete Airport =================
    $("body").on("click", "#deleteAirport", function (e) {
        e.preventDefault();
        var id = $(this).data("id");
        var tableID = $(this).data("table"); // <-- get table from element

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
                    url: "/gms/setting/airport/delete/" + id,
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
        `<a href="javascript:void(0);" id="editAirport" data-id="${row.id}" data-table="airports_table" title="${label_update}" class="text-primary mx-1">
            <i class="bx bx-edit"></i>
        </a>`,
        `<button type="button" id="deleteAirport" data-id="${row.id}" data-table="airports_table" title="${label_delete}" class="btn btn-link text-danger p-0 mx-1">
            <i class="bx bx-trash"></i>
        </button>`
    ].join('');
}
