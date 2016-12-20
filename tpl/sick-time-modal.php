<?php


require_once("config.php");


// setup variables
$EMPLOYEE_ID = array();
$EMPLOYEE_NAME = array();
$DEFAULT_HR = 8;
$DEFAULT_MIN = 0;

?>
<div class="modal fade" id="sick-time-modal" tabindex="-1" role="dialog" aria-labelledby="sick-time-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="sick-time-modal-title"><strong>Add Sick Time</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="sick-date-modal-button" class="btn btn-default sick-time-dropdown" onclick="toggle('#main-sick-date',500)">
                                <strong>Choose a Date:</strong> <span id="main-sick-date-header" class="main-sick-header"><?php print date("F j, Y"); ?></span> <span id="sick-date-caret" class="sick-time-caret caret"></span>
                            </button>
                            <div id="main-sick-date">
                                <div id="sick-time-datepicker"></div>
                                <input class="startDate" type="hidden" id="startSickDate" name="startSickDate" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="sick-time-modal-button" class="btn btn-default sick-time-dropdown" onclick="toggle('#main-sick-time',500)">
                                <strong>Choose a Time:</strong> <span id="main-sick-time-header" class="main-sick-header"><?php print $DEFAULT_HR." hr ".$DEFAULT_MIN." min"; ?></span> <span id="sick-time-caret" class="caret"></span>
                            </button>
                            <div id="main-sick-time">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <select id="sick-time-hour" name="sick-time-hour" class="form-control input-lg select-time">
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
                                    <div class="col-xs-2 sick-time-time-label"><span>hr</span></div>
                                    <div class="col-xs-4">
                                        <select id="sick-time-minute" name="sick-time-minute" class="form-control input-lg select-time">
                                            <?php
                                            for ($i=0; $i<=59; $i++)
                                            {
                                                print '<option value="'.$i.'">'.$i.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-2 sick-time-time-label"><span>min</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="sick-time-row" class="row">
                        <div class="col-xs-5">
                            <h4><strong>Choose a User:</strong></h4>
                        </div>
                        <div class="col-xs-7">
                            <select id="sick-time-user" name="sick-time-user" class="form-control input-lg select-time">
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
                                        print '<option id="sick-time-user-'.$user->id.'" value="'.$user->id.'">'.$user->name.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div id="sick-time-type-row" class="row">
                        <div class="col-xs-5">
                            <h4><strong>Absence Type:</strong></h4>
                        </div>
                        <div class="col-xs-7">
                            <select id="sick-time-absence" name="sick-time-absence" class="form-control input-lg">
                                <option value="0">Excused</option>
                                <option value="1">Unexcused</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="addSickTime(0)" id="sick-time-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-bed"></span> Add Sick Time</button>
            </div>
        </div>
    </div>
</div>
<!-- Internal Sick Time Javascript -->
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

    $("#sick-time-datepicker").datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            $('.startDate').val($.datepicker.formatDate("yy-mm-dd", date));
            $('#main-sick-date-header').html($.datepicker.formatDate("MM d, yy", date));
            $('#sick-date-modal-button').trigger("click");
            $('.btn-done').removeAttr('disabled');
        }
    });

    $("#sick-time-hour").change(function() {

        if ($("#sick-time-hour").val() == 24)
        {
            $("#sick-time-minute").val(0);
            $("#sick-time-minute").attr("disabled", true);
        }
        else
        {
            $("#sick-time-minute").removeAttr('disabled');
        }

        DEFAULT_HR = $('#sick-time-hour').val();
        $('#main-sick-time-header').html(DEFAULT_HR+" hr "+DEFAULT_MIN+" min");

        // reset the employee list times
        $('.employee-list-hr').val(DEFAULT_HR);
    });

    $('#sick-time-minute').change(function() {
        DEFAULT_MIN = $('#sick-time-minute').val();
        $('#main-sick-time-header').html(DEFAULT_HR+" hr "+DEFAULT_MIN+" min");

        // reset the employee list times
        if (DEFAULT_MIN == 0) {$('.employee-list-min').val('');}
        else {$('.employee-list-min').val(DEFAULT_MIN);}
    });

    $("#sick-time-user").change(function() {

        // IF All Employees is selected
        if ($("#sick-time-user").val() == 0)
        {
            $('#sick-time-employee-list').show(500);
        }
        else
        {
            $('#sick-time-employee-list').hide(500);
        }
    });

    $('#sick-date-modal-button').click(function() {
        if ($('#sick-date-caret').hasClass('caret-reversed'))
        {
            $('#sick-date-caret').removeClass('caret-reversed');
        }
        else {$('#sick-date-caret').addClass('caret-reversed');}
    });

    $('#sick-time-modal-button').click(function() {
        if ($('#sick-time-caret').hasClass('caret-reversed'))
        {
            $('#sick-time-caret').removeClass('caret-reversed');
        }
        else {$('#sick-time-caret').addClass('caret-reversed');}
    });

    // set the start date to today for everything with the startDate class
    $('.startDate').val(today);

</script>