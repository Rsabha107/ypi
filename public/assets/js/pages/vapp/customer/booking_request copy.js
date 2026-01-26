$(document).ready(function () {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-center", // top-right, top-center, bottom-left, etc.
        timeOut: "3000",
    };
    // initialize Choices.js for the VAPP Venue select element
    const vappVenueSelect = document.getElementById("add_var_venue_id");
    const vappVenueChoices = new Choices(vappVenueSelect, {
        searchEnabled: false,
        shouldSort: false,
        placeholder: true,
        itemSelectText: "",
        allowHTML: true,
        // placeholderValue: "Select VAPP Codes",
    });

    // initialize Choices.js for the VAPP Size select element
    const vappVappSizeSelect = document.getElementById("add_var_vapp_size_id");
    const vappVappSizeChoices = new Choices(vappVappSizeSelect, {
        searchEnabled: false,
        shouldSort: false,
        placeholder: true,
        itemSelectText: "",
        allowHTML: true,

        // placeholderValue: "Select VAPP Codes",
    });

    // initialize Choices.js for the Match select element
    const vappMatchSelect = document.getElementById("add_var_match_id");
    const vappMatchChoices = new Choices(vappMatchSelect, {
        searchEnabled: false,
        shouldSort: false,
        placeholder: true,
        itemSelectText: "",
        allowHTML: true,

        // placeholderValue: "Select VAPP Codes",
    });

    // initialize Choices.js for the Parking Code select element
    const vappParkingCodeSelect = document.getElementById("add_var_parking_id");
    const vappParkingCodeChoices = new Choices(vappParkingCodeSelect, {
        searchEnabled: false,
        shouldSort: false,
        placeholder: true,
        itemSelectText: "",
        allowHTML: true,

        // placeholderValue: "Select VAPP Codes",
    });

    // initialize Choices.js for the FunctionalArea select element
    const vappFunctionalAreaSelect = document.getElementById(
        "add_var_functional_area_id"
    );
    const vappFunctionalAreaChoices = new Choices(vappFunctionalAreaSelect, {
        searchEnabled: false,
        shouldSort: false,
        placeholder: true,
        itemSelectText: "",
        allowHTML: true,
    });

    // you will initialize the category choices here
    $("body").on("change", "#add_var_parking_id", function () {
        // Get the selected VAPP Code
        $("#cardSpinner").removeClass("d-none");
        var varParkingId = $(this).val();
        var varParkingText = $(this).find("option:selected").text();
        console.log("Selected VAPP Code:", varParkingId);
        // const selectedVappCode = vappVenueSelect.value;
        // $(".main-card").css("background-color", "rgba(52, 152, 219, 0.6)");
        $("#add_requested_vapp_a5").prop("disabled", true);
        $("#add_requested_vapp_a4").prop("disabled", true);
        $("#add_requested_vapp_20").prop("disabled", true);
        $("#add_requested_vapp_hanger").prop("disabled", true);

        if (varParkingId) {
            $.ajax({
                url: "/get-parking-color",
                type: "GET",
                data: {
                    parking_id: varParkingId,
                    // match_id: varMatchId,
                },
                success: function (data) {
                    console.log("data", data);
                    $(".main-card").css(
                        "background-color",
                        data.parking_color
                            ? data.parking_color
                            : "rgba(52, 152, 219, 0.6)"
                    );
                    console.log("Parking Code:", varParkingText);
                    $("#sub-card-parking-code").text(varParkingText);
                    $("#cardSpinner").addClass("d-none");
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                    toastr.error(thrownError);
                    $("#cardSpinner").addClass("d-none");
                },
            });
        } else {
            null;
            $("#cardSpinner").addClass("d-none");
        }
        // if (varParkingId) {
        //     $.ajax({
        //         url: "/get-catergory",
        //         type: "GET",
        //         data: {
        //             parking_id: varParkingId,
        //         },
        //         success: function (data) {
        //             console.log("data", data.variationCategory);
        //             $("#cover-spin").show();
        //             const vappCategoryOptions = data.variationCategory.map(
        //                 (item) => ({
        //                     value: item.match_category.id,
        //                     label: item.match_category.title,
        //                 })
        //             );

        //             vappCategoryChoices.clearStore();
        //             vappCategoryChoices.setChoices(
        //                 vappCategoryOptions,
        //                 "value",
        //                 "label",
        //                 true
        //             );
        //             $("#cover-spin").hide();
        //         },
        //         error: function () {
        //             vappCategoryChoices.clearStore();
        //             vappCategoryChoices.setChoices(
        //                 [
        //                     {
        //                         value: "",
        //                         label: "Failed to load venues",
        //                         disabled: true,
        //                     },
        //                 ],
        //                 "value",
        //                 "label",
        //                 true
        //             );
        //         },
        //     });
        // } else {
        //     vappCategoryChoices.clearStore();
        //     vappCategoryChoices.setChoices(
        //         [{ value: "", label: "Select a Category", disabled: false }],
        //         "value",
        //         "label",
        //         true
        //     );
        // }
    });

    // Handle the change event for the VAPP Category select element
    $("body").on("change", "#add_var_functional_area_id", function () {
        console.log("Functional Area Changed *******************************");
        $("#cardSpinner").removeClass("d-none");
        var varFaId = $(this).val();
        var varFaText = $(this).find("option:selected").text();
        console.log("Selected Fa Text:", varFaText);
        // var varMatchId = $(this).val();
        // var  = $("#match_Fa_id").val();
        var varParkingId = $("#add_var_parking_id").val();

        console.log("Selected Fa ID:", varFaId);
        console.log("Selected Parking ID:", varParkingId);
        // $("#add_requested_vapp_a5").prop("disabled", true);
        // $("#add_requested_vapp_a4").prop("disabled", true);
        // $("#add_requested_vapp_20").prop("disabled", true);
        // $("#add_requested_vapp_hanger").prop("disabled", true);

        if (varFaId) {
            $.ajax({
                url: "/get-pariking-by-fa",
                type: "GET",
                data: {
                    var_fa_id: varFaId,
                    // match_id: varMatchId,
                },
                success: function (data) {
                    console.log("data", data);
                    const vappParkingCodeOptions =
                        data.variationParkingCode.map((item) => ({
                            value: item.parking_id,
                            label: item.parking_code,
                            // selected: item.match_code === "ALL" ? true : false,
                        }));

                    vappParkingCodeChoices.clearStore();
                    vappParkingCodeChoices.setChoices(
                        vappParkingCodeOptions,
                        "value",
                        "label",
                        true
                    );
                    $("#cardSpinner").addClass("d-none");
                },
                error: function () {
                    vappParkingCodeChoices.clearStore();
                    vappParkingCodeChoices.setChoices(
                        [
                            {
                                value: "",
                                label: "Failed to load Parking Codes",
                                disabled: true,
                            },
                        ],
                        "value",
                        "label",
                        true
                    );
                    $("#cardSpinner").addClass("d-none");
                },
            });
        } else {
            vappParkingCodeChoices.clearStore();
            vappParkingCodeChoices.setChoices(
                [{ value: "", label: "Select a VAPP Code", disabled: false }],
                "value",
                "label",
                true
            );
            $("#cardSpinner").addClass("d-none");
        }
    });

    // Handle the change event for the VAPP Category select element
    $("body").on("change", "#add_var_category_id", function () {
        console.log("Category ID Changed");
        $("#cardSpinner").removeClass("d-none");

        var varCategoryId = $(this).val();
        var varCategoryText = $(this).find("option:selected").text();
        console.log("Selected Category Text:", varCategoryText);
        // var varMatchId = $(this).val();
        // var  = $("#match_category_id").val();
        var varParkingId = $("#add_var_parking_id").val();

        console.log("Selected Category ID:", varCategoryId);
        console.log("Selected Parking ID:", varParkingId);
        $("#add_requested_vapp_a5").prop("disabled", true);
        $("#add_requested_vapp_a4").prop("disabled", true);
        $("#add_requested_vapp_20").prop("disabled", true);
        $("#add_requested_vapp_hanger").prop("disabled", true);

        if (varCategoryId) {
            $.ajax({
                url: "/get-matches",
                type: "GET",
                data: {
                    parking_id: varParkingId,
                    match_category_id: varCategoryId,
                    // match_id: varMatchId,
                },
                success: function (data) {
                    console.log("data", data);
                    const vappMatchOptions = data.matches.map((item) => ({
                        value: item.id,
                        label: item.match_code_date_description,
                        selected: item.match_code === "ALL" ? true : false,
                    }));

                    vappMatchChoices.clearStore();
                    vappMatchChoices.setChoices(
                        vappMatchOptions,
                        "value",
                        "label",
                        true
                    );

                    if (data.variation_venues.length > 0) {
                        const vappVenueOptions = data.variation_venues.map(
                            (item) => ({
                                value: item.id,
                                label: item.title,
                            })
                        );

                        vappVenueChoices.clearStore();
                        vappVenueChoices.setChoices(
                            vappVenueOptions,
                            "value",
                            "label",
                            true
                        );
                    }
                    $("#cardSpinner").addClass("d-none");
                },
                error: function () {
                    vappMatchChoices.clearStore();
                    vappMatchChoices.setChoices(
                        [
                            {
                                value: "",
                                label: "Failed to load venues",
                                disabled: true,
                            },
                        ],
                        "value",
                        "label",
                        true
                    );
                    $("#cardSpinner").addClass("d-none");
                },
            });
        } else {
            vappMatchChoices.clearStore();
            vappMatchChoices.setChoices(
                [{ value: "", label: "Select a VAPP Sizes", disabled: false }],
                "value",
                "label",
                true
            );
            $("#cardSpinner").addClass("d-none");
        }
    });

    // Handle the change event for the VAPP Match select element
    $("body").on("change", "#add_var_match_id", function () {
        $("#cardSpinner").removeClass("d-none");
        var varMatchId = $(this).val();
        var varMatchText = $(this).find("option:selected").text();
        let varMatchTextSplit = varMatchText.split(" -")[0]; // split by " -" and take first element
        var varCategoryId = $("#add_var_category_id").val();
        var varParkingId = $("#add_var_parking_id").val();

        console.log("Selected Match ID:", varMatchId);

        if (varMatchId) {
            $.ajax({
                url: "/get-venues",
                type: "GET",
                data: {
                    parking_id: varParkingId,
                    match_id: varMatchId,
                    match_category_id: varCategoryId,
                },
                success: function (data) {
                    console.log("data", data);
                    const vappVenueOptions = data.matches.map((item) => ({
                        value: item.venue.id,
                        label: item.venue.title,
                    }));

                    vappVenueChoices.clearStore();
                    vappVenueChoices.setChoices(
                        vappVenueOptions,
                        "value",
                        "label",
                        true
                    );
                    $("#sub-card-match-day").text(varMatchTextSplit);
                    $("#cardSpinner").addClass("d-none");
                    // const vappFuncitonalAreaOptions = data.functional_areas.map(
                    //     (item) => ({
                    //         value: item.id,
                    //         label: item.title,
                    //     })
                    // );

                    // vappFunctionalAreaChoices.clearStore();
                    // vappFunctionalAreaChoices.setChoices(
                    //     vappFuncitonalAreaOptions,
                    //     "value",
                    //     "label",
                    //     true
                    // );
                },
                error: function () {
                    vappVenueChoices.clearStore();
                    vappVenueChoices.setChoices(
                        [
                            {
                                value: "",
                                label: "Failed to load venues",
                                disabled: true,
                            },
                        ],
                        "value",
                        "label",
                        true
                    );
                    $("#cardSpinner").addClass("d-none");
                },
            });
        } else {
            vappVenueChoices.clearStore();
            vappVenueChoices.setChoices(
                [{ value: "", label: "Select a Match", disabled: false }],
                "value",
                "label",
                true
            );
            $("#cardSpinner").addClass("d-none");
        }
    });

    $("#add_var_venue_id").on("change", function () {
        $("#cardSpinner").removeClass("d-none");
        var varMatchId = $("#add_var_category_id").val();
        var varVenueId = $("#add_var_venue_id").val();
        var varVenueText = $("#add_var_venue_id")
            .find("option:selected")
            .text();
        var varVenueTextSplit = varVenueText.split(" - ");
        var varParkingId = $("#add_var_parking_id").val();

        console.log("Selected Venue ID:", varVenueId);
        console.log("Selected Venue Text:", varVenueText);
        console.log("Selected Parking ID:", varParkingId);
        console.log("Selected Match Category ID:", varMatchId);
        if (varVenueId && varMatchId && varParkingId) {
            $.ajax({
                url: "/get-variation",
                method: "GET",
                data: {
                    parking_id: varParkingId,
                    venue_id: varVenueId,
                    match_category_id: varMatchId,
                },
                success: function (data) {
                    console.log("data", data);
                    $("#add_variation_id").val(data.variation.id);
                    const vappVappSizeOptions = data.variation.vapp_sizes.map(
                        (item) => ({
                            value: item.id,
                            label: item.title,
                            // selected: item.match_code === "ALL" ? true : false,
                        })
                    );

                    vappVappSizeChoices.clearStore();
                    vappVappSizeChoices.setChoices(
                        vappVappSizeOptions,
                        "value",
                        "label",
                        true
                    );
                    $("#sub-card-venue").text(varVenueTextSplit[0] || "");
                    $("#cardSpinner").addClass("d-none");
                },
                error: function () {
                    vappVappSizeChoices.clearStore();
                    vappVappSizeChoices.setChoices(
                        [
                            {
                                value: "",
                                label: "Failed to load sizes",
                                disabled: true,
                            },
                        ],
                        "value",
                        "label",
                        true
                    );
                    $("#cardSpinner").addClass("d-none");
                },
            });
        } else {
            vappVenueChoices.clearStore();
            vappVenueChoices.setChoices(
                [{ value: "", label: "Select a Venue", disabled: false }],
                "value",
                "label",
                true
            );
            $("#cardSpinner").addClass("d-none");
        }
    });
});
