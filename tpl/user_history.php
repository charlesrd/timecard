<?php

 

?>
<nav id="top-nav" class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <h1 id="header-title"><a href="http://americasmiles.com/timecard" class="navbar-brand"><span class="glyphicon glyphicon-time"></span> AMS Timecard</a></h1>
        </div>
    </div><!-- /.container -->
</nav>


<footer>
    <div class="navbar navbar-inverse navbar-fixed-bottom">
        <div class="container">
            <div class="navbar-header">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <a href="#" class="thumbnail text-center" onclick="clockIn()" title="Clock in to start recording your time">
                                <span class="icon-clock-o"></span>
                                <h3 class="text-center"><strong>Clock in</strong></h3>
                            </a>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <a href="#" class="thumbnail text-center" onclick="viewHistory()" title="View your history of your weekly timecards">
                                <span class="icon-bar-chart-o"></span>
                                <h3 class="text-center"><strong>View History</strong></h3>
                            </a>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <a href="#" class="thumbnail text-center" onclick="logout()" title="Logout of your timecard">
                                <span class="icon-sign-out"></span>
                                <h3 class="text-center"><strong>Logout</strong></h3>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>