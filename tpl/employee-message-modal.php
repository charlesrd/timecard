<?php

require_once("config.php");

?>
<div class="modal fade" id="employee-message-modal" tabindex="-1" role="dialog" aria-labelledby="employee-message-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="employee-message-modal-title"><strong>AMS Employee Message</strong></h2>
            </div>
            <div class="modal-body">
                <div id="employee-message-row" class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12 text-left">
                            <label id="employee-message-label" for="employee-message-text-title">Enter your Comments / Concerns / Message below:</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea class="form-control employee-message-textarea" id="employee-message-textarea" name="employee-message-textarea" placeholder="Type message here..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="saveEmployeeMessage()" id="employee-message-done" class="btn btn-lg btn-primary btn-done" disabled="true"><span class="fa fa-save"></span> Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Internal Message Board Javascript -->
<script type="text/javascript">

    $('.employee-message-textarea').keydown(function(){
        $('.btn-done').removeAttr('disabled');
    });

</script>