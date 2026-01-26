$(document).ready(function () {
    // console.log("all tasksJS file");

    // ************************************************** task venues


    $("body").on("click", "#offcanvas-add-flight", function () {
        console.log("inside #offcanvas-add-flight");
        // $("#add_edit_form").get(0).reset()
        // console.log(window.choices.removeActiveItems())
        $("#cover-spin").show();
        $("#offcanvas-add-flight-modal").offcanvas("show");
        $("#cover-spin").hide();
    });

    $("body").on("click", "#edit_guest_offcanv", function () {
        console.log("inside #edit_guest_offcanv");
        $("#cover-spin").show();
        var id = $(this).data("id");
        var table = $(this).data("table");
        console.log("id", id);
        console.log("table", table);
        $.ajax({
            url: "/gms/admin/guest/mv/get/" + id,
            method: "GET",
            async: true,
            success: function (response) {
                g_response = response.view;
                $("#global-edit-guest-content")
                    .empty("")
                    .append(g_response);
                $("#edit_guest_table").val(table);
                $("#offcanvas-edit-guest-modal").offcanvas("show");
                $("#cover-spin").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
                $("#cover-spin").hide();
            },
        });
    });


    // delete guest
    $("body").on("click", "#deleteGuest", function (e) {
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
                    url: "/gms/admin/guest/delete/" + id,
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



