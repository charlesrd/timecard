<?php


require_once("config.php");


// setup variables
$EMPLOYEE_ID = array();
$EMPLOYEE_NAME = array();
$DEFAULT_HR = 8;
$DEFAULT_MIN = 0;

?>
<div class="modal fade" id="personal-time-modal" tabindex="-1" role="dialog" aria-labelledby="personal-time-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="personal-time-modal-title"><strong>Add Personal Time</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="personal-date-modal-button" class="btn btn-default personal-time-dropdown" onclick="toggle('#main-personal-date',500)">
                                <strong>Choose a Date:</strong> <span id="main-personal-date-header" class="main-personal-header"><?php print date("F j, Y"); ?></span> <span id="personal-date-caret" class="personal-time-caret caret"></span>
                            </button>
                            <div id="main-personal-date">
                                <div id="personal-time-datepicker"></div>
                                <input class="startDate" type="hidden" id="startPersonalDate" name="startPersonalDate" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="personal-time-modal-button" class="btn btn-default personal-time-dropdown" onclick="toggle('#main-personal-time',500)">
                                <strong>Choose a Time:</strong> <span id="main-personal-time-header" class="main-personal-header"><?php print $DEFAULT_HR." hr ".$DEFAULT_MIN." min"; ?></span> <span id="personal-time-caret" class="caret"></span>
                            </button>
                            <div id="main-personal-time">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <select id="personal-time-hour" name="personal-time-hour" class="form-control input-lg select-time">
                                            <?php
                                            $default = $DEFAULT_HR;
                                            for ($i=0; $i<=24; $i++)
                                            {
                                                if ($i == $default) {print '<option value="'.$i.'" selected="selected">'.$i.'</option>';}
                                                else {print '<option value="'.$i.'">'.$i.'</option>';}
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-2 personal-time-time-label"><span>hr</span></div>
                                    <div class="col-xs-4">
                                        <select id="personal-time-minute" name="personal-time-minute" class="form-control input-lg select-time">
                                            <?php
                                            for ($i=0; $i<=59; $i++)
                                            {
                                                print '<option value="'.$i.'">'.$i.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-2 personal-time-time-label"><span>min</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="personal-time-row" class="row">
                        <div class="col-xs-5">
                            <h4><strong>Choose a User:</strong></h4>
                        </div>
                        <div class="col-xs-7">
                            <select id="personal-time-user" name="personal-time-user" class="form-control input-lg select-time">
                                <?php

                                    // get the list of all the Employees in the company, sorted by first name
                                    $sql = "SELECT E.id,
                                            CONCAT(E.first_name,' ',E.last_name) AS name
                                            FROM Employee E
                                            WHERE E.companyID IN
                                            (
                                                SELECT CA.cid
                                                FROM Company_Administrator CA
                                                WHERE CA.aid = $adminID
                                            )
                                            AND E.active = 1
                                            ORDER BY E.first_name ASC";

                                    // $sql = "SELECT id, CONCAT(E.first_name,' ',E.last_name) AS name
                                    //         FROM Employee E
                                    //         ORDER BY E.first_name ASC";
                                    $query = mysql_query($sql);

                                    while($user = mysql_fetch_object($query))
                                    {
                                        $EMPLOYEE_ID[] = $user->id;
                                        $EMPLOYEE_NAME[] = $user->name;
                                        print '<option id="personal-time-user-'.$user->id.'" value="'.$user->id.'">'.$user->name.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="addPersonalTime(0)" id="personal-time-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-child"></span> Add Personal Time</button>
            </div>
        </div>
    </div>
</div>
<!-- Internal Personal Time Javascript -->
<script type="text/javascript">

    // setup variables
    var startDate;
    var endDate;
    var d = new Date();
    var month = d.getMonth()+1;
    var day = d.getDate();
    month = format2Digit(month);
    day = format2Digit(day);
    var today = d.getFullYear()+"-"+month+"-"+day;
    var DEFAULT_HR = 8;
    var DEFAULT_MIN = 0;
    var DEFAULT_NAME_ID = 0;


    $('input:radio').change(function(){
        $('.btn-done').removeAttr('disabled');
    });

    $(".select-time").change(function() {
        $('.btn-done').removeAttr('disabled');
    });

    $(".select-type").change(function() {
        $('.btn-done').removeAttr('disabled');
    });

    $("#personal-time-datepicker").datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            $('.startDate').val($.datepicker.formatDate("yy-mm-dd", date));
            $('#main-personal-date-header').html($.datepicker.formatDate("MM d, yy", date));
            $('#personal-date-modal-button').trigger("click");
            $('.btn-done').removeAttr('disabled');
        }
    });

    $("#personal-time-hour").change(function() {

        if ($("#personal-time-hour").val() == 24)
        {
            $("#personal-time-minute").val(0);
            $("#personal-time-minute").attr("disabled", true);
        }
        else
        {
            $("#personal-time-minute").removeAttr('disabled');
        }

        DEFAULT_HR = $('#personal-time-hour').val();
        $('#main-personal-time-header').html(DEFAULT_HR+" hr "+DEFAULT_MIN+" min");

        // reset the employee list times
        $('.employee-list-hr').val(DEFAULT_HR);
    });

    $('#personal-time-minute').change(function() {
        DEFAULT_MIN = $('#personal-time-minute').val();
        $('#main-personal-time-header').html(DEFAULT_HR+" hr "+DEFAULT_MIN+" min");

        // reset the employee list times
        if (DEFAULT_MIN == 0) {$('.employee-list-min').val('');}
        else {$('.employee-list-min').val(DEFAULT_MIN);}
    });

    $("#personal-time-user").change(function() {

        // IF All Employees is selected
        if ($("#personal-time-user").val() == 0)
        {
            $('#personal-time-employee-list').show(500);
        }
        else
        {
            $('#personal-time-employee-list').hide(500);
        }
    });

    $('#personal-date-modal-button').click(function() {
        if ($('#personal-date-caret').hasClass('caret-reversed'))
        {
            $('#personal-date-caret').removeClass('caret-reversed');
        }
        else {$('#personal-date-caret').addClass('caret-reversed');}
    });

    $('#personal-time-modal-button').click(function() {
        if ($('#personal-time-caret').hasClass('caret-reversed'))
        {
            $('#personal-time-caret').removeClass('caret-reversed');
        }
        else {$('#personal-time-caret').addClass('caret-reversed');}
    });

    // set the start date to today for everything with the startDate class
    $('.startDate').val(today);

</script>