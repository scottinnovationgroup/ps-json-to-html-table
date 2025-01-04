var industryResponse;
var fallbackObjectivesHTML = $('#Slide-5-Wrapper .form-about-section').html();

$('.solution-field-group-wrapper').css("visibility","hidden");
$('select#Solution-Area').on('change', setFallbackObjectives);

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
            console.log('bp1');
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
        $('#Slide-5-Wrapper .form-about-section').html(fallbackObjectivesHTML);
        $('select#Solution-Area').on('change', setFallbackObjectives);
        console.log('bp4');
        return industryResponse;
    }
}

function nextSlideButtonAjax(event='') {
    $.ajax({
        url: event.data.endpoint,
        method: 'GET',
        success: function(response) {
            console.log(ajaxIndustryResponse(response));
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error:', textStatus, errorThrown);
            // Handle any errors here
        }
    }); // end ajax
}

function updateSelectField(nestedObject, selectFieldId) {
    // Get the select field by ID
    const selectField = document.getElementById(selectFieldId);

    if (!selectField || !nestedObject[selectFieldId]) {
        console.error("Invalid select field ID or nested object name.");
        return;
    }

    // Clear existing options
    selectField.innerHTML = "";

    // Create and append a default empty option
    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.textContent = "";
    selectField.appendChild(defaultOption);

    // Add new options from the nested object array
    nestedObject[selectFieldId].forEach(value => {
        const option = document.createElement("option");
        option.value = value;
        option.textContent = value;
        selectField.appendChild(option);
    });

}

function setFallbackObjectives() {
    $('.other-option-input-section').remove();

    if($(this).val() == '') {
        $('.solution-field-group-wrapper').css("visibility","hidden");
        $('.solution-field-group-wrapper select').val("");

        $('div.form-wrapper-landing-page').animate({
            height: $('div[aria-label="4 of 5"]').height() + 30 + 'px'
        }, 500);
    } else {
        $('.solution-field-group-wrapper').css("visibility","visible");
        $('.solution-field-group-wrapper').css("display","flex");

        if($(this).val() == 'Brand and Marketing Strategy') {
            $('.solution-field-group-wrapper').css("visibility","visible");
            $('.form-label-focus-area-span').text('brand and marketing strategy');
            updateSelectField(brandAndMarketing, 'Primary-Objective');
            updateSelectField(brandAndMarketing, 'Current-Measures');
            updateSelectField(brandAndMarketing, 'Challenges');
            updateSelectField(brandAndMarketing, 'Areas-for-Improvement');
        }
        if($(this).val() == 'Business Transformation') {
            $('.solution-field-group-wrapper').css("visibility","visible");
            $('.form-label-focus-area-span').text('business transformation');
            updateSelectField(businessTransformation, 'Primary-Objective');
            updateSelectField(businessTransformation, 'Current-Measures');
            updateSelectField(businessTransformation, 'Challenges');
            updateSelectField(businessTransformation, 'Areas-for-Improvement');
        }
        if($(this).val() == 'Organizational Change Management') {
            $('.solution-field-group-wrapper').css("visibility","visible");
            $('.form-label-focus-area-span').text('organizational change management');
            updateSelectField(organizationalChangeManagement, 'Primary-Objective');
            updateSelectField(organizationalChangeManagement, 'Current-Measures');
            updateSelectField(organizationalChangeManagement, 'Challenges');
            updateSelectField(organizationalChangeManagement, 'Areas-for-Improvement');
        }
        if($(this).val() == 'IT Service Delivery') {
            $('.solution-field-group-wrapper').css("visibility","visible");
            $('.form-label-focus-area-span').text('IT service delivery');
            updateSelectField(itServiceDelivery, 'Primary-Objective');
            updateSelectField(itServiceDelivery, 'Current-Measures');
            updateSelectField(itServiceDelivery, 'Challenges');
            updateSelectField(itServiceDelivery, 'Areas-for-Improvement');
        }
        if($(this).val() == 'Product Innovation and Development') {
            $('.solution-field-group-wrapper').css("visibility","visible");
            $('.form-label-focus-area-span').text('product innovation and development');
            updateSelectField(productInnovationAndDevelopment, 'Primary-Objective');
            updateSelectField(productInnovationAndDevelopment, 'Current-Measures');
            updateSelectField(productInnovationAndDevelopment, 'Challenges');
            updateSelectField(productInnovationAndDevelopment, 'Areas-for-Improvement');
        }
        if($(this).val() == 'Startup Growth and Expansion') {
            $('.solution-field-group-wrapper').css("visibility","visible");
            $('.form-label-focus-area-span').text('startup growth and expansion');
            updateSelectField(startupGrowthAndExpansion, 'Primary-Objective');
            updateSelectField(startupGrowthAndExpansion, 'Current-Measures');
            updateSelectField(startupGrowthAndExpansion, 'Challenges');
            updateSelectField(startupGrowthAndExpansion, 'Areas-for-Improvement');
        }

        $('div.form-wrapper-landing-page').animate({
            height: $('div[aria-label="5 of 5"]').height() + 30 + 'px'
        }, 500);
    }
}

