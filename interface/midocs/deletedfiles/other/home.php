<?php
require("header.php");


use OpenEMR\Core\Header;
use OpenEMR\Services\FacilityService;
use OpenEMR\Common\Csrf\CsrfUtils;

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;
require_once("../../globals.php");
?>

<html>
<head>
    <?php Header::setupHeader(); ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.standalone.min.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid">
    <div class="row mt-3">
        <div class="col-md-6 offset-3 text-center text-capitalize">
            <h3><?php echo $_SESSION['organization'] ?></h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <nav class="navbar navbar-expand-lg">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <form class="form-inline my-2 my-lg-0 mr-auto">
                        <input class="form-control mr-sm-2" type="search" placeholder="Patients search" aria-label="Search">
                        <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">Search</button>
                    </form>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-capitalize" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php echo $_SESSION['username'] ?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="../logout.php?mode=other&username=<?php echo $_SESSION['username'] ?>">Logout</a>
<!--                                <a class="dropdown-item" href="#">Another action</a>-->
<!--                                <div class="dropdown-divider"></div>-->
<!--                                <a class="dropdown-item" href="#">Something else here</a>-->
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div id="calendar" style="width:200px"></div>
        </div>
        <div class="col-md-9">
            <table class="table">
                <thead>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Provider / Patient Association</th>
                </thead>
                <tbody>

                </tbody>
            </table>
            <span>Patient not listed? <a href="request.php?username=<?php echo $_SESSION['username'] ?>">Click HERE </a> to create a Provider/Patient Association</span>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.nl.min.js"></script>
<script>
    $(function(){
        $('#calendar').datepicker({
            calendarWeeks: true,
            todayHighlight: true,
        });
    })
</script>
</body>
</html>
