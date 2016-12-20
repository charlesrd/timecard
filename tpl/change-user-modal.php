<?php


require_once("config.php");


// setup variables
$today = date('Y-m-d');
$employeeType = 1;


?>
<div class="modal fade" id="change-user-modal" tabindex="-1" role="dialog" aria-labelledby="change-user-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h2 class="modal-title" id="change-user-modal-title"><strong>Choose a User:</strong></h2>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="change-user-row" class="row">
                        <?php

                            // get the list of all the Employees from this company, sorted by first name
                            $sql = "SELECT 
                                    E.id,
                                    CONCAT(E.first_name,' ',E.last_name) AS name
                                    FROM Employee E
                                    WHERE E.companyID IN 
                                    (
                                        SELECT CA.cid 
                                        FROM Company_Administrator CA
                                        WHERE CA.aid = $adminID
                                    )
                                    ORDER BY E.first_name ASC";

                            $query = mysql_query($sql);

                            $i = 1;
                            while($user = mysql_fetch_object($query))
                            {
                                print '<div class="col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-2 col-radio">
                                                    <input id="change-user-name-'.$i.'" name="change-user-name" class="input-lg" type="radio" value="'.$user->id.'" />
                                                </div>
                                                <div class="col-xs-10 col-label">
                                                    <label id="change-user-name-'.$i.'-label" class="modal-label" for="change-user-name-'.$i.'"><h4><strong>'.$user->name.'</strong></h4></label>
                                                </div>
                                            </div>
                                        </div>';

                                // update the counter
                                $i++;
                            }

                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-lg btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="changeUser()" id="change-user-done" class="btn btn-lg btn-primary btn-done"><span class="glyphicon glyphicon-user"></span> Change User</button>
            </div>
        </div>
    </div>
</div>
<!-- Change User Modal -->
<script type="text/javascript">

    $('input:radio').change(function(){
        $('.btn-done').removeAttr('disabled');
    });

</script>