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
                    $("#create_hosted_bys_modal").modal("hide");
                    $("#hosted_by_table").bootstrapTable("refresh"); // refresh your table if exists
                    form[0].reset(); // clear form
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                toastr.error("Something went wrong while creating Hosted By.");
            }
        });
    });

    // ================= Edit hosted_by =================
    $("body").on("click", "#editHosted_by", function () {
        var id = $(this).data("id");
        var table = $(this).data("table");

        $.ajax({
            url: "/gms/setting/hosted_by/get/" + id,
            type: "GET",
            headers: { "X-CSRF-TOKEN": $('input[name="_token"]').val() },
            dataType: "json",
            success: function (response) {
                $("#edit_hosted_bys_id").val(response.op.id);
                $("#edit_hosted_bys_title").val(response.op.title);
                $("#edit_hosted_bys_table").val(table);

                $("#edit_hosted_bys_modal").modal("show");
            },
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
                    $("#edit_hosted_bys_modal").modal("hide");
                    $("#hosted_bys_table").bootstrapTable("refresh");
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

    // ================= Delete hosted_by =================
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
                    url: "/gms/setting/hosted_by/delete/" + id,
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
        '<a href="javascript:void(0);" id="editHosted_by" data-id="' + row.id + '" data-table="hosted_bys_table" title="' + label_update + '"><i class="bx bx-edit mx-1"></i></a>',
        '<button type="button" id="deleteHosted_by" data-id="' + row.id + '" data-table="hosted_bys_table" title="' + label_delete + '" class="btn"><i class="bx bx-trash text-danger mx-1"></i></button>'
    ].join("");
}