const brandAndMarketing = {
    "Primary-Objective": [
        "Enhance Brand Awareness",
        "Strengthen Brand Loyalty",
        "Improve Brand Image and Perception",
        "Expand Brand Reach",
        "Innovate Brand Positioning",
        "Optimize Brand Portfolio",
        "Enhance Digital Brand Experience",
        "Ensure Brand Consistency",
        "Develop Strategic Brand Partnerships",
        "Leverage Data for Brand Insights"
    ],
    "Current-Measures": [
        "Brand awareness metrics",
        "Customer loyalty/retention rates",
        "Brand perception surveys",
        "Market reach and penetration",
        "Positioning effectiveness",
        "Portfolio performance",
        "Digital engagement metrics",
        "Brand consistency audits",
        "Partnership ROI",
        "Data analytics and insights",
        "Other"

    ],
    "Challenges": [
        "Limited resources",
        "Lack of clear strategy",
        "Difficulty in measuring impact",
        "Keeping up with digital trends",
        "Maintaining brand consistency",
        "Identifying strategic partners",
        "Utilizing data effectively",
        "Other"

    ],
    "Areas-for-Improvement": [
        "Brand awareness strategies",
        "Loyalty programs",
        "Image and perception management",
        "Reach expansion tactics",
        "Positioning strategies",
        "Portfolio optimization",
        "Digital experience enhancement",
        "Consistency in branding",
        "Partnership development",
        "Data leverage for insights",
        "Other"

    ]
};

const businessTransformation = {
    "Primary-Objective": [
        "Improving Efficiency and Productivity",
        "Enhancing Customer Experience",
        "Increasing Agility and Innovation",
        "Digital Transformation",
        "Expanding Market Reach",
        "Improving Decision-Making",
        "Cultural Transformation",
        "Enhancing Compliance and Risk Management",
        "Sustainability and Social Responsibility",
        "Financial Performance"
    ],
    "Current-Measures": [
        "Efficiency gains (time/cost)",
        "Customer satisfaction scores",
        "Innovation metrics (number of new products/services)",
        "Digital adoption rates",
        "Market share growth",
        "Quality of decision-making",
        "Employee engagement scores",
        "Compliance audit outcomes",
        "Sustainability indices",
        "Financial performance (revenue, profit margins)",
        "Other"

    ],
    "Challenges": [
        "Resource constraints",
        "Resistance to change",
        "Technology limitations",
        "Lack of expertise",
        "Market competition",
        "Data quality and accessibility",
        "Cultural barriers",
        "Regulatory and compliance hurdles",
        "Sustainability implementation challenges",
        "Financial constraints",
        "Other"

    ],
    "Areas-for-Improvement": [
        "Process optimization",
        "Customer experience enhancement",
        "Agility and innovation processes",
        "Digital transformation initiatives",
        "Market expansion strategies",
        "Decision-making frameworks",
        "Organizational culture shift",
        "Compliance and risk management",
        "Sustainability practices",
        "Financial restructuring",
        "Other"

    ]
};

const itServiceDelivery = {
    "Primary-Objective": [
        "Improve Service Quality",
        "Enhance IT Service Efficiency",
        "Ensure High Availability and Reliability",
        "Strengthen IT Security and Compliance",
        "Foster Innovation within IT Services",
        "Optimize IT Cost Management",
        "Enhance IT Service Agility",
        "Improve IT Vendor Management",
        "Strengthen IT Infrastructure Management",
        "Enhance IT Skills and Knowledge Management"
    ],
    "Current-Measures": [
        "Service quality metrics (e.g., SLAs)",
        "Efficiency metrics (e.g., response times)",
        "Availability and reliability metrics (e.g., uptime)",
        "Security compliance audits",
        "Innovation metrics (e.g., new services launched)",
        "Cost management metrics (e.g., ROI, TCO)",
        "Agility metrics (e.g., time to market)",
        "Vendor performance metrics",
        "Infrastructure management metrics (e.g., health checks)",
        "Skills and knowledge assessments",
        "Other"

    ],
    "Challenges": [
        "Limited resources",
        "Lack of clear strategy",
        "Difficulty in measuring impact",
        "Security and compliance concerns",
        "Keeping up with technological advancements",
        "Cost constraints",
        "Vendor management issues",
        "Infrastructure limitations",
        "Skill gaps in the team",
        "Other"

    ],
    "Areas-for-Improvement": [
        "Service quality improvement",
        "Efficiency enhancements",
        "Availability and reliability",
        "Security and compliance",
        "Fostering innovation",
        "Cost optimization",
        "Increasing agility",
        "Vendor management",
        "Infrastructure management",
        "Skills and knowledge development",
        "Other"

    ]
};

const organizationalChangeManagement = {
    "Primary-Objective": [
        "Enhance Change Readiness",
        "Improve Stakeholder Engagement",
        "Minimize Resistance to Change",
        "Align Organizational Culture with Change",
        "Ensure Effective Change Leadership",
        "Build Change Management Competency",
        "Maximize Employee Engagement and Support",
        "Measure and Improve Change Impact",
        "Enhance Communication Effectiveness",
        "Foster Agility and Flexibility in Change"
    ],
    "Current-Measures": [
        "Employee feedback and surveys",
        "Stakeholder engagement levels",
        "Speed of adoption",
        "Depth of utilization",
        "Change resilience metrics",
        "Performance improvements",
        "Communication reach and clarity",
        "Leadership support and participation",
        "Training and competency development",
        "Other"

    ],
    "Challenges": [
        "Lack of change readiness",
        "Insufficient stakeholder engagement",
        "High resistance to change",
        "Misalignment of organizational culture",
        "Ineffective change leadership",
        "Lack of change management competency",
        "Low employee engagement",
        "Difficulty in measuring change impact",
        "Ineffective communication strategies",
        "Lack of agility and flexibility",
        "Other"

    ],
    "Areas-for-Improvement": [
        "Change readiness assessment",
        "Stakeholder engagement strategies",
        "Resistance management techniques",
        "Cultural alignment initiatives",
        "Leadership development for change",
        "Change management training programs",
        "Employee engagement and support mechanisms",
        "Change impact measurement tools",
        "Communication effectiveness enhancement",
        "Developing agility and flexibility in change processes",
        "Other"

    ]
};

const productInnovationAndDevelopment = {
    "Primary-Objective": [
        "Accelerating Time to Market",
        "Enhancing Product Quality",
        "Increasing Product Innovation",
        "Expanding Product Range",
        "Improving Customer Satisfaction with New Products",
        "Enhancing Market Competitiveness",
        "Boosting Product Profitability",
        "Sustainability in Product Design",
        "Leveraging Technology for Product Development",
        "Improving Adaptability to Market Changes"
    ],
    "Current-Measures": [
        "Time to market",
        "Product quality metrics",
        "Number of new products launched",
        "Customer satisfaction scores",
        "Market share",
        "Profit margins",
        "Sustainability indices",
        "Technology adoption rates",
        "Flexibility in product design changes",
        "Other"

    ],
    "Challenges": [
        "Resource constraints",
        "Lack of clear strategy",
        "Insufficient market research",
        "Technology limitations",
        "Regulatory hurdles",
        "Supply chain issues",
        "Skill gaps in the team",
        "Other"

    ],
    "Areas-for-Improvement": [
        "Project selection and prioritization",
        "Resource allocation and optimization",
        "Risk management and mitigation",
        "Collaboration and communication",
        "Performance tracking and reporting",
        "Integration with other business systems",
        "Agile and flexible project management",
        "Other"

    ]
};

const startupGrowthAndExpansion = {
    "Primary-Objective": [
        "Increase Market Share",
        "Enhance Customer Base",
        "Strengthen Brand Awareness",
        "Diversify Product/Service Offerings",
        "Optimize Operational Efficiency",
        "Secure Financial Stability",
        "Expand Geographic Presence",
        "Foster Innovation and R&D",
        "Build Strategic Partnerships",
        "Enhance Talent Acquisition and Retention"
    ],
    "Current-Measures": [
        "Revenue growth",
        "Market share increase",
        "Customer acquisition rates",
        "Brand recognition metrics",
        "Product/service diversification",
        "Operational efficiency metrics",
        "Financial health indicators",
        "Geographic expansion milestones",
        "Innovation and R&D outputs",
        "Talent acquisition and retention rates",
        "Other"

    ],
    "Challenges": [
        "Market competition",
        "Customer acquisition and retention",
        "Brand visibility",
        "Product/service innovation",
        "Operational inefficiencies",
        "Financial constraints",
        "Geographic expansion barriers",
        "Finding and maintaining strategic partnerships",
        "Talent management",
        "Other"

    ],
    "Areas-for-Improvement": [
        "Market share growth strategies",
        "Customer base enhancement",
        "Brand awareness campaigns",
        "Product/service diversification planning",
        "Operational efficiency optimization",
        "Financial stability programs",
        "Geographic expansion planning",
        "Innovation and R&D investment",
        "Strategic partnership development",
        "Talent acquisition and retention strategies",
        "Other"

    ]
};

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
                        console.log('bp2');
                    } else {
                        console.log('bp3');

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