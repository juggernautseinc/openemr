<?php
require('header.php');


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
    <?php Header::setupHeader('datetime-picker'); ?>
</head>
<body>
<div class="container">
    <div class="row" style="margin-top:100px;">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-light">
                    <h4>To request a Provider/Patient Association please complete the following information.</h4>
                </div>
                <div class="card-body">
                    <div id="success-label" class="text-center">
                        <label class="badge text-success"></label>
                    </div>
                    <form id="request-form">
                        <div class="form-group row">
                            <label class="col-2" for="fname">Patient Firstname</label>
                            <div class="col-6">
                                <input class="form-control" name="fname" type="text">
                                <input type="hidden" name="form" value="other-request-form">
                                <label class="badge text-danger mt-1 error-label" id="fname"></label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2" for="provider_name">Patient Lastname</label>
                            <div class="col-6">
                                <input class="form-control" name="lname" type="text">
                                <label class="badge text-danger mt-1 error-label" id="lname"></label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2" for="dob">Patient Date of birth</label>
                            <div class="col-4">
                                <input type='text' class='form-control datepicker' name='dob'/>
                                <label class="badge text-danger mt-1 error-label" id="dob"></label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2" for="dob">Sex</label>
                            <div class="col-3">
                                <select name="sex" class="custom-select">
                                    <option value="male">Male</option>
                                    <option value="female">Fe-Male</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="button" id="request-btn" class="btn btn-primary">Submit</button>
                            <!--                            <a href="submit2.php?username=--><?php //echo $_SESSION['username'] ?><!--" class="btn btn-primary">Submit</a>-->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('.datepicker').datetimepicker({
            <?php $datetimepicker_timepicker = false; ?>
            <?php $datetimepicker_showseconds = false; ?>
            <?php $datetimepicker_formatInput = true; ?>
            <?php require($GLOBALS['srcdir'] . '/js/xl/jquery-datetimepicker-2-5-4.js.php'); ?>
            <?php // can add any additional javascript settings to datetimepicker here; need to prepend first setting with a comma ?>
        });

        $("#request-btn").click(function(){
            var btn = $(this);
            var error = false;
            $(".error-label").text("");
            $("#request-form input").each(function(i){
                var id = $(this).attr('name');

                if($(this).val() == ""){
                    error = true;
                    $("#"+id).fadeIn().text("This field can not be empty");

                    setTimeout(function(){
                        $("#"+id).fadeOut();
                    },2000)
                }
            })

            if(!error){
                btn.text("Loading..");
                var url = "../midocsController.php";
                var data = $("#request-form").serialize();
                ajPost(url,data,function(r){
                    if(r.error == 1){
                        location.href = "submit2.php?username=<?php echo $_SESSION['username'] ?>";
                    }else{
                        //    send message to users.
                        $("#success-label label").fadeIn().text("Your message has been sent. Thank you for using MiDocs.");

                        setTimeout(function(){
                            $("#success-label label").fadeOut();

                            window.close();
                        },5000)
                    }
                    btn.text("Submit");
                })
            }
        })
    })

    function ajPost(url,data,callback){
        $.ajax({
            url,
            type: "post",
            data,
            dataType: "json",
            success : function(r){
                callback(r);
            }
        })
    }
</script>
</body>
</html>
