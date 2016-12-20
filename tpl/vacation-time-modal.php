<?php


require_once("config.php");


// setup variables
$EMPLOYEE_ID = array();
$EMPLOYEE_NAME = array();
$DEFAULT_HR = 8;
$DEFAULT_MIN = 0;

?>
<div class="modal fade" id="vacation-time-modal" tabindex="-1" role="dialog" aria-labelledby="vacation-time-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="vacation-time-modal-title"><strong>Add Vacation Time</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="vacation-date-modal-button" class="btn btn-default vacation-time-dropdown" onclick="toggle('#main-vacation-date',500)">
                                <strong>Choose a Date:</strong> <span id="main-vacation-date-header" class="main-vacation-header"><?php print date("F j, Y"); ?></span> <span id="vacation-date-caret" class="vacation-time-caret caret"></span>
                            </button>
                            <div id="main-vacation-date">
                                <div id="vacation-time-datepicker"></div>
                                <input class="startDate" type="hidden" id="startVacationDate" name="startVacationDate" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="vacation-time-modal-button" class="btn btn-default vacation-time-dropdown" onclick="toggle('#main-vacation-time',500)">
                                <strong>Choose a Time:</strong> <span id="main-vacation-time-header" class="main-vacation-header"><?php print $DEFAULT_HR." hr ".$DEFAULT_MIN." min"; ?></span> <span id="vacation-time-caret" class="caret"></span>
                            </button>
                            <div id="main-vacation-time">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <select id="vacation-time-hour" name="vacation-time-hour" class="form-control input-lg select-time">
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
                                    <div class="col-xs-2 vacation-time-time-label"><span>hr</span></div>
                                    <div class="col-xs-4">
                                        <select id="vacation-time-minute" name="vacation-time-minute" class="form-control input-lg select-time">
                                            <?php
                                            for ($i=0; $i<=59; $i++)
                                            {
                                                print '<option value="'.$i.'">'.$i.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-2 vacation-time-time-label"><span>min</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="vacation-time-row" class="row">
                        <div class="col-xs-5">
                            <h4><strong>Choose a User:</strong></h4>
                        </div>
                        <div class="col-xs-7">
                            <select id="vacation-time-user" name="vacation-time-user" class="form-control input-lg select-time">
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

                                    $query = mysql_query($sql);

                                    while($user = mysql_fetch_object($query))
                                    {
                                        $EMPLOYEE_ID[] = $user->id;
                                        $EMPLOYEE_NAME[] = $user->name;
                                        print '<option id="vacation-time-user-'.$user->id.'" value="'.$user->id.'">'.$user->name.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 margin-top-10">
                            <h4><strong><label class="control-label" for="vacation-time-note">Note:</label></strong></h4>
                        </div>

                        <div class="col-xs-12">
                            <textarea id="vacation-time-note" rows="3" class="form-control input-lg" placeholder="Type your note here..." value=""></textarea>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="addVacationTime(0)" id="vacation-time-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-sun-o"></span> Add Vacation Time</button>
            </div>
        </div>
    </div>
</div>
<!-- Internal Vacation Time Javascript -->
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

    $("#vacation-time-datepicker").datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            $('.startDate').val($.datepicker.formatDate("yy-mm-dd", date));
            $('#main-vacation-date-header').html($.datepicker.formatDate("MM d, yy", date));
            $('#vacation-date-modal-button').trigger("click");
            $('.btn-done').removeAttr('disabled');
        }
    });

    $("#vacation-time-hour").change(function() {

        if ($("#vacation-time-hour").val() == 24)
        {
            $("#vacation-time-minute").val(0);
            $("#vacation-time-minute").attr("disabled", true);
        }
        else
        {
            $("#vacation-time-minute").removeAttr('disabled');
        }

        DEFAULT_HR = $('#vacation-time-hour').val();
        $('#main-vacation-time-header').html(DEFAULT_HR+" hr "+DEFAULT_MIN+" min");

        // reset the employee list times
        $('.employee-list-hr').val(DEFAULT_HR);
    });

    $('#vacation-time-minute').change(function() {
        DEFAULT_MIN = $('#vacation-time-minute').val();
        $('#main-vacation-time-header').html(DEFAULT_HR+" hr "+DEFAULT_MIN+" min");

        // reset the employee list times
        if (DEFAULT_MIN == 0) {$('.employee-list-min').val('');}
        else {$('.employee-list-min').val(DEFAULT_MIN);}
    });

    $("#vacation-time-user").change(function() {

        // IF All Employees is selected
        if ($("#vacation-time-user").val() == 0)
        {
            $('#vacation-time-employee-list').show(500);
        }
        else
        {
            $('#vacation-time-employee-list').hide(500);
        }
    });

    $('#vacation-date-modal-button').click(function() {
        if ($('#vacation-date-caret').hasClass('caret-reversed'))
        {
            $('#vacation-date-caret').removeClass('caret-reversed');
        }
        else {$('#vacation-date-caret').addClass('caret-reversed');}
    });

    $('#vacation-time-modal-button').click(function() {
        if ($('#vacation-time-caret').hasClass('caret-reversed'))
        {
            $('#vacation-time-caret').removeClass('caret-reversed');
        }
        else {$('#vacation-time-caret').addClass('caret-reversed');}
    });

    // set the start date to today for everything with the startDate class
    $('.startDate').val(today);

</script>