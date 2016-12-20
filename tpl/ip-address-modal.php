<?php


require_once("config.php");


// get the company ip address
$query = mysql_query("SELECT INET_NTOA(C.ip_address) AS ip_address
                      FROM Company_IP_Address C
                      WHERE C.cid IN (
                                      SELECT A.cid 
                                      FROM Company_Administrator A
                                      WHERE A.aid = $adminID 
                                   )");
$company = mysql_fetch_object($query);
$COMPANY_IP_ADDRESS = $company->ip_address;

?>
<div class="modal fade" id="ip-address-modal" tabindex="-1" role="dialog" aria-labelledby="ip-address-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="ip-address-modal-title"><strong>Company IP Address</strong></h2>
            </div>
            <div class="modal-body">
                <div id="ip-address-row" class="container-fluid">
                    <div class="row">
                        <div id="info-ip-address-row" class="col-xs-12">
                            <p class="alert alert-info">Your current local ip address is <strong><?php print $IP_ADDRESS; ?></strong></p>
                        </div>
                    </div>
                    <div id="company-ip-address-row" class="row">
                        <div class="col-xs-12 col-sm-4">
                            <label id="ip-address-text-title-label" for="ip-address-text-title">Company IP Address:</label>
                        </div>
                        <div class="col-xs-12 col-sm-8">
                            <input type="text" class="form-control input-lg decimal-numbers ip-address-input" id="ip-address" name="ip-address" placeholder="Ex: <?php print $COMPANY_IP_ADDRESS; ?>" value="<?php print $COMPANY_IP_ADDRESS; ?>" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="saveIPAddress()" id="ip-address-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-save"></span> Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Internal IP Address Javascript -->
<script type="text/javascript">

    $('.ip-address-input').keydown(function(){
        $('.btn-done').removeAttr('disabled');
    });

    // Only allow decimal numbers for certain elements
    $(".decimal-numbers").keydown(function (e) {

        console.log("key: "+e.keyCode);

        // Allow: backspace, delete, tab, escape, enter and .
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
            $('.btn-done').removeAttr('disabled');
        }
    });

</script>