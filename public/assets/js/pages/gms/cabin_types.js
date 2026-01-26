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
                    $("#create_cabin_types_modal").modal("hide");
                    $("#cabin_type").bootstrapTable("refresh"); // refresh your table if exists
                    form[0].reset(); // clear form
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                toastr.error("Something went wrong while creating cabin_type.");
            }
        });
    });

 // ================= Edit cabin_type =================
$("body").on("click", "#editHosted_by", function () {
    var id = $(this).data("id");

    $.ajax({
        url: "/gms/setting/cabin_type/get/" + id,
        type: "GET",
        dataType: "json",
        success: function(response) {
            if(response.op) {
                $("#edit_cabin_types_id").val(response.op.id);
                $("#edit_cabin_types_cabin_name").val(response.op.cabin_name);
                $("#edit_cabin_types_description").val(response.op.description);
            } else {
                toastr.error("cabin_type data not found.");
            }
        },
        error: function(xhr) {
            console.log(xhr.responseText);
            toastr.error("Failed to fetch cabin_type data.");
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
                $("#edit_cabin_types_modal").modal("hide");
                $("#cabin_types_table").bootstrapTable("refresh"); // updates table without page reload
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
        `<a href="javascript:void(0);" id="editCabin_type" data-id="${row.id}" title="${label_update}" class="text-primary mx-1">
            <i class="bx bx-edit"></i>
        </a>`,
        `<button type="button" id="deleteCabin_type" data-id="${row.id}" title="${label_delete}" class="btn btn-link text-danger p-0 mx-1">
            <i class="bx bx-trash"></i>
        </button>`
    ].join('');
}

