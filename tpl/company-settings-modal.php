<?php

require_once("config.php");

?>
<div class="modal fade" id="company-settings-modal" tabindex="-1" role="dialog" aria-labelledby="company-settings-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="company-settings-modal-title"><strong>Company Settings</strong></h2>
            </div>
            <div class="modal-body">
                <div id="company-settings-container" class="container-fluid">
                    <div class="row">
                        <div id="info-company-settings-row" style="min-height: 100px;" class="col-xs-12">
                            <p class="alert alert-info">
                                Your current local ip address is <strong><?php print $IP_ADDRESS; ?></strong><br>
                                Breaks/day: -1 = unlimited<br>
                                A max of 2 breaks can be displayed at once. More breaks will still influence total times but will be hidden.
                            </p>
                        </div>
                    </div>
                    <div id="company-settings-row" class="row">

                        <div class="col-xs-12 col-sm-12">

                            <table class="table table-hovered table-striped table-bordered">
                                <head>
                                    <tr>
                                        <th>Company</th>
                                        <th>Breaks/day</th>
                                        <th id="ip-address-col">IP Address</th>
                                    </tr>
                                </head>
                                <tbody>
                                    <?php

                                    // get the company info
                                    $sql = "SELECT *
                                            FROM Company C
                                            WHERE C.id IN (
                                                SELECT CA.cid
                                                FROM Company_Administrator CA
                                                WHERE CA.aid = $adminID 
                                            )
                                            ORDER BY C.name ASC";

                                    $query = mysql_query($sql);
                                    while($company = mysql_fetch_object($query)):
                                    ?>

                                    <tr>
                                        <td><?php print $company->name; ?></td>
                                        <td style="width:10%; min-width:100px;">
                                            <input type="text" class="form-control integers company-breaks-input" data-id="<?php print $company->id; ?>" id="company-breaks-<?php print $company->id; ?>" name="company-breaks-<?php print $company->id; ?>" placeholder="0" value="<?php print $company->breaks_per_day; ?>" />
                                        </td>
                                        <td>

                                            <div id="ip-address-container-<?php print $company->id; ?>">

                                            <?php

                                            // get the company ip addresses
                                            $sql = "SELECT CIP.id, INET_NTOA(CIP.ip_address) AS ip_address
                                                    FROM Company_IP_Address CIP
                                                    WHERE CIP.cid = $company->id";

                                            $q1 = mysql_query($sql);
                                            while ($c = mysql_fetch_object($q1)):
                                            ?>

                                                <div id="ip-row-<?php print $c->id; ?>" class="row ip-row">
                                                    <div class="col-xs-8 col-sm-3"><?php print $c->ip_address; ?></div>
                                                    <div class="col-xs-4 col-sm-9">
                                                        <button class="btn btn-xs btn-danger" onclick="removeIPAddress(<?php print $c->id; ?>)"><span class="fa fa-remove"></span></button>
                                                    </div>
                                                </div>

                                            <?php endwhile; ?>

                                            </div>

                                            <div class="row">
                                                <div class="col-xs-12 col-sm-8">
                                                    <input type="text" maxlength="15" class="form-control decimal-numbers company-new-ip-input" id="company-new-ip-<?php print $company->id; ?>" name="company-new-ip-<?php print $company->id; ?>" placeholder="Ex: <?php print $IP_ADDRESS; ?>" value="" />
                                                </div>
                                                <div class="col-xs-12 col-sm-4">
                                                    <button class="btn btn-primary" onclick="saveIPAddress(<?php print $company->id; ?>)">Add</button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <?php endwhile; ?>
                                    
                                </tbody>
                            </table>
                            
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Internal IP Address Javascript -->
<script type="text/javascript">

$(document).ready(function() {

    // on breaks/day change
    $('.company-breaks-input').on('input', function() {
        var company_id = $(this).data("id");
        var breaks = $("#company-breaks-"+company_id).val();
        breaks = (breaks == "") ? 0 : breaks;
        updateCompanyBreaks(company_id, breaks);
    });


    // Only allow integers for certain elements
    $(".integers").keydown(function (e) {

        // console.log("key: "+e.keyCode);

        // Allow: backspace, delete, tab, escape, enter, dash
        if ($.inArray(e.keyCode, [8, 9, 27, 13, 110, 189]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
        else // the key was allowed
        {
            // enable the done button
            // $('.btn-done').removeAttr('disabled');
        }
    });


    // Only allow decimal numbers for certain elements
    $(".decimal-numbers").keydown(function (e) {

        // console.log("key: "+e.keyCode);

        // Allow: backspace, delete, tab, escape, enter, and .
        if ($.inArray(e.keyCode, [8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
        else // the key was allowed
        {
            // enable the done button
            // $('.btn-done').removeAttr('disabled');
        }
    });

});

</script>