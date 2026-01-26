$(document).ready(function () {
    // console.log("all tasksJS file");

    // ************************************************** task venues

    $("body").on("click", "#editApplicationSetting", function () {
        console.log('inside editApplicationSetting')
        var id = $(this).data("id");
        var table = $(this).data("table");
        // console.log('edit venues in venues.js');
        // console.log('id: '+id);
        // console.log('table: '+table);
        // var target = document.getElementById("edit_venues_modal");
        // var spinner = new Spinner().spin(target);
        // $("#edit_venues_modal").modal("show");
        $.ajax({
            url: "ypi/setting/application/get/" + id,
            type: "get",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').attr("value"), // Replace with your method of getting the CSRF token
            },
            dataType: "json",
            success: function (response) {
                console.log(response)
                $("#edit_app_setting_id").val(response.op.id);
                $("#edit_key").val(response.op.key);
                $("#edit_value").val(response.op.value);
                $("#edit_app_setting_table").val(table);
                // $("#edit_rsp_modal").modal("show");
            },
        }).done(function () {
            $("#edit_app_setting_modal").modal("show");
        });
    });
});


("use strict");
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

window.icons = {
    refresh: "bx-refresh",
    toggleOn: "bx-toggle-right",
    toggleOff: "bx-toggle-left",
    fullscreen: "bx-fullscreen",
    columns: "bx-list-ul",
    export_data: "bx-list-ul",
};

function loadingTemplate(message) {
    return '<i class="bx bx-loader-alt bx-spin bx-flip-vertical" ></i>';
}

