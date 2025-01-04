var industryResponse;
var fallbackObjectivesHTML = $('#Slide-5-Wrapper .form-about-section').html();

function getInitiativeTitles(jsonData, industry) {

    var initiatives = JSON.parse(industryResponse)[industry]["initiatives"];
    var titles = [];

    // Loop through each initiative and extract the title
    $.each(initiatives, function(index, initiative) {
        titles.push(initiative.title);
    });

    return titles;
}

function createFields(fieldID, data) {
    $('#'+fieldID+' option').remove();
    $('#'+fieldID).append('<option value=""></option>');
    $.each(data, function(index, item) {
        $('#'+fieldID).append('<option value="'+item+'">'+item+'</option>');
    });
}

function updateSolutionSelectFieldV2(nestedObject, selectFieldId, other=false) {

    // Get the select field by ID
    const selectField = document.getElementById(selectFieldId);

    // Clear existing options
    selectField.innerHTML = "";

    // Create and append a default empty option
    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.textContent = "";
    selectField.appendChild(defaultOption);

    //Add new options from the nested object array
    nestedObject.forEach(value => {
        const option = document.createElement("option");
        option.value = value;
        option.textContent = value;
        selectField.appendChild(option);
    });

    if(other) {
        $(selectField).append('<option value="Other">Other</option>');
    }

}

function ajaxIndustryResponse(response, request) {

    industryResponse = response;
    industryRequest = request;

    if(industryResponse != '{"result":"false"}') {
        var titles = getInitiativeTitles(industryResponse, industryRequest);
        createFields('Solution-Area', titles);

        $('select#Solution-Area').on('change',function(){
            var changedElement = $(this);
            $.each(JSON.parse(industryResponse)[$('#Industry-2').val()]["initiatives"], function(index, initiative) {

                if(changedElement.val() == '') {
                    $('.solution-field-group-wrapper').css("display","none");
                    $('.solution-field-group-wrapper').css("visibility","hidden");
                    $('.solution-field-group-wrapper select').val("");
                    $('div.form-wrapper-landing-page').animate({
                        height: $('div[aria-label="4 of 5"]').height() + 30 + 'px'
                    }, 500);
                } else {
                    $('.solution-field-group-wrapper').css("visibility","visible");
                    $('.solution-field-group-wrapper').css("display","flex");
                }

                if(initiative.title == changedElement.val()) {
                    $('.form-label-focus-area-span').text(initiative.title.toLowerCase());
                    updateSolutionSelectFieldV2(initiative.business_objectives, 'Primary-Objective', true);
                    updateSolutionSelectFieldV2(initiative.success_measures, 'Current-Measures', true);
                    updateSolutionSelectFieldV2(initiative.implementation_challenges, 'Challenges', true);
                    updateSolutionSelectFieldV2(initiative.areas_of_improvement, 'Areas-for-Improvement', true);

                    $('div.form-wrapper-landing-page').animate({
                        height: $('div[aria-label="5 of 5"]').height() + 30 + 'px'
                    }, 500);
                }
            });
        });

        return true;
    } else {
        $('#Slide-5-Wrapper .form-about-section').html(window.fallbackObjectivesHTML);
        $('select#Solution-Area').on('change', setFallbackObjectives);
        return industryResponse;
    }
}

function nextSlideButtonAjax(event='') {
    $.ajax({
        url: event.data.endpoint,
        method: 'GET',
        success: function(response) {
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error:', textStatus, errorThrown);
            // Handle any errors here
        }
    }); // end ajax
}

$(document).ready(function() {

    $('#Industry-2').on('change', function(){
        $('.solution-field-group-wrapper').css("display","none");
        $('.solution-field-group-wrapper').css("visibility","hidden");
        $('#Slide-5-Wrapper select').val('');
    });

    $('#Next-Slide-2').on('click press', function() {
        var industryRequest = $('#Industry-2').val();

        if (industryRequest !== '') {
            var endpoint = 'https://ps-json-to-html-table-43c5e8c6dd96.herokuapp.com/get_industry.php?item='+industryRequest;

            $.ajax({
                url: endpoint,
                method: 'GET',
                success: function(response) {
                    if(ajaxIndustryResponse(response, industryRequest) === true) {
                        $('#Next-Slide-3,#Next-Slide-4,#Next-Slide-5').off('click press', nextSlideButtonAjax);
                    } else {
                        var endpoint_check_only = 'https://ps-json-to-html-table-43c5e8c6dd96.herokuapp.com/get_industry.php?item='+industryRequest+'&check_only=true';

                        $('#Next-Slide-3,#Next-Slide-4,#Next-Slide-5').on('click press', {endpoint: endpoint_check_only}, nextSlideButtonAjax);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error:', textStatus, errorThrown);
                    // Handle any errors here
                }
            });
        }
    });
});