<?php


require_once("config.php");


$IP_ADDRESS = $_SERVER['REMOTE_ADDR'];

// IF the incoming ip address is the same as a Company_IP_Address, then get the company message for that company
$query = mysql_query("SELECT M.title, M.message 
                      FROM Company_IP_Address C, Company_Message M 
                      WHERE INET_NTOA(C.ip_address) = '$IP_ADDRESS' 
                      AND C.cid = M.cid");
$company = mysql_fetch_object($query);
$companyMessageTitle = $company->title;
$companyMessage = $company->message;

?>
<script type="text/javascript">
$(document).keypress(function(e) {
    if ( (e.which == 13) && (!($("#opinion-board-modal").is(":visible"))) ) 
    {
        login();
    }
});
</script>
<div id="login-container" class="container">

    <div class="row">
        <div class="col-sm-6 col-sm-offset-6"><h1 id="login-header" class="inset-text">AMS Timecard</h1></div>
    </div>

    <div id="login-row" class="row">
        <div id="login" class="col-sm-6 col-sm-offset-6 text-left">
            <p>
                <div class="row">
                    <div class="col-md-6">
                        <input id="username" type="text" class="input-lg" placeholder="username" />
                        <br>
                        <input id="password" type="password" class="input-lg" placeholder="password" />
                        <br>
                        <span id="login-message"></span>
                        <br>
                        <a id="login-button" href="javascript:void(0);" onclick="login()">Login</a>
                    </div>
                    <div class="col-md-6">
                        <p id="sponsors">
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                    <a class="fade" href="http://www.americasmiles.com" target="_blank">
                                        <img class="sponsor" src="http://americasmiles.com/img/americasmiles-md-logo.png" alt="America Smiles" />
                                    </a>
                                </div>
                            </div>
                        </p>
                    </div>
                </div>
            </p>

            <?php if ( ($companyMessageTitle != "") || ($companyMessage != "") ): ?>
                <p id="message-board">
                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                            <div id="message-board-text" class="alert alert-info text-center">
                                <strong id="message-board-text-title"><?php print $companyMessageTitle; ?></strong>
                                <br>
                                <?php print $companyMessage; ?>
                            </div>
                        </div>
                    </div>
                </p>
            <?php endif; ?>



            <p id="opinion-board">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <input id="opinion-code" class="input-lg form-control text-center" placeholder="Enter Your Opinion Code" />
                        <button onclick="submitOpinionCode()" style="margin-top:10px; margin-bottom:10px;" class="btn btn-lg btn-block btn-primary">Submit</button>
                        <span id="opinion-message"></span>
                    </div>
                </div>
            </p>

        </div>
    </div>

</div>

<!-- Opinion Board Modal -->
<div id="opinion-board-modal-container"><?php include("/usr/www/www.amstimecard.com/tpl/opinion-board-modal.php"); ?></div>

