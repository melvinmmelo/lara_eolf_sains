// Function to populate the Province dropdown based on the selected region
function editPopulateProvinceDropdown(regionId) {
    $("#e_province").empty();

    // Add a blank option as the first option
    $("#e_province").append('<option value="">Please select</option>');
    $.ajax({
        type: "GET",
        url: "/get-provinces/" + regionId, // Route to fetch provinces based on region
        success: function (response) {
            // $('#e_province').empty(); // Clear existing options
            $.each(response, function (key, value) {
                $("#e_province").append(
                    '<option value="' +
                        value.code +
                        '">' +
                        value.name +
                        "</option>"
                );
            });
        },
    });
}

function editPopulateProvinceDropdown2(regionId) {
    $("#e_province2").empty();

    // Add a blank option as the first option
    $("#e_province2").append('<option value="">Please select</option>');
    $.ajax({
        type: "GET",
        url: "/get-provinces/" + regionId, // Route to fetch provinces based on region
        success: function (response) {
            // $('#cust_prov').empty(); // Clear existing options
            $.each(response, function (key, value) {
                $("#e_province2").append(
                    '<option value="' +
                        value.code +
                        '">' +
                        value.name +
                        "</option>"
                );
            });
        },
    });
}

// Function to populate the City dropdown based on the selected province
function editPopulateCityDropdown(provinceId) {
    $("#e_city").empty();

    // Add a blank option as the first option
    $("#e_city").append('<option value="">Please select</option>');
    $.ajax({
        type: "GET",
        url: "/get-cities/" + provinceId, // Route to fetch cities based on province
        success: function (response) {
            // $('#e_city').empty(); // Clear existing options
            $.each(response, function (key, value) {
                $("#e_city").append(
                    '<option value="' +
                        value.code +
                        '">' +
                        value.name +
                        "</option>"
                );
            });
        },
    });
}

function editPopulateCityDropdown2(provinceId) {
    $("#e_city2").empty();

    // Add a blank option as the first option
    $("#e_city2").append('<option value="">Please select</option>');
    $.ajax({
        type: "GET",
        url: "/get-cities/" + provinceId, // Route to fetch cities based on province
        success: function (response) {
            // $('#cust_city').empty(); // Clear existing options
            $.each(response, function (key, value) {
                $("#e_city2").append(
                    '<option value="' +
                        value.code +
                        '">' +
                        value.name +
                        "</option>"
                );
            });
        },
    });
}

function editPopulateBrgyDropdown(cityId) {
    $("#e_brgy").empty();

    // Add a blank option as the first option
    $("#e_brgy").append('<option value="">Please select</option>');
    $.ajax({
        type: "GET",
        url: "/get-brgy/" + cityId, // Route to fetch cities based on city/mun
        success: function (response) {
            // $('#e_brgy').empty(); // Clear existing options
            $.each(response, function (key, value) {
                $("#e_brgy").append(
                    '<option value="' +
                        value.code +
                        '">' +
                        value.name +
                        "</option>"
                );
            });
        },
    });
}

function editPopulateBrgyDropdown2(cityId) {
    $("#e_brgy2").empty();

    $("#e_brgy2").append('<option value="">Please select</option>');
    $.ajax({
        type: "GET",
        url: "/get-brgy/" + cityId,
        success: function (response) {
            $.each(response, function (key, value) {
                $("#e_brgy2").append(
                    '<option value="' +
                        value.code +
                        '">' +
                        value.name +
                        "</option>"
                );
            });
        },
    });
}
