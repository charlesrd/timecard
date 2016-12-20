<?php


require_once("config.php");


// setup variables
$IP_ADDRESS = $_SERVER['REMOTE_ADDR'];

// get the company message for this company
$query = mysql_query("SELECT M.title, M.message 
                      FROM Company_IP_Address C, Company_Message M
                      WHERE INET_NTOA(C.ip_address) = '$IP_ADDRESS' 
                      AND C.cid = M.cid
                      AND C.cid IN (
                                      SELECT A.cid 
                                      FROM Company_Administrator A
                                      WHERE A.aid = $adminID 
                                   )");
$company = mysql_fetch_object($query);
$companyMessageTitle = $company->title;
$companyMessage = $company->message;

?>
<div class="modal fade" id="message-board-modal" tabindex="-1" role="dialog" aria-labelledby="message-board-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <a type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</a>
                <h2 class="modal-title" id="message-board-modal-title"><strong>Message Board</strong></h2>
            </div>
            <div class="modal-body">
                <div id="message-board-row" class="container-fluid">
                    <div class="row">
                        <div class="col-xs-3">
                            <label id="message-board-text-title-label" for="message-board-text-title">Title:</label>
                        </div>
                        <div class="col-xs-9">
                            <input type="text" class="form-control input-lg message-board-input" id="message-board-text-title" name="message-board-text-title" placeholder="Message Board Title..." value="<?php print $companyMessageTitle; ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-left">
                            <label id="message-board-textarea-label" for="message-board-textarea">Message:</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <textarea class="form-control message-board-input" id="message-board-textarea" name="message-board-textarea" placeholder="Enter Message Board text here..."><?php print $companyMessage; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</a>
                <a type="button" onclick="saveMessageBoard()" id="message-board-done" class="btn btn-lg btn-primary btn-done"><span class="fa fa-save"></span> Save</a>
            </div>
        </div>
    </div>
</div>
<!-- Internal Message Board Javascript -->
<script type="text/javascript">

    $('.message-board-input').keydown(function(){
        $('.btn-done').removeAttr('disabled');
    });

</script>