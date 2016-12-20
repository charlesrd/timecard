/*
	AMS Timecard Program - Javascript / Jquery Code
*/

// execeute when the document finishes loading
$(document).ready(function() {
    init();
});

function init()
{

}

// setup variables
var LOGGED_IN = false;
var PAGE_TITLE = "AMS Timecard | AmericaSmiles & United Dental Resources Hour Time Tracker";
var CLOCK_ACTION = "";
var CLOCK_DATE = "";
var RECORD_NUMBER = "";
var FORGOT_STRING = "";
var USER_ID = "";
var forgotCell = "";
var isForgotActive = false;
var doc = "";
var CHANGE_WEEK_START_DAY = "";
var REPORT_NUMBER = 0;
var VACATION_DATE = "";
var HOLIDAY_DATE = "";
var PERSONAL_DATE = "";
var SICK_DATE = "";
var ERRORS = "";

/* Functions */

function clockIn()
{
    // variable to hold request
    var request;
    var clockTime = $("#clock-time");
    var clockIn = $("#clock-in");
    var clockOut = $("#clock-out");
    var userTableDiv = $("#user-table-div");

    // hide clockTime and clockIn
    clockIn.hide(300);
    setTimeout(function () {clockTime.hide();}, 300);

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/clockIn.php", // the post url
        {
            uid: $("#uid").val()
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");
        
        // show clockTime and clockOut
        setTimeout(function ()
        {
            clockIn.removeClass("show").addClass("hide");
        	clockOut.removeClass("hide").addClass("show");
            clockOut.hide();
            clockTime.show();
            clockOut.show(700);
            userTableDiv.html(response);
            userTableDiv.scrollTop(0);
        }, 300);
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function updateCompanyBreaks(company_id, breaks)
{
    // variable to hold request
    var request;

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/updateCompanyBreaks.php", // the post url
        {
            c: company_id,
            b: breaks
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function submitOpinionCode()
{
    // variable to hold request
    var request;
    var modal = $(".modal");

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/submitOpinionCode.php", // the post url
        {
            code: $("#opinion-code").val()
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        console.log("response: "+response);

        if (response === "1")
        {
            $("#opinion-message").html('').show();

            console.log("Made it HERE CODE");
            $("#opinion-board-modal").modal(
            {
                backdrop: 'static',
                show: true
            });
        }
        else
        {
            $("#opinion-message").html('').show();
            $('<div class="alert alert-danger text-center"><strong>Invalid Opinion Code. Please Try Again.</strong></div>').hide().appendTo('#opinion-message').slideDown(500);
            return false;
        }
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function saveOpinionBoard()
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var errors = 0;


    // error checking
    $(".opinion-board-textarea").removeClass("error-input");
    
    if ($("#opinion-board-self-textarea").val() == "") {$("#opinion-board-self-textarea").addClass("error-input"); errors++;}
    if ($("#opinion-board-company-textarea").val() == "") {$("#opinion-board-company-textarea").addClass("error-input"); errors++;}

    if (errors > 0) {return;}



    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/saveOpinionBoard.php", // the post url
        {
            code: $("#opinion-code").val(),
            self_opinion: $('#opinion-board-self-textarea').val(),
            company_opinion: $('#opinion-board-company-textarea').val()
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        $("#opinion-code").val('');
        $(".opinion-board-textarea").val('');

        $("#opinion-message").html('').show();
        $('<div class="alert alert-success text-center"><strong>Your Opinion was successfully saved. Thank you for your input.</strong></div>').hide().appendTo('#opinion-message').slideDown(500);
        return;

     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function saveEmployeeMessage()
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var errors = 0;


    // error checking
    $("#employee-message-textarea").removeClass("error-input");
    if ($("#employee-message-textarea").val() == "") {$("#employee-message-textarea").addClass("error-input"); errors++;}
    if (errors > 0) {return;}


    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/saveEmployeeMessage.php", // the post url
        {
            message: $('#employee-message-textarea').val()
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function setupModal(radio)
{
    if (radio != 'edit-time')
    {
    	$('input[name='+radio+']').removeAttr('checked');
    }

    // reset and show the add-user modal
    if (radio == 'change-user') {resetChangeUserModal(1);}

    // reset and show the account-settings modal
    if (radio == 'account-settings') {resetAccountSettingsModal(1);}

    // reset and show the add-user modal
    if (radio == 'add-user') {resetAddUserModal(1);}

    // reset and show the message-board modal
    if (radio == 'message-board') {resetMessageBoardModal(1);}

    // reset and show the ip-address modal
    if (radio == 'ip-address') {resetIPAddressModal(1);}

    // reset and show the company-settings modal
    if (radio == 'company-settings') {resetCompanySettingsModal(1);}

    // reset and show the request-personal-time modal
    if (radio == 'request-personal-time') {resetRequestPersonalTimeModal(1);}

    // reset and show the request-vacation-time modal
    if (radio == 'request-vacation-time') {resetRequestVacationTimeModal(1);}

    // reset and show the admin-warning-messages modal
    if (radio == 'admin-warning-messages') {resetAdminWarningMessagesModal(1);}

    // reset and show the holiday-time modal
    if (radio == 'holiday-time') {resetHolidayTimeModal(1);}

    // reset and show the personal-time modal
    if (radio == 'personal-time') {resetPersonalTimeModal(1);}

    // reset and show the vacation-time modal
    if (radio == 'vacation-time') {resetVacationTimeModal(1);}

    // reset and show the sick-time modal
    if (radio == 'sick-time') {resetSickTimeModal(1);}

    // reset and show the employee-message modal
    if (radio == 'employee-message') {resetEmployeeMessageModal(1);}

    // disable the done buttons
    $('.btn-done').attr("disabled", true);
}

function clockOut()
{
    // variable to hold request
    var request;
    var clockTime = $("#clock-time");
    var clockIn = $("#clock-in");
    var clockOut = $("#clock-out");
    var clockOutModal = $("#clock-out-modal");
    var userTableDiv = $("#user-table-div");

    // hide the clockOutModal, clockTime and clockOut
    clockOutModal.modal('hide');
    clockOut.hide(300);
    setTimeout(function () {clockTime.hide();}, 300);

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/clockOut.php", // the post url
        {
            uid: $("#uid").val(),
            r: $('input[name=clock-out-reason]:checked').val()
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");
        
        // show clockTime and clockOut
        setTimeout(function ()
        {
            clockIn.removeClass("hide").addClass("show");
        	clockOut.removeClass("show").addClass("hide");
            clockIn.hide();

            var clockTimeHTML = clockTime.html();
            clockTime.html('');
            clockTime.html(clockTimeHTML);

            userTableDiv.html(response);
            userTableDiv.scrollTop(0);
            
            if ($("#hideClockTime").val() == 0)
            {
                clockTime.show();
                clockIn.show(700);
            }

            // IF the user is an admin, don't show the warning messages after clocking out
            if ($('#admin-warning-messages-modal-container').length == 0)
            {
                // IF the user has any warning messages, display the warning message modal
                if ( ($('#missingHours').val() === "1") || ($('#overtimeHours').val() === "1") )
                {
                    // show the warning-message modal
                    setTimeout(function ()
                    {
                        // hide all the warning messages
                        $('.warning-message').hide();

                        // setup variables
                        var d = new Date();
                        var day = d.getDay();

                        // only show the warning messages that apply
                        if ($('#missingHours').val() === "1") {$('#missing-hours-alert').show();}
                        if ( ($('#overtimeHours').val() === "1") && (day <= 5) ) {$('#overtime-hours-alert').show();}

                        $("#warning-messages-modal").modal(
                        {
                            backdrop: 'static',
                            show: true
                        });
                    }, 500);
                }
            }

        }, 300);
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function viewHistory()
{
	// variable to hold request
    var request;
    var modal = $(".modal");
    var clockTime = $("#clock-time");
    var editTime = $("#edit-time");
    var userTableDiv = $("#user-table-div");

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/user-table.php", // the post url
        {
            uid: $("#uid").val(),
            d: $('#startHistoryDate').val()
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();

        // hide edit-time
        editTime.hide();

        // hide or show clock-time
        if ($('#hideClockTime').val() === "1") {clockTime.hide();}
        else {clockTime.show();}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function changeDay()
{
    CHANGE_WEEK_START_DAY = $('.startDate').val();
    viewReport(4);
}


function printReport(num,start_date,end_date)
{
    // optional: start_date, end_date
    start_date = arguments[1] || "";
    end_date = arguments[2] || "";
	var printPage = "http://www.amstimecard.com/tpl/printReport.php?r="+num+"&sd="+start_date+"&ed="+end_date;
	var win = window.open(printPage, '_blank');
	win.focus();
}

function changeUser(id,date)
{
	// optional: id, date (only used if a user's name is clicked in the table)
    id = arguments[0] || 0;

    // optional: date (only used if a row is clicked on in the forgotten hours report)
    date = arguments[1] || "";

    // check if the id is present
    if (id != 0)
    {
    	// the id is present
    	USER_ID = id;
    }
    else
    {
    	// the id is not present
    	USER_ID = $('input[name=change-user-name]:checked').val();
    }

	// variable to hold request
    var request;
    var modal = $(".modal");
    var editTime = $("#edit-time");
    var clockTime = $("#clock-time");
    var clockIn = $("#clock-in");
    var clockOut = $("#clock-out");
    var viewHistoryThumbnailContainer = $("#view-history-thumbnail-container");
    var userTableDiv = $("#user-table-div");

    // hide the modal
    modal.modal('hide');

    // show view history, hide clock-time and edit-time
    editTime.hide();
    // clockTime.hide();
    viewHistoryThumbnailContainer.show();

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/user-table.php", // the post url
        {
            uid: $("#uid").val(),
            d: date,
            n: USER_ID
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // reset the uid to the new user id
        $("#uid").val(USER_ID);

        // reset the account-settings-modal to the correct information for the new user
        resetAccountSettingsModal();

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the view-history-modal to the correct information for the new user
        // resetViewHistoryModal();

        // reset the page title
        document.title = $("#pageTitleNew").val();

        // alert("showClockInNew: "+$('#showClockInNew').val());

        // hide / show clock-in or clock-out
        if ($('#showClockInNew').val() === "1")
        {
        	clockIn.show();
        	clockOut.hide();
        	clockIn.removeClass("hide").addClass("show");
        	clockOut.removeClass("show").addClass("hide");
        }
        else
        {
        	clockIn.hide();
        	clockOut.show();
        	clockIn.removeClass("show").addClass("hide");
        	clockOut.removeClass("hide").addClass("show");
        }

        // show clock-time
        clockTime.show();
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function resetChangeUserModal(show)
{
    // optional: show (whether or not to show the modal after it resets)
    show = arguments[0] || 0;

    // variable to hold request
    var request;
    var changeUserModalContainer = $("#change-user-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/change-user-modal.php", // the post url
        {

        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        changeUserModalContainer.html(response);

        // uncheck all the radio buttons
        $('input[name="change-user-name"]').removeAttr('checked');

        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // IF show == 1, show the modal
        if (show == 1) {$('#change-user-modal').modal('show');}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function resetAccountSettingsModal(show)
{
    // optional: show (whether or not to show the modal after it resets)
    show = arguments[0] || 0;

    // variable to hold request
    var request;
    var accountSettingsModalContainer = $("#account-settings-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/account-settings-modal.php", // the post url
        {
            uid: USER_ID
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // remove the add-user modal from the DOM
        $('#add-user-modal-container').html('');

        // print the response
        accountSettingsModalContainer.html(response);

        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // IF show == 1, show the modal
        if (show == 1) {$('#account-settings-modal').modal('show');}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function showEmployeeReviewModal(erid)
{
    // variable to hold request
    var request;
    var employeeReviewModalContainer = $("#employee-review-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/employee-review-modal.php", // the post url
        {
            erid: erid
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        employeeReviewModalContainer.html(response);

        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // show the modal
        $('#employee-review-modal').modal('show');
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function resetAddUserModal(show)
{
    // optional: show (whether or not to show the modal after it resets)
    show = arguments[0] || 0;

    // variable to hold request
    var request;
    var addUserModalContainer = $("#add-user-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/add-user-modal.php", // the post url
        {

        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // remove the account-settings modal from the DOM
        $('#account-settings-modal-container').html('');

        // print the response
        addUserModalContainer.html(response);

        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // IF show == 1, show the modal
        if (show == 1) {$('#add-user-modal').modal('show');}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function resetMessageBoardModal(show)
{
    // optional: show (whether or not to show the modal after it resets)
    show = arguments[0] || 0;

    // variable to hold request
    var request;
    var messageBoardModalContainer = $("#message-board-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/message-board-modal.php", // the post url
        {

        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        messageBoardModalContainer.html(response);

        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // IF show == 1, show the modal
        if (show == 1) {$('#message-board-modal').modal('show');}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function resetIPAddressModal(show)
{
    // optional: show (whether or not to show the modal after it resets)
    show = arguments[0] || 0;

    // variable to hold request
    var request;
    var ipAddressModalContainer = $("#ip-address-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/ip-address-modal.php", // the post url
        {

        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        ipAddressModalContainer.html(response);

        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // IF show == 1, show the modal
        if (show == 1) {$('#ip-address-modal').modal('show');}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function resetCompanySettingsModal(show)
{
    // optional: show (whether or not to show the modal after it resets)
    show = arguments[0] || 0;

    // variable to hold request
    var request;
    var companySettingsModalContainer = $("#company-settings-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/company-settings-modal.php", // the post url
        {

        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        companySettingsModalContainer.html(response);

        // IF show == 1, show the modal
        if (show == 1) {$('#company-settings-modal').modal('show');}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function resetRequestPersonalTimeModal(show)
{
    // optional: show (whether or not to show the modal after it resets)
    show = arguments[0] || 0;

    // variable to hold request
    var request;
    var requestPersonalTimeModalContainer = $("#request-personal-time-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/request-personal-time-modal.php", // the post url
        {

        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        requestPersonalTimeModalContainer.html(response);

        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // IF show == 1, show the modal
        if (show == 1) {$('#request-personal-time-modal').modal('show');}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function resetRequestVacationTimeModal(show)
{
    // optional: show (whether or not to show the modal after it resets)
    show = arguments[0] || 0;

    // variable to hold request
    var request;
    var requestVacationTimeModalContainer = $("#request-vacation-time-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/request-vacation-time-modal.php", // the post url
        {

        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        requestVacationTimeModalContainer.html(response);

        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // IF show == 1, show the modal
        if (show == 1) {$('#request-vacation-time-modal').modal('show');}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function resetAdminWarningMessagesModal(show)
{
    // optional: show (whether or not to show the modal after it resets)
    show = arguments[0] || 0;

    // variable to hold request
    var request;
    var adminWarningMessagesModalContainer = $("#admin-warning-messages-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/admin-warning-messages-modal.php", // the post url
        {
            fnct: 1
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        adminWarningMessagesModalContainer.html(response);

        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // IF show == 1, show the modal
        if (show == 1) {$('#admin-warning-messages-modal').modal('show');}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function resetHolidayTimeModal(show)
{
    // optional: show (whether or not to show the modal after it resets)
    show = arguments[0] || 0;

    // variable to hold request
    var request;
    var holidayTimeModalContainer = $("#holiday-time-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/holiday-time-modal.php", // the post url
        {

        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        holidayTimeModalContainer.html(response);

        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // IF show == 1, show the modal
        if (show == 1) {$('#holiday-time-modal').modal('show');}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function resetPersonalTimeModal(show)
{
    // optional: show (whether or not to show the modal after it resets)
    show = arguments[0] || 0;

    // variable to hold request
    var request;
    var personalTimeModalContainer = $("#personal-time-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/personal-time-modal.php", // the post url
        {

        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        personalTimeModalContainer.html(response);

        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // IF show == 1, show the modal
        if (show == 1) {$('#personal-time-modal').modal('show');}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function resetVacationTimeModal(show)
{
    // optional: show (whether or not to show the modal after it resets)
    show = arguments[0] || 0;

    // variable to hold request
    var request;
    var vacationTimeModalContainer = $("#vacation-time-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/vacation-time-modal.php", // the post url
        {

        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        vacationTimeModalContainer.html(response);

        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // IF show == 1, show the modal
        if (show == 1) {$('#vacation-time-modal').modal('show');}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function resetSickTimeModal(show)
{
    // optional: show (whether or not to show the modal after it resets)
    show = arguments[0] || 0;

    // variable to hold request
    var request;
    var sickTimeModalContainer = $("#sick-time-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/sick-time-modal.php", // the post url
        {

        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        sickTimeModalContainer.html(response);

        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // IF show == 1, show the modal
        if (show == 1) {$('#sick-time-modal').modal('show');}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function resetEmployeeMessageModal(show)
{
    // optional: show (whether or not to show the modal after it resets)
    show = arguments[0] || 0;

    // variable to hold request
    var request;
    var employeeMessageModalContainer = $("#employee-message-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/employee-message-modal.php", // the post url
        {

        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        employeeMessageModalContainer.html(response);

        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // IF show == 1, show the modal
        if (show == 1) {$('#employee-message-modal').modal('show');}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function resetViewHistoryModal()
{
	// variable to hold request
    var request;
    var viewHistoryModalContainer = $("#view-history-modal-container");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/view-history-modal.php", // the post url
        {
            uid: USER_ID
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        viewHistoryModalContainer.html(response);
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function changeWeek()
{
	// get the start date of the chosen week
	// CHANGE_WEEK_START_DAY = $('input[name=change-week-week]:checked').val();
    CHANGE_WEEK_START_DAY = $('#startDate').val();

	// view the All Employee Hours report
	viewReport(6);
}

function format2Digit(n) {return n < 10 ? '0' + n : n;}

function addVacationTime(num)
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var addVacation = $("#add-vacation");
    var editVacation = $("#edit-vacation");
    var userTableDiv = $("#user-table-div");
    var uid = $("#uid").val();
    var hour = 0;
    var minute = 0;
    var note = "";
    var newUser = "";
    var day = "";
    var edit = "";
    var user_hr_list = "";
    var user_min_list = "";
    var user_id_list = "";

    // IF the num == 0, then its from the Add Vacation button
    if (num == 0)
    {
        day = $("#startVacationDate").val();
        newUser = $("#vacation-time-user").val();
        hour = $("#vacation-time-hour").val();
        minute = $("#vacation-time-minute").val();
        note = $("#vacation-time-note").val();

        $('.employee-list-hr').each(function() {
            user_hr_list += $(this).val()+"|";
        });

        $('.employee-list-min').each(function() {
            user_min_list += $(this).val()+"|";
        });

        $('.employee-list-id').each(function() {
            user_id_list += $(this).val()+"|";
        });

        // remove the last character from the lists
        user_hr_list = user_hr_list.slice(0,-1);
        user_min_list = user_min_list.slice(0,-1);
        user_id_list = user_id_list.slice(0,-1);
    }

    // IF the num == 1, then its from the Edit Vacation button
    if (num == 1)
    {
        day = VACATION_DATE;
        newUser = uid;
        hour = $("#edit-vacation-hour").val();
        minute = $("#edit-vacation-minute").val();
        edit = 1;
    }

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/user-table.php", // the post url
        {
            uid: uid,
            d: day,
            n: newUser,
            vh: hour,
            vm: minute,
            vn: note,
            velh: user_hr_list,
            velm: user_min_list,
            velid: user_id_list,
            ve: edit
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // reset the uid to the new user id
        $("#uid").val(USER_ID);

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();

        // hide edit-vacation
        editVacation.hide();

        // show add-vacation
        addVacation.show();
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function addHolidayTime(num)
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var addHoliday = $("#add-holiday");
    var editHoliday = $("#edit-holiday");
    var userTableDiv = $("#user-table-div");
    var uid = $("#uid").val();
    var hour = 0;
    var minute = 0;
    var newUser = "";
    var day = "";
    var edit = "";
    var user_hr_list = "";
    var user_min_list = "";
    var user_id_list = "";

    // IF the num == 0, then its from the Add Holiday button
    if (num == 0)
    {
        day = $("#startHolidayDate").val();
        newUser = $("#holiday-time-user").val();
        hour = $("#holiday-time-hour").val();
        minute = $("#holiday-time-minute").val();

        $('.employee-list-hr').each(function() {
            user_hr_list += $(this).val()+"|";
        });

        $('.employee-list-min').each(function() {
            user_min_list += $(this).val()+"|";
        });

        $('.employee-list-id').each(function() {
            user_id_list += $(this).val()+"|";
        });

        // remove the last character from the lists
        user_hr_list = user_hr_list.slice(0,-1);
        user_min_list = user_min_list.slice(0,-1);
        user_id_list = user_id_list.slice(0,-1);
    }

    // IF the num == 1, then its from the Edit Holiday button
    if (num == 1)
    {
        day = HOLIDAY_DATE;
        newUser = uid;
        hour = $("#edit-holiday-hour").val();
        minute = $("#edit-holiday-minute").val();
        edit = 1;
    }

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/user-table.php", // the post url
        {
            uid: uid,
            d: day,
            n: newUser,
            hh: hour,
            hm: minute,
            helh: user_hr_list,
            helm: user_min_list,
            helid: user_id_list,
            he: edit
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // reset the uid to the new user id
        $("#uid").val(USER_ID);

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();

        // hide edit-holiday
        editHoliday.hide();

        // show add-holiday
        addHoliday.show();
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function addPersonalTime(num)
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var addPersonal = $("#add-personal");
    var editPersonal = $("#edit-personal");
    var userTableDiv = $("#user-table-div");
    var uid = $("#uid").val();
    var hour = 0;
    var minute = 0;
    var newUser = "";
    var day = "";
    var edit = "";
    var user_hr_list = "";
    var user_min_list = "";
    var user_id_list = "";

    // IF the num == 0, then its from the Add Personal button
    if (num == 0)
    {
        day = $("#startPersonalDate").val();
        newUser = $("#personal-time-user").val();
        hour = $("#personal-time-hour").val();
        minute = $("#personal-time-minute").val();

        $('.employee-list-hr').each(function() {
            user_hr_list += $(this).val()+"|";
        });

        $('.employee-list-min').each(function() {
            user_min_list += $(this).val()+"|";
        });

        $('.employee-list-id').each(function() {
            user_id_list += $(this).val()+"|";
        });

        // remove the last character from the lists
        user_hr_list = user_hr_list.slice(0,-1);
        user_min_list = user_min_list.slice(0,-1);
        user_id_list = user_id_list.slice(0,-1);
    }

    // IF the num == 1, then its from the Edit Personal button
    if (num == 1)
    {
        day = PERSONAL_DATE;
        newUser = uid;
        hour = $("#edit-personal-hour").val();
        minute = $("#edit-personal-minute").val();
        edit = 1;
    }

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/user-table.php", // the post url
        {
            uid: uid,
            d: day,
            n: newUser,
            ph: hour,
            pm: minute,
            pelh: user_hr_list,
            pelm: user_min_list,
            pelid: user_id_list,
            pe: edit
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // reset the uid to the new user id
        $("#uid").val(USER_ID);

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();

        // hide edit-vacation
        editPersonal.hide();

        // show add-vacation
        addPersonal.show();
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function addSickTime(num)
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var addSick = $("#add-sick");
    var editSick = $("#edit-sick");
    var userTableDiv = $("#user-table-div");
    var uid = $("#uid").val();
    var hour = 0;
    var minute = 0;
    var newUser = "";
    var day = "";
    var edit = "";
    var user_hr_list = "";
    var user_min_list = "";
    var user_id_list = "";

    // IF the num == 0, then its from the Add Sick button
    if (num == 0)
    {
        day = $("#startSickDate").val();
        newUser = $("#sick-time-user").val();
        hour = $("#sick-time-hour").val();
        minute = $("#sick-time-minute").val();
        absence_type = $("#sick-time-absence").val();

        $('.employee-list-hr').each(function() {
            user_hr_list += $(this).val()+"|";
        });

        $('.employee-list-min').each(function() {
            user_min_list += $(this).val()+"|";
        });

        $('.employee-list-id').each(function() {
            user_id_list += $(this).val()+"|";
        });

        // remove the last character from the lists
        user_hr_list = user_hr_list.slice(0,-1);
        user_min_list = user_min_list.slice(0,-1);
        user_id_list = user_id_list.slice(0,-1);
    }

    // IF the num == 1, then its from the Edit Sick button
    if (num == 1)
    {
        day = SICK_DATE;
        newUser = uid;
        hour = $("#edit-sick-hour").val();
        minute = $("#edit-sick-minute").val();
        absence_type = $("#edit-sick-time-absence").val();
        edit = 1;
    }

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/user-table.php", // the post url
        {
            uid: uid,
            d: day,
            n: newUser,
            sh: hour,
            sm: minute,
            selh: user_hr_list,
            selm: user_min_list,
            selid: user_id_list,
            sat: absence_type,
            se: edit
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // reset the uid to the new user id
        $("#uid").val(USER_ID);

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();

        // hide edit-sick
        editSick.hide();

        // show add-sick
        addSick.show();
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function requestPersonalTime(num)
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var userTableDiv = $("#user-table-div");
    var uid = $("#uid").val();
    var day = $('#startPersonalDate').val();
    var hour = $('#personal-time-hour option:selected').val();
    var min = $('#personal-time-minute option:selected').val();
    var note = $('#personal-time-note').val();
    var time = format2Digit(hour)+":"+format2Digit(min)+":00";

    // // hide the modal
    // modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/user-table.php", // the post url
        {
            uid: uid,
            pd: day,
            pt: time,
            pn: note
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        if (response == "0")
        {
            // disable the done buttons
            $('.btn-done').attr("disabled", true);

            // display the errors
            $('<div class="alert alert-danger"><strong>Your maximum allowed Personal Days per year has been reached.</strong></div>').hide().appendTo('#span-request-personal-time').slideDown(500);
        }
        else
        {
            // hide the modal
            modal.modal('hide');

            // print the response
            userTableDiv.html(response);
            userTableDiv.scrollTop(0);

            // reset the page title
            document.title = $("#pageTitleNew").val();
        }
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function requestVacationTime(num)
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var userTableDiv = $("#user-table-div");
    var uid = $("#uid").val();
    var day = $('#startVacationDate').val();


    // IF no date is selected, display message and exit
    if (day == "")
    {
        // disable the done buttons
        $('.btn-done').attr("disabled", true);

        // display the errors
        $('<div class="alert alert-danger"><strong>Please select vacation date(s).</strong></div>').hide().appendTo('#span-request-vacation-time').slideDown(500);
        
        // exit early
        return;
    }


    // // hide the modal
    // modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/user-table.php", // the post url
        {
            uid: uid,
            vd: day,
            ven: $('#vacation-employee-note').val()
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        if (response == "0")
        {
            // disable the done buttons
            $('.btn-done').attr("disabled", true);

            // display the errors
            $('<div class="alert alert-danger"><strong>Your maximum allowed Vacation Days per year has been reached.</strong></div>').hide().appendTo('#span-request-vacation-time').slideDown(500);
        }
        else
        {
            // hide the modal
            modal.modal('hide');

            // print the response
            userTableDiv.html(response);
            userTableDiv.scrollTop(0);

            // reset the page title
            document.title = $("#pageTitleNew").val();
        }
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function removePersonalRequestTime(id)
{
    // variable to hold request
    var request;

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/removePersonalRequestTime.php", // the post url
        {
            id: id
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        $("#personal-request-"+id).hide(500);
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function removeVacationRequestTime(id)
{
    // variable to hold request
    var request;

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/removeVacationRequestTime.php", // the post url
        {
            id: id
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        $("#vacation-request-"+id).hide(500);
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function findPersonalDaysRemaining(selected_date)
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var personalDaysRemaining = $("#personal-days-remaining");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/findPersonalDaysRemaining.php", // the post url
        {
            d: selected_date
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        personalDaysRemaining.html(response);
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function findVacationDaysRemaining(selected_date)
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var vacationDaysRemaining = $("#vacation-days-remaining");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/findVacationDaysRemaining.php", // the post url
        {
            d: selected_date
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        vacationDaysRemaining.html(response);
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function findVacationDaysUsed(selected_date)
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var vacationDaysUsed = $("#vacation-days-used");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/findVacationDaysUsed.php", // the post url
        {
            d: selected_date
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        vacationDaysUsed.html(response);

        return response;
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function findPersonalDaysUsed(selected_date)
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var personalDaysUsed = $("#personal-days-used");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/findPersonalDaysUsed.php", // the post url
        {
            d: selected_date
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        personalDaysUsed.html(response);
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function showVacationDays()
{
    $("#show-vacation-days-used").toggle();
}


function showPersonalDays()
{
    $("#show-personal-days-used").toggle();
}


function viewReport(num)
{
	// optional: num (shows the given report)
    num = arguments[0] || 0;

    var start_date = "";
    var end_date = "";
    var fromViewReportModal = false;

    // check if the parameter is present
    if (num != 0)
    {
    	// the num is present
    	REPORT_NUMBER = num;

        // the "Do Payroll" report
        if (REPORT_NUMBER == 8)
        {
            start_date = $("#startPayrollDate").val();
            end_date = $("#endPayrollDate").val();
        }
    }
    else
    {
    	// the num is not present
    	REPORT_NUMBER = $('input[name=view-report-type]:checked').val();
        fromViewReportModal = true;
    }

	// variable to hold request
    var request;
    var modal = $(".modal");
    var clockTime = $("#clock-time");
    var viewHistoryThumbnailContainer = $("#view-history-thumbnail-container");
    var userTableDiv = $("#user-table-div");

    // hide the modal
    modal.modal('hide');

    // hide clock-time and view history
    clockTime.hide();
    viewHistoryThumbnailContainer.hide();

    // abort any pending request
    if (request) {request.abort();}


    if (fromViewReportModal)
    {
        if (REPORT_NUMBER === "do-payroll")
        {
            $('#do-payroll-link').trigger("click");
            setupModal('do-payroll');
            return;
        }

        if (REPORT_NUMBER === "admin-warning-messages")
        {
            setupModal('admin-warning-messages');
            return;
        }
    }


    // fire off the request
    request = $.post(
        "/tpl/view-report.php", // the post url
        {
            r: REPORT_NUMBER,
            d: CHANGE_WEEK_START_DAY,
            sd: start_date,
            ed: end_date
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function sortReport(report,sortType,sortOrder)
{
	// variable to hold request
    var request;
    var clockTime = $("#clock-time");
    var viewHistoryThumbnailContainer = $("#view-history-thumbnail-container");
    var userTableDiv = $("#user-table-div");

    // hide clock-time and view history
    clockTime.hide();
    viewHistoryThumbnailContainer.hide();

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/view-report.php", // the post url
        {
            r: report,
            st: sortType,
            so: sortOrder
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function sortPrintReport(report,sortType,sortOrder,start_date,end_date)
{
    // optional: start_date, end_date
    start_date = arguments[3] || "";
    end_date = arguments[4] || "";

	// variable to hold request
    var request;
    var userTableDiv = $("#user-table-div");

    // close the current print window
    window.close();

    // open the new print page
    var printPage = "/tpl/printReport.php?r="+report+"&st="+sortType+"&so="+sortOrder+"&sd="+start_date+"&ed="+end_date;
	var win = window.open(printPage, '_blank');
	win.focus();
}

function selectTime(date,record,action,cell,hour,minute,daytime,isBlank)
{
	// setup variables
	var selectedCell = $("#cell"+cell);
	var clockTime = $("#clock-time");
	var editTime = $("#edit-time");
	var editTimeHour = $("#edit-time-hour");
	var editTimeMinute = $("#edit-time-minute");
	var editTimeDaytime = $("#edit-time-daytime");
	var editTimeDelete = $("#edit-time-delete");
	var uid = $("#uid");
	var aid = $("#aid");
	CLOCK_ACTION = action;
	CLOCK_DATE = date;
	RECORD_NUMBER = record;

	// show the delete button
	editTimeDelete.show();

	// select OR unselect the cell
	if (selectedCell.hasClass("active"))
	{
		// unselect every cell
		$('td[id^="cell"].active').removeAttr('style');
		$('td[id^="cell"].active').removeClass("active");

		// hide edit-time
		editTime.hide();

		// IF this is the current week, show clock-time
		if ($("#isCurrentWeek").val() == "1") {clockTime.show();}

		// IF the selected user is the logged in admin, show clock-time
		// if (uid.val() === aid.val())
		// {
		// 	clockTime.show();
		// }

	}
	else
	{
		// unselect every cell
		$('td[id^="cell"].active').removeAttr('style');
		$('td[id^="cell"].active').removeClass("active");

		// select the chosen cell
		selectedCell.addClass("active");
		selectedCell.css('backgroundColor', '#2750ff');
		selectedCell.attr('style', 'background-color: #2750ff !important');
		selectedCell.css('color', '#fff');

		// IF this is cell is blank, hide the delete button
		if (isBlank == 1) {editTimeDelete.hide();}

		// hide clock time, show edit time
		clockTime.hide();
		editTime.show();
	}

	// setup the select time modal
	editTimeHour.val(hour);
	editTimeMinute.val(minute);
	editTimeDaytime.val(daytime);
}

function selectVacationTime(date,cell,hour,minute,isBlank)
{
    // setup variables
    var selectedCell = $("#vacation"+cell);
    var addVacation = $("#add-vacation");
    var editVacation = $("#edit-vacation");
    var editVacationHour = $("#edit-vacation-hour");
    var editVacationMinute = $("#edit-vacation-minute");
    var editVacationDelete = $("#edit-vacation-delete");
    var uid = $("#uid");
    var aid = $("#aid");
    VACATION_DATE = date;

    // show the delete button
    editVacationDelete.show();

    // select OR unselect the cell
    if (selectedCell.hasClass("active"))
    {
        // unselect every cell
        $('.vacation.active').removeAttr('style');
        $(".vacation.active").removeClass("active");

        // hide edit-vacation
        editVacation.hide();

        // show add-vacation
        addVacation.show();
    }
    else
    {
        // unselect every cell
        $('.vacation.active').removeAttr('style');
        $(".vacation.active").removeClass("active");

        // select the chosen cell
        selectedCell.addClass("active");
        selectedCell.css('backgroundColor', '#09db44');
        selectedCell.attr('style', 'background-color: #09db44 !important');
        selectedCell.css('color', '#000');

        // IF this is cell is blank, hide the delete button
        // if (isBlank == 1) {editVacationDelete.hide();}

        // hide add-vacation, show edit-vacation
        addVacation.hide();
        editVacation.show();
    }

    // setup the select vacation time modal
    editVacationHour.val(hour);
    editVacationMinute.val(minute);
}

function selectHolidayTime(date,cell,hour,minute,isBlank)
{
    // setup variables
    var selectedCell = $("#holiday"+cell);
    var addHoliday = $("#add-holiday");
    var editHoliday = $("#edit-holiday");
    var editHolidayHour = $("#edit-holiday-hour");
    var editHolidayMinute = $("#edit-holiday-minute");
    var editHolidayDelete = $("#edit-holiday-delete");
    var uid = $("#uid");
    var aid = $("#aid");
    HOLIDAY_DATE = date;

    // show the delete button
    editHolidayDelete.show();

    // select OR unselect the cell
    if (selectedCell.hasClass("active"))
    {
        // unselect every cell
        $('.holiday.active').removeAttr('style');
        $(".holiday.active").removeClass("active");

        // hide edit-holiday
        editHoliday.hide();

        // show add-holiday
        addHoliday.show();
    }
    else
    {
        // unselect every cell
        $('.holiday.active').removeAttr('style');
        $(".holiday.active").removeClass("active");

        // select the chosen cell
        selectedCell.addClass("active");
        selectedCell.css('backgroundColor', '#09db44');
        selectedCell.attr('style', 'background-color: #09db44 !important');
        selectedCell.css('color', '#000');

        // IF this is cell is blank, hide the delete button
        if (isBlank == 1) {editHolidayDelete.hide();}

        // hide add-holiday, show edit-holiday
        addHoliday.hide();
        editHoliday.show();
    }

    // setup the select holiday time modal
    editHolidayHour.val(hour);
    editHolidayMinute.val(minute);
}

function selectPersonalTime(date,cell,hour,minute,isBlank)
{
    // setup variables
    var selectedCell = $("#personal"+cell);
    var addPersonal = $("#add-personal");
    var editPersonal = $("#edit-personal");
    var editPersonalHour = $("#edit-personal-hour");
    var editPersonalMinute = $("#edit-personal-minute");
    var editPersonalDelete = $("#edit-personal-delete");
    var uid = $("#uid");
    var aid = $("#aid");
    PERSONAL_DATE = date;

    // show the delete button
    editPersonalDelete.show();

    // select OR unselect the cell
    if (selectedCell.hasClass("active"))
    {
        // unselect every cell
        $('.personal.active').removeAttr('style');
        $(".personal.active").removeClass("active");

        // hide edit-personal
        editPersonal.hide();

        // show add-personal
        addPersonal.show();
    }
    else
    {
        // unselect every cell
        $('.personal.active').removeAttr('style');
        $(".personal.active").removeClass("active");

        // select the chosen cell
        selectedCell.addClass("active");
        selectedCell.css('backgroundColor', '#fc07ff');
        selectedCell.attr('style', 'background-color: #fc07ff !important');
        selectedCell.css('color', '#000');

        // IF this is cell is blank, hide the delete button
        if (isBlank == 1) {editPersonalDelete.hide();}

        // hide add-personal, show edit-personal
        addPersonal.hide();
        editPersonal.show();
    }

    // setup the select personal time modal
    editPersonalHour.val(hour);
    editPersonalMinute.val(minute);
}

function selectSickTime(date,cell,hour,minute,isBlank,absence_type)
{
    // setup variables
    var selectedCell = $("#sick"+cell);
    var addSick = $("#add-sick");
    var editSick = $("#edit-sick");
    var editSickHour = $("#edit-sick-hour");
    var editSickMinute = $("#edit-sick-minute");
    var editSickAbsence = $("#edit-sick-time-absence");
    var editSickDelete = $("#edit-sick-delete");
    var uid = $("#uid");
    var aid = $("#aid");
    SICK_DATE = date;

    // show the delete button
    editSickDelete.show();

    // select OR unselect the cell
    if (selectedCell.hasClass("active"))
    {
        // unselect every cell
        $('.sick.active').removeAttr('style');
        $(".sick.active").removeClass("active");

        // hide edit-sick
        editSick.hide();

        // show add-sick
        addSick.show();
    }
    else
    {
        // unselect every cell
        $('.sick.active').removeAttr('style');
        $(".sick.active").removeClass("active");

        // select the chosen cell
        selectedCell.addClass("active");
        selectedCell.css('backgroundColor', '#ff26de');
        selectedCell.attr('style', 'background-color: #ff26de !important');
        selectedCell.css('color', '#000');

        // IF this cell is blank, hide the delete button
        if (isBlank == 1) {editSickDelete.hide();}

        // hide add-sick, show edit-sick
        addSick.hide();
        editSick.show();
    }

    // setup the select sick time modal
    editSickHour.val(hour);
    editSickMinute.val(minute);
    editSickAbsence.val(absence_type);
}

function selectForgot(date,record,action,cell,isBlank)
{
	// setup variables
	var selectedCell = $("#cell"+cell);
	var uid = $("#uid");
	var aid = $("#aid");
	forgotCell += date+","+record+","+action+"|";

	// IF this is cell is blank, select or unselect it
	if ( (isBlank == 1) && (isForgotActive) )
	{
		// select OR unselect the cell
		if (selectedCell.hasClass("active"))
		{
			// unselect this cell
			selectedCell.removeAttr('style');
			selectedCell.removeClass("active");

			// remove the forgotCell from the FORGOT_STRING
			FORGOT_STRING = str.replace(forgotCell, "");

			// IF there are no selected cells
			if ($('.active').length <= 0)
			{
				// disable the done button
				$('.btn-done').attr("disabled", true);
			}
		}
		else
		{
			// select the chosen cell
			selectedCell.addClass("active");
			selectedCell.css('backgroundColor', '#ff3030');
			selectedCell.css('color', '#fff');

			// add the forgotCell to the FORGOT_STRING
			FORGOT_STRING += forgotCell;

			// enable the done button
			$('.btn-done').removeAttr('disabled');
		}
	}
}

function saveForgot()
{
	// variable to hold request
    var request;
    var clockTime = $("#clock-time");
    var userTableDiv = $("#user-table-div");
    var userActionRow = $("#user-action-row");
    var clockIn = $("#clock-in");
    var clockOut = $("#clock-out");
	var forgotRow = $("#forgot-row");

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/saveForgot.php", // the post url
        {
            uid: $("#uid").val(),
            fs: FORGOT_STRING
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();

        // hide / show clock-in or clock-out
        if ($('#showClockInNew').val() === "1")
        {
        	clockIn.show();
        	clockOut.hide();
        	clockIn.removeClass("hide").addClass("show");
        	clockOut.removeClass("show").addClass("hide");
        }
        else
        {
        	clockIn.hide();
        	clockOut.show();
        	clockIn.removeClass("show").addClass("hide");
        	clockOut.removeClass("hide").addClass("show");
        }

        // hide or show clock-time
        if ($('#hideClockTime').val() === "1") {clockTime.hide();}
        else {clockTime.show();}

        // hide the forgot-row
		forgotRow.hide(500);

		// show the user-action-row
		userActionRow.show(500);
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function deleteTime()
{
	// variable to hold request
    var request;
    var modal = $(".modal");
    var clockTime = $("#clock-time");
	var editTime = $("#edit-time");
    var userTableDiv = $("#user-table-div");

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/deleteTime.php", // the post url
        {
            uid: $("#uid").val(),
            a: CLOCK_ACTION,
            d: CLOCK_DATE,
            r: RECORD_NUMBER
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();

        // hide edit-time
        editTime.hide();

        // hide or show clock-time
        if ($('#hideClockTime').val() === "1") {clockTime.hide();}
        else {clockTime.show();}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function saveTime()
{
	// variable to hold request
    var request;
    var modal = $(".modal");
    var clockTime = $("#clock-time");
	var editTime = $("#edit-time");
    var userTableDiv = $("#user-table-div");

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/saveTime.php", // the post url
        {
            uid: $("#uid").val(),
            a: CLOCK_ACTION,
            d: CLOCK_DATE,
            r: RECORD_NUMBER,
            hour: $("#edit-time-hour").val(),
            minute: $("#edit-time-minute").val(),
            daytime: $("#edit-time-daytime").val()
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();

        // hide edit-time
        editTime.hide();

        // hide or show clock-time
        if ($('#hideClockTime').val() === "1") {clockTime.hide();}
        else {clockTime.show();}
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function updatePersonalDayRequestStatus(review_id)
{
    // variable to hold request
    var request;

    var status = $('#select_status_'+review_id).val();

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/updatePersonalDayRequestStatus.php", // the post url
        {
            review_id: review_id,
            status: status
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // update the report
        viewReport(7);

     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function updateVacationTimeRequestStatus(request_id)
{
    // error checking
    if (request_id == "") {return;}

    // setup variables
    var request;
    var status = $('#select_status_'+request_id).val();
    var request_dates = "";
    var request_hours = "";

    // get the request dates and hours
    $(".request-hours-"+request_id).each(function()
    {
        request_dates += $(this).data("date")+"||";
        request_hours += $(this).val()+"||";
    });

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/updateVacationTimeRequestStatus.php", // the post url
        {
            request_id: request_id,
            status: status,
            dates: request_dates,
            hours: request_hours
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // update the report
        viewReport(12);

     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function updatePersonalDayRequestGlobalStatus()
{
    // variable to hold request
    var request;

    // setup variables
    var pdrs = $("#personal-day-request-status").val();

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/updatePersonalDayRequestGlobalStatus.php", // the post url
        {
            pdrs: pdrs
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // refresh the report
        viewReport(7);
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function updateVacationTimeRequestGlobalStatus()
{
    // variable to hold request
    var request;

    // setup variables
    var vtrs = $("#vacation-time-request-status").val();

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/updateVacationDayRequestGlobalStatus.php", // the post url
        {
            vtrs: vtrs
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // refresh the report
        viewReport(12);
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function updateHasKey(uid)
{
    // variable to hold request
    var request;

    var haskey = $('#select_has_key_'+uid).val();

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/updateHasKey.php", // the post url
        {
            uid: uid,
            hk: haskey
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // update the total value of the table
        var hasKeyCount = 0;
        $('.has-key').each(function()
        {    
            if ($(this).val() !== "0") {hasKeyCount++;}
        });

        $("#tableTotalValue").html(hasKeyCount);

     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function updateAlarmCode(uid)
{
    // variable to hold request
    var request;

    var alarmCode = $('#input_alarm_code_'+uid).val();

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/updateAlarmCode.php", // the post url
        {
            uid: uid,
            ac: alarmCode
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // update the total value of the table
        var alarmCodeCount = 0;
        $('.alarm-code').each(function()
        {    
            if ($(this).val() !== "") {alarmCodeCount++;}
        });

        $("#tableTotalValue").html(alarmCodeCount);

     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function updatePersonalDayAdminNote(review_id, admin_note)
{
    // variable to hold request
    var request;

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/updatePersonalDayAdminNote.php", // the post url
        {
            review_id: review_id,
            admin_note: admin_note
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // update the total value of the table
        var adminNoteCount = 0;
        $('.admin_note').each(function()
        {    
            if ($(this).val() !== "") {adminNoteCount++;}
        });

        $("#tableTotalValue").html(adminNoteCount);

     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function updateVacationTimeAdminNote(request_id, admin_note)
{
    // variable to hold request
    var request;

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/updateVacationTimeAdminNote.php", // the post url
        {
            request_id: request_id,
            admin_note: admin_note
        },
        function(response,status,xhr) {
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // update the total value of the table
        var adminNoteCount = 0;
        $('.admin_note').each(function()
        {    
            if ($(this).val() !== "") {adminNoteCount++;}
        });

        $("#tableTotalValue").html(adminNoteCount);

     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function saveEmployeeReview(reviewID)
{
    // variable to hold request
    var request;

    // setup variables
    var reviewDate = $("#review-date-"+reviewID).val();
    var reviewed = $("#reviewed-select-"+reviewID).val();
    var reviewNote = $("#review-note-"+reviewID).val();


    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/saveEmployeeReview.php", // the post url
        {
            erid: reviewID,
            err: reviewed,
            erd: reviewDate,
            ern: reviewNote
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // refresh the report
        viewReport(11);

        setTimeout(function ()
        {
            $(".review-save-message").html('').show();
            $('<span class="text-success">Saved.</span>').hide().appendTo("#review-save-message-"+reviewID).slideDown(500);
        }, 700);
 
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );

        $(".review-save-message").html('').show();
        $('<span class="text-danger">Error</span>').hide().appendTo("#review-save-message-"+reviewID).slideDown(500);
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function updateEmployeeReviewTimeFrame()
{
    // variable to hold request
    var request;

    // setup variables
    var ertf = $("#employee-review-timeframe").val();

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/updateEmployeeReviewTimeFrame.php", // the post url
        {
            ertf: ertf
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // refresh the report
        viewReport(11);
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function deleteEmployeeReviewNote(reviewID, reviewNoteID)
{
    // variable to hold request
    var request;


    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/deleteEmployeeReviewNote.php", // the post url
        {
            erid: reviewID,
            ernid: reviewNoteID
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // refresh the report
        viewReport(11);
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );

        $(".review-save-message").html('').show();
        $('<span class="text-danger">Error</span>').hide().appendTo("#review-save-message-"+reviewID).slideDown(500);
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function deleteVacationTime(date)
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var addVacation = $("#add-vacation");
    var editVacation = $("#edit-vacation");
    var userTableDiv = $("#user-table-div");
    var uid = $("#uid").val();
    var hour = 0;
    var minute = 0;

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/user-table.php", // the post url
        {
            uid: uid,
            d: VACATION_DATE,
            n: uid,
            vh: 0,
            vm: 0,
            ve: 2
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // reset the uid to the new user id
        $("#uid").val(USER_ID);

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();

        // hide edit-vacation
        editVacation.hide();

        // show add-vacation
        addVacation.show();
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

// function saveVacationTime()
// {
//     // variable to hold request
//     var request;
//     var modal = $(".modal");
//     var clockTime = $("#clock-time");
//     var addVacation = $("#add-vacation");
//     var editVacation = $("#edit-vacation");
//     var userTableDiv = $("#user-table-div");

//     // hide the modal
//     modal.modal('hide');

//     // abort any pending request
//     if (request) {request.abort();}

//     // fire off the request
//     request = $.post(
//         "/tpl/saveVacationTime.php", // the post url
//         {
//             uid: $("#uid").val(),
//             d: VACATION_DATE,
//             hour: $("#edit-vacation-hour").val(),
//             minute: $("#edit-vacation-minute").val()
//         },
//         function(response,status,xhr){
//             //alert("responseText: "+response.responseText);
//         });

//     // callback handler that will be called on success
//     request.done(function (response, textStatus, jqXHR) {
//         console.log("Successful request");

//         // print the response
//         userTableDiv.html(response);
//         userTableDiv.scrollTop(0);

//         // reset the page title
//         document.title = $("#pageTitleNew").val();

//         // hide edit-vacation
//         editVacation.hide();

//         // hide or show clock-time
//         if ($('#hideClockTime').val() === "1") {clockTime.hide();}
//         else {clockTime.show();}
//      });

//     // callback handler that will be called on failure
//     request.fail(function (jqXHR, textStatus, errorThrown) {
//         // log the error to the console
//         console.error(
//             "The following error occured: "+
//             textStatus, errorThrown
//         );
//     });

//     // callback handler that will be called regardless
//     // if the request failed or succeeded
//     request.always(function () {
//         console.log("A request was sent.");
//     });
// }

function deleteHolidayTime(date)
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var addHoliday = $("#add-holiday");
    var editHoliday = $("#edit-holiday");
    var userTableDiv = $("#user-table-div");
    var uid = $("#uid").val();
    var hour = 0;
    var minute = 0;

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/user-table.php", // the post url
        {
            uid: uid,
            d: HOLIDAY_DATE,
            n: uid,
            hh: 0,
            hm: 0,
            he: 2
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // reset the uid to the new user id
        $("#uid").val(USER_ID);

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();

        // hide edit-holiday
        editHoliday.hide();

        // show add-holiday
        addHoliday.show();
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

// function saveHolidayTime()
// {
//     // variable to hold request
//     var request;
//     var modal = $(".modal");
//     var clockTime = $("#clock-time");
//     var addHoliday = $("#add-holiday");
//     var editHoliday = $("#edit-holiday");
//     var userTableDiv = $("#user-table-div");

//     // hide the modal
//     modal.modal('hide');

//     // abort any pending request
//     if (request) {request.abort();}

//     // fire off the request
//     request = $.post(
//         "/tpl/saveHolidayTime.php", // the post url
//         {
//             uid: $("#uid").val(),
//             d: HOLIDAY_DATE,
//             hour: $("#edit-holiday-hour").val(),
//             minute: $("#edit-holiday-minute").val()
//         },
//         function(response,status,xhr){
//             //alert("responseText: "+response.responseText);
//         });

//     // callback handler that will be called on success
//     request.done(function (response, textStatus, jqXHR) {
//         console.log("Successful request");

//         // print the response
//         userTableDiv.html(response);
//         userTableDiv.scrollTop(0);

//         // reset the page title
//         document.title = $("#pageTitleNew").val();

//         // hide edit-holiday
//         editHoliday.hide();

//         // hide or show clock-time
//         if ($('#hideClockTime').val() === "1") {clockTime.hide();}
//         else {clockTime.show();}
//      });

//     // callback handler that will be called on failure
//     request.fail(function (jqXHR, textStatus, errorThrown) {
//         // log the error to the console
//         console.error(
//             "The following error occured: "+
//             textStatus, errorThrown
//         );
//     });

//     // callback handler that will be called regardless
//     // if the request failed or succeeded
//     request.always(function () {
//         console.log("A request was sent.");
//     });
// }

function deletePersonalTime(date)
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var addPersonal = $("#add-personal");
    var editPersonal = $("#edit-personal");
    var userTableDiv = $("#user-table-div");
    var uid = $("#uid").val();
    var hour = 0;
    var minute = 0;

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/user-table.php", // the post url
        {
            uid: uid,
            d: PERSONAL_DATE,
            n: uid,
            ph: 0,
            pm: 0,
            pe: 2
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // reset the uid to the new user id
        $("#uid").val(USER_ID);

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();

        // hide edit-personal
        editPersonal.hide();

        // show add-personal
        addPersonal.show();
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

// function savePersonalTime()
// {
//     // variable to hold request
//     var request;
//     var modal = $(".modal");
//     var clockTime = $("#clock-time");
//     var addPersonal = $("#add-personal");
//     var editPersonal = $("#edit-personal");
//     var userTableDiv = $("#user-table-div");

//     // hide the modal
//     modal.modal('hide');

//     // abort any pending request
//     if (request) {request.abort();}

//     // fire off the request
//     request = $.post(
//         "/tpl/savePersonalTime.php", // the post url
//         {
//             uid: $("#uid").val(),
//             d: PERSONAL_DATE,
//             hour: $("#edit-personal-hour").val(),
//             minute: $("#edit-personal-minute").val()
//         },
//         function(response,status,xhr){
//             //alert("responseText: "+response.responseText);
//         });

//     // callback handler that will be called on success
//     request.done(function (response, textStatus, jqXHR) {
//         console.log("Successful request");

//         // print the response
//         userTableDiv.html(response);
//         userTableDiv.scrollTop(0);

//         // reset the page title
//         document.title = $("#pageTitleNew").val();

//         // hide edit-personal
//         editPersonal.hide();

//         // hide or show clock-time
//         if ($('#hideClockTime').val() === "1") {clockTime.hide();}
//         else {clockTime.show();}
//      });

//     // callback handler that will be called on failure
//     request.fail(function (jqXHR, textStatus, errorThrown) {
//         // log the error to the console
//         console.error(
//             "The following error occured: "+
//             textStatus, errorThrown
//         );
//     });

//     // callback handler that will be called regardless
//     // if the request failed or succeeded
//     request.always(function () {
//         console.log("A request was sent.");
//     });
// }

function deleteSickTime(date)
{
    // variable to hold request
    var request;
    var modal = $(".modal");
    var addSick = $("#add-sick");
    var editSick = $("#edit-sick");
    var userTableDiv = $("#user-table-div");
    var uid = $("#uid").val();
    var hour = 0;
    var minute = 0;

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/user-table.php", // the post url
        {
            uid: uid,
            d: SICK_DATE,
            n: uid,
            sh: 0,
            sm: 0,
            se: 2
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // reset the uid to the new user id
        $("#uid").val(USER_ID);

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();

        // hide edit-sick
        editSick.hide();

        // show add-sick
        addSick.show();
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function saveAccountSettings(skipErrors)
{
    // optional: skipErrors (1 = skip error checking, else don't skip error checking)
    skipErrors = arguments[0] || 0;

    // variable to hold request
    var request;
    var modal = $(".modal");
    var userTableDiv = $("#user-table-div");
    var firstName = $('#firstName').val();
    var middleName = $('#middleName').val();
    var lastName = $('#lastName').val();
    var email = $('#email').val();
    var username = $('#username').val();
    var password = $('#password').val();
    var confirmPassword = $('#confirmPassword').val();
    var payrate = $('#payrate').val();
    var salary = $('#salary').val();
    var paytype = $('#paytype').val();
    var schedule = $('#schedule').val();
    var company = $('#company').val();
    var peachID = $('#peachID').val();
    var employeeType = $('#employeeType').val();
    var status = $('#status').val();
    var start_time = $('#start_time_hour').val()+":"+$('#start_time_minute').val()+":00";
    var daysBetweenReview = $('#daysBetweenReview').val();
    var personalDays = $('#personalDays').val();
    var vacationDays = $('#vacationDays').val();
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    var birthDate = $('#birthDate').val();

    var adminBilling = $('#adminBilling').val();
    var adminIncidentReports = $('#adminIncidentReports').val();
    var adminMilling = $('#adminMilling').val();
    var adminQualityControl = $('#adminQualityControl').val();
    var adminShipping = $('#adminShipping').val();

    var signsIncidentReports = $('#signsIncidentReports').val();
    var salesCalendarActive = $('#salesCalendarActive').val();
    var marketingDept = $('#marketingDept').val();
    var dlpActive = $('#dlpActive').val();
    var dlpAdmin = $('#dlpAdmin').val();
    var overtimeAlerts = $('#overtimeAlerts').val();
    var enforceIPAddress = $('#enforceIPAddress').val();

    
    ERRORS = "";

    // IF skipErrors != 1, check for errors
    if (skipErrors != 1)
    {
        // check that the first name is not empty
        if (firstName == "")
        {
            ERRORS += '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <strong>Invalid First Name</strong></div>';
        }

        // check the email address
        if ( (email != "") && (!isValidEmailAddress(email)) )
        {
            ERRORS += '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <strong>Invalid Email Address</strong></div>';
        }

        // check that the username is not shorter than 3 characters
        if ( (username == "") || (username.length < 3) )
        {
            ERRORS += '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <strong>Username must be at least 3 characters</strong></div>';
        }

        // check that the passwords are not empty
        if ( (password == "") || (confirmPassword == "") || (password.length < 3) || (confirmPassword.length < 3) )
        {
            ERRORS += '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <strong>Password must be at least 3 characters</strong></div>';
        }

        // check that the passwords match
        if (password !== confirmPassword)
        {
            ERRORS += '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <strong>Passwords do not match</strong></div>';
        }

        if ( (username != "") && (username.length > 2) ) {isUniqueUsername(username,1);}
        else
        {
            // display the error messages
            $('#span-errors').html('');
            $(ERRORS).hide().appendTo('#span-errors').slideDown(500);
        }

        return;
    }

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/saveAccountSettings.php", // the post url
        {
            uid: $("#uid").val(),
            fn: firstName,
            mn: middleName,
            ln: lastName,
            em: email,
            un: username,
            pw: password,
            cpw: confirmPassword,
            pr: payrate,
            sl: salary,
            pt: paytype,
            ft: schedule,
            cm: company,
            peachID: peachID,
            et: employeeType,
            st: status,
            stm: start_time,
            dbr: daysBetweenReview,
            pd: personalDays,
            vd: vacationDays,
            sd: startDate,
            ed: endDate,
            bd: birthDate,
            adminBilling: adminBilling,
            adminIncidentReports: adminIncidentReports,
            adminMilling: adminMilling,
            adminQualityControl: adminQualityControl,
            adminShipping: adminShipping,
            signsIncidentReports: signsIncidentReports,
            salesCalendarActive: salesCalendarActive,
            marketingDept: marketingDept,
            dlpActive: dlpActive,
            dlpAdmin: dlpAdmin,
            overtimeAlerts: overtimeAlerts,
            enforceIPAddress: enforceIPAddress
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function addUser(skipErrors)
{
    // optional: skipErrors (1 = skip error checking, else don't skip error checking)
    skipErrors = arguments[0] || 0;

    // variable to hold request
    var request;
    var modal = $(".modal");
    var userTableDiv = $("#user-table-div");
    var firstName = $('#firstName').val();
    var middleName = $('#middleName').val();
    var lastName = $('#lastName').val();
    var email = $('#email').val();
    var username = $('#username').val();
    var password = $('#password').val();
    var confirmPassword = $('#confirmPassword').val();
    var payrate = $('#payrate').val();
    var salary = $('#salary').val();
    var paytype = $('#paytype').val();
    var schedule = $('#schedule').val();
    var company = $('#company').val();
    var employeeType = $('#employeeType').val();
    var status = $('#status').val();
    var start_time = $('#start_time_hour').val()+":"+$('#start_time_minute').val()+":00";
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    var birthDate = $('#birthDate').val();
    ERRORS = "";

    // IF skipErrors != 1, check for errors
    if (skipErrors != 1)
    {
        // check that the first name is not empty
        if (firstName == "")
        {
            ERRORS += '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <strong>Invalid First Name</strong></div>';
        }

        // check the email address
        if ( (email != "") && (!isValidEmailAddress(email)) )
        {
            ERRORS += '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <strong>Invalid Email Address</strong></div>';
        }

        // check that the username is not shorter than 3 characters
        if ( (username == "") || (username.length < 3) )
        {
            ERRORS += '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <strong>Username must be at least 3 characters</strong></div>';
        }

        // check that the passwords are not shorter than 3 characters
        if ( (password == "") || (confirmPassword == "") || (password.length < 3) || (confirmPassword.length < 3) )
        {
            ERRORS += '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <strong>Password must be at least 3 characters</strong></div>';
        }

        // check that the passwords match
        if (password !== confirmPassword)
        {
            ERRORS += '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <strong>Passwords do not match</strong></div>';
        }

        if ( (username != "") && (username.length > 2) ) {isUniqueUsername(username,2);}
        else
        {
            // display the error messages
            $('#span-errors').html('');
            $(ERRORS).hide().appendTo('#span-errors').slideDown(500);
        }

        return;
    }

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/addUser.php", // the post url
        {
            uid: $("#uid").val(),
            fn: firstName,
            mn: middleName,
            ln: lastName,
            em: email,
            un: username,
            pw: password,
            cpw: confirmPassword,
            pr: payrate,
            sl: salary,
            pt: paytype,
            ft: schedule,
            cm: company,
            et: employeeType,
            st: status,
            stm: start_time,
            sd: startDate,
            ed: endDate,
            bd: birthDate
        },
        function(response,status,xhr) {
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // print the response
        userTableDiv.html(response);
        userTableDiv.scrollTop(0);

        // reset the page title
        document.title = $("#pageTitleNew").val();
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function isUniqueUsername(username,from)
{
    // variable to hold request
    var request;
    var userTableDiv = $("#user-table-div");
    var errors = "";

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/isUniqueUsername.php", // the post url
        {
            uid: $("#uid").val(),
            un: username
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // IF the response == 0, then the username is taken or shorter than 3 characters
        if (response === "0")
        {
            ERRORS += '<div class="alert alert-danger"><span class="glyphicon glyphicon-remove"></span> <strong>Username is already taken</strong></div>';
        }

        // IF there are errors, display the error messages and return false
        if (ERRORS !== "")
        {
            $('#span-errors').html('');
            $(ERRORS).hide().appendTo('#span-errors').slideDown(500);
            return false;
        }
        else
        {
            if (from == 1) {saveAccountSettings(1);}
            if (from == 2) {addUser(1);}
        }
        
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function saveMessageBoard()
{
    // variable to hold request
    var request;
    var modal = $(".modal");

    // hide the modal
    modal.modal('hide');

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/saveMessageBoard.php", // the post url
        {
            uid: $("#uid").val(),
            title: $('#message-board-text-title').val(),
            message: $('#message-board-textarea').val()
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}

function saveIPAddress(company_id)
{
    // setup variables
    var request;
    var new_ip = $('#company-new-ip-'+company_id).val();

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/saveIPAddress.php", // the post url
        {
            c: company_id,
            ip: new_ip
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // console.log("response: "+response);

        // clear the input
        $('#company-new-ip-'+company_id).val('');

        // update the company ip address list
        $('<div id="ip-row-'+response+'" class="row ip-row"><div class="col-xs-8 col-sm-3">'+new_ip+'</div><div class="col-xs-4 col-sm-9"><button class="btn btn-xs btn-danger" onclick="removeIPAddress('+response+')"><span class="fa fa-remove"></span></button></div></div>').hide().appendTo("#ip-address-container-"+company_id).slideDown(500);
     
     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function removeIPAddress(id)
{
    // setup variables
    var request;

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/removeIPAddress.php", // the post url
        {
            id: id
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful request");

        // update the company ip address list
        $('#ip-row-'+id).hide();

     });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A request was sent.");
    });
}


function showForgotOverlay()
{
	// setup variables
	var userActionRow = $("#user-action-row");
	var forgotRow = $("#forgot-row");

	// disable the done button
	$('.btn-done').attr("disabled", true);

	// turn the isForgotActive ON
	isForgotActive = true;

	// hide the user-action-row
	userActionRow.hide(500);

	// show the forgot-row
	forgotRow.show(500);
}

function hideForgotOverlay()
{
	// setup variables
	var userActionRow = $("#user-action-row");
	var forgotRow = $("#forgot-row");

	// unselect every cell
	$(".active").removeAttr('style');
	$(".active").removeClass("active");

	// turn the isForgotActive ON
	isForgotActive = false;

	// hide the forgot-row
	forgotRow.hide(500);

	// show the user-action-row
	userActionRow.show(500);
}

function closeModal()
{
    // variable to hold request
    var request;
    var modal = $(".modal");

    // hide the modal
    modal.modal('hide');
}

function login()
{
    // setup variables
    var request;
    var username = $("#username").val();
    var password = $("#password").val();

    // error checking
    if ( (username.length <= 2) || (password.length <= 4) )
    {
        $("#login-message").html('').show();
        $('<div class="alert alert-danger"><strong>Invalid Username or Password. Please Login Again.</strong></div>').hide().appendTo('#login-message').slideDown(500);
        return false;
    }

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/submitLogin.php", // the post url
        {
            u: username,
            p: password
        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful login");
        
        // check the response
        if (response === "0") // Incorrect Login information
        {
        	$("#login-message").html('').show();
        	$('<div class="alert alert-danger"><strong>Incorrect Username or Password. Please Login Again.</strong></div>').hide().appendTo('#login-message').slideDown(500);
        }
        else // Correct Login Information
        {
        	$("#main").fadeOut(1000);
    		setTimeout(function ()
    		{
    			// $("#main").hide(1000);
		        $("#main").html(response);
		        $("#main").show(1300);
                document.title = $("#pageTitle").val();
    		}, 1000);
        }
    });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A login request was sent.");
    });
}

function logout()
{
	// variable to hold request
    var request;

    // abort any pending request
    if (request) {request.abort();}

    // fire off the request
    request = $.post(
        "/tpl/submitLogout.php", // the post url
        {

        },
        function(response,status,xhr){
            //alert("responseText: "+response.responseText);
        });

    // callback handler that will be called on success
    request.done(function (response, textStatus, jqXHR) {
        console.log("Successful logout");
        
        $("#main").hide(1000);
        setTimeout(function ()
		{
			// $("#main").hide(1000);
	        $("#main").html(response);
	        $("#main").show(1000);
            document.title = PAGE_TITLE;
		}, 1000);
    });

    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown) {
        // log the error to the console
        console.error(
            "The following error occured: "+
            textStatus, errorThrown
        );
    });

    // callback handler that will be called regardless
    // if the request failed or succeeded
    request.always(function () {
        console.log("A logout request was sent.");
    });
}


function unknownStartDate()
{
    $('.startDate').val("0000-00-00");
    $('#main-start-date-header').html("");
    $('#start-date-modal-button').trigger("click");
}


function unknownEndDate()
{
    $('.endDate').val("0000-00-00");
    $('#main-end-date-header').html("");
    $('#end-date-modal-button').trigger("click");
}


function unknownBirthDate()
{
    $('.birthDate').val("0000-00-00");
    $('#main-birth-date-header').html("");
    $('#birth-date-modal-button').trigger("click");
}


function hide(e,t) {$(e).hide(t);}

function show(e,t) {$(e).show(t);}

function toggle(e,t) {$(e).toggle(t);}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
}
