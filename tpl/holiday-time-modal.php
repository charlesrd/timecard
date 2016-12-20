<?php


require_once("config.php");


// setup variables
$EMPLOYEE_ID = array();
$EMPLOYEE_NAME = array();
$DEFAULT_HR = 8;
$DEFAULT_MIN = 0;

?>
<div class="modal fade" id="holiday-time-modal" tabindex="-1" role="dialog" aria-labelledby="holiday-time-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="holiday-time-modal-title"><strong>Add Holiday Time</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="holiday-date-modal-button" class="btn btn-default holiday-time-dropdown" onclick="toggle('#main-holiday-date',500)">
                                <strong>Choose a Date:</strong> <span id="main-holiday-date-header" class="main-holiday-header"><?php print date("F j, Y"); ?></span> <span id="holiday-date-caret" class="holiday-time-caret caret"></span>
                            </button>
                            <div id="main-holiday-date">
                                <div id="holiday-time-datepicker"></div>
                                <input class="startDate" type="hidden" id="startHolidayDate" name="startHolidayDate" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <button id="holidayholiday-time-modal-button" class="btn btn-default holiday-time-dropdown" onclick="toggle('#main-holiday-time',500)">
                                <strong>Choose a Time:</strong> <span id="main-holiday-time-header" class="main-holiday-header"><?php print $DEFAULT_HR." hr ".$DEFAULT_MIN." min"; ?></span> <span id="holiday-time-caret" class="caret"></span>
                            </button>
                            <div id="main-holiday-time">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <select id="holiday-time-hour" name="holiday-time-hour" class="form-control input-lg select-time">
                                            <?php
                                            $default = $DEFAULT_HR;
                                            for ($i=1; $i<=24; $i++)
                                            {
                                                if ($i == $default) {print '<option value="'.$i.'" selected="selected">'.$i.'</option>';}
                                                else {print '<option value="'.$i.'">'.$i.'</option>';}
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-2 holiday-time-time-label"><span>hr</span></div>
                                    <div class="col-xs-4">
                                        <select id="holiday-time-minute" name="holiday-time-minute" class="form-control input-lg select-time">
                                            <?php
                                            for ($i=0; $i<=59; $i++)
                                            {
                                                print '<option value="'.$i.'">'.$i.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-xs-2 holiday-time-time-label"><span>min</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="holiday-time-row" class="row">
                        <div class="col-xs-5">
                            <h4><strong>Choose a User:</strong></h4>
                        </div>
                        <div class="col-xs-7">
                            <select id="holiday-time-user" name="holiday-time-user" class="form-control input-lg select-time">
                                <option id="holiday-time-user-0" value="0" selected="selected">All Employees</option>
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
                                        print '<option id="holiday-time-user-'.$user->id.'" value="'.$user->id.'">'.$user->name.'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div id="holiday-time-employee-list" class="row">
                        <div id="employee-list-table-div" class="col-xs-12">
                            <table id="employee-list-table" class="table table-hover table-striped text-center">
                                <thead>
                                    <tr><td class="table-col-sm">hr</td><td class="table-col-sm">min</td><td>Name</td></tr>
                                </thead>
                                <tbody>
                                    <?php 

                                        // prepare the defaults for printing
                                        if ($DEFAULT_MIN == 0) {$DEFAULT_MIN = "";}

                                        $count = 0;
                                        foreach ($EMPLOYEE_NAME as $name)
                                        {
                                            print '<tr>
                                                    <td class="table-col-sm"><input id="employee-list-hr-'.$count.'" name="employee-list-hr-'.$count.'" type="text" placeholder="0" class="employee-list-hr numbers-only" value="'.$DEFAULT_HR.'" /></td>
                                                    <td class="table-col-sm"><input id="employee-list-min-'.$count.'" name="employee-list-min-'.$count.'" type="text" placeholder="0" class="employee-list-min numbers-only" value="'.$DEFAULT_MIN.'" /></td>
                                                    <td class="employee-list-name">
                                                     <span id="employee-list-name-'.$count.'">'.$name.'</span>
                                                     <input id="employee-list-id-'.$count.'" name="employee-list-id-'.$count.'" type="hidden" class="employee-list-id" value="'.$EMPLOYEE_ID[$count].'" />
                                                    </td>
                                                   </tr>';
                                            $count++;
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="addHolidayTime(0)" id="holiday-time-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-gift"></span> Add Holiday Time</button>
            </div>
        </div>
    </div>
</div>
<!-- Internal Holiday Time Javascript -->
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

    $("#holiday-time-datepicker").datepicker( {
        showOtherMonths: true,
        selectOtherMonths: true,
        onSelect: function(dateText, inst) { 
            var date = $(this).datepicker('getDate');
            $('.startDate').val($.datepicker.formatDate("yy-mm-dd", date));
            $('#main-holiday-date-header').html($.datepicker.formatDate("MM d, yy", date));
            $('#holiday-date-modal-button').trigger("click");
            $('.btn-done').removeAttr('disabled');
        }
    });

    $("#holiday-time-hour").change(function() {

        if ($("#holiday-time-hour").val() == 24)
        {
            $("#holiday-time-minute").val(0);
            $("#holiday-time-minute").attr("disabled", true);
        }
        else
        {
            $("#holiday-time-minute").removeAttr('disabled');
        }

        DEFAULT_HR = $('#holiday-time-hour').val();
        $('#main-holiday-time-header').html(DEFAULT_HR+" hr "+DEFAULT_MIN+" min");

        // reset the employee list times
        $('.employee-list-hr').val(DEFAULT_HR);
    });

    $('#holiday-time-minute').change(function() {
        DEFAULT_MIN = $('#holiday-time-minute').val();
        $('#main-holiday-time-header').html(DEFAULT_HR+" hr "+DEFAULT_MIN+" min");

        // reset the employee list times
        if (DEFAULT_MIN == 0) {$('.employee-list-min').val('');}
        else {$('.employee-list-min').val(DEFAULT_MIN);}
    });

    $("#holiday-time-user").change(function() {

        // IF All Employees is selected
        if ($("#holiday-time-user").val() == 0)
        {
            $('#holiday-time-employee-list').show(500);
        }
        else
        {
            $('#holiday-time-employee-list').hide(500);
        }
    });

    $('#holiday-date-modal-button').click(function() {
        if ($('#holiday-date-caret').hasClass('caret-reversed'))
        {
            $('#holiday-date-caret').removeClass('caret-reversed');
        }
        else {$('#holiday-date-caret').addClass('caret-reversed');}
    });

    $('#holiday-time-modal-button').click(function() {
        if ($('#holiday-time-caret').hasClass('caret-reversed'))
        {
            $('#holiday-time-caret').removeClass('caret-reversed');
        }
        else {$('#holiday-time-caret').addClass('caret-reversed');}
    });

    // set the start date to today for everything with the startDate class
    $('.startDate').val(today);

</script>