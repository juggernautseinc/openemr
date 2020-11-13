<?php
include('header.php');

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
</head>
<body>
<div class="container">
    <div class="row" style="margin-top:100px;">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-light">
                    <h5>To send a note to <?php echo "<span class='text-capitalize'>".$_SESSION['patient_fname']." ".$_SESSION['patient_lname']."</span>"; ?>  to upload their data into MiDocs, Please complete the following and click Send.</h5>
                </div>
                <div class="card-body">
                    <div id="success-label" class="text-center">
                        <label class="badge text-success"></label>
                    </div>
                    <form id="provider-contact-form">
                        <div class="form-group row">
                            <label class="col-2" for="email">Patient Email</label>
                            <div class="col-6">
                                <input class="form-control" name="email" type="text">
                                <input type="hidden" name="form" value="patientRequester-contact-form">
                                <label class="badge text-danger mt-1 error-label" id="email"></label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2" for="con_email">Patient confirm email</label>
                            <div class="col-6">
                                <input class="form-control" name="con_email" type="text">
                                <label class="badge text-danger mt-1 error-label" id="con_email"></label>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="button" id="provider-contact-btn" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $("#provider-contact-form input[name='con_email']").keyup(function(){
            var con_email = $(this).val();
            var email = $("#provider-contact-form input[name='email']").val();

            $("#provider-contact-btn").attr("disabled",false);
            $("#con_email").text("");

            if(con_email !== email){
                $("#con_email").fadeIn().text("the email doesn't match");
                $("#provider-contact-btn").attr("disabled",true);
            }
        })
        $("#provider-contact-btn").click(function(){
            var error = false;
            var btn = $(this);
            $("#provider-contact-form input").each(function(i){
                var id = $(this).attr('name');
                if($(this).val() == ""){
                    error = true;
                    $("#"+id).fadeIn().text("This field can not be empty");

                    setTimeout(function () {
                        $("#"+id).fadeOut();
                    },2000)
                }
            })

            if(!error){
                btn.text("Sending..");
                var url = "../midocsController.php";
                var data = $("#provider-contact-form").serialize();

                $.ajax({
                    url,
                    type : "post",
                    data,
                    dataType : "json",
                    success: function (r) {
                        if(r.send == 1){
                            $("#success-label label").fadeIn().text("Your message has been sent. Thank you for using MiDocs.");

                            setTimeout(function(){
                                $("#success-label label").fadeOut();

                                window.close();
                            },5000)
                        }

                        btn.text("Send");
                    }
                })
            }
        })
    })
</script>
</body>
</html>
