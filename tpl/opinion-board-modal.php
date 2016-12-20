<?php

require_once("config.php");

?>
<div class="modal fade" id="opinion-board-modal" tabindex="-1" role="dialog" aria-labelledby="opinion-board-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="opinion-board-modal-title"><strong>AMS Employee Opinion</strong></h2>
            </div>
            <div class="modal-body">
                <div id="opinion-board-row" class="container-fluid">
                    <div class="row">
                        <div class="col-xs-12 text-left">
                            <label id="opinion-board-self-label" for="opinion-board-text-title">What 3 things would you like to see yourself improve on this year?</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea class="form-control opinion-board-textarea" id="opinion-board-self-textarea" name="opinion-board-self-textarea" placeholder="Enter your opinion"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-left">
                            <label id="opinion-board-company-label" for="opinion-board-textarea">What 3 things would you like to see the company improve on this year?</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <textarea class="form-control opinion-board-textarea" id="opinion-board-company-textarea" name="opinion-board-company-textarea" placeholder="Enter your opinion"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="saveOpinionBoard()" id="opinion-board-done" class="btn btn-lg btn-primary btn-done" disabled="true"><span class="fa fa-save"></span> Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Internal Message Board Javascript -->
<script type="text/javascript">

    $('.opinion-board-textarea').keydown(function(){
        $('.btn-done').removeAttr('disabled');
    });


    $('#opinion-board-modal').on('hidden.bs.modal', function (e) {
        $("#opinion-code").val('');
    });

</script>