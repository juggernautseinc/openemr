<?php
session_start();

use OpenEMR\Core\Header;
use OpenEMR\Services\FacilityService;
use OpenEMR\Common\Csrf\CsrfUtils;

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;
require_once("../globals.php");

// mdsupport - Add 'App' functionality for user interfaces without standard menu and frames
// If this script is called with app parameter, validate it without showing other apps.
//
// Build a list of valid entries

$emr_app = array();


// This code allows configurable positioning in the login page
$loginrow = "row login-row align-items-center m-5";

if ($GLOBALS['login_page_layout'] == 'left') {
    $logoarea = "col-md-6 login-bg-left py-3 px-5 py-md-login order-1 order-md-2";
    $formarea = "col-md-6 p-5 login-area-left order-2 order-md-1";
} else if ($GLOBALS['login_page_layout'] == 'right') {
    $logoarea = "col-md-6 login-bg-right py-3 px-5 py-md-login order-1 order-md-1";
    $formarea = "col-md-6 p-5 login-area-right order-2 order-md-2";
} else {
    $logoarea = "col-12 login-bg-center py-3 px-5 order-1";
    $formarea = "col-12 p-5 login-area-center order-2";
    $loginrow = "row login-row";
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$url = $protocol . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = substr($url, 0, strpos($url, "interface"));
?>

<html>
<head>
    <?php Header::setupHeader(); ?>

    <?php CsrfUtils::setupCsrfKey(); ?>

    <title><?php echo text($openemr_name) . " " . xlt('Mi Docs'); ?></title>
</head>
<body class="login">
<form id="create-other-form" >
    <div class="<?php echo $loginrow; ?>">
        <div class="<?php echo $formarea; ?>">
            <div class="mb-4" style="font-size:15px;margin-left: -14px;">
                <label class="badge text-danger error-label" style="line-height:16px;width:0px;"></label>
            </div>
            <div class="form-group row">
                <label for="username" class="col-sm-2">Username</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="username">
                    <label class="badge text-danger mt-1 error-field" id="username"></label>
                    <input type="hidden" name="form" value="create-other-form">
                </div>
            </div>
            <div class="form-group row">
                <label for="password" class="col-sm-2">Password</label>
                <div class="col-sm-4">
                    <input type="password" class="form-control" name="password">
                    <label class="badge text-danger mt-1 error-field" id="password"></label>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-outline-primary btn-sm"
                            onclick="hideShowPassword('show','password')"><i class="fa fa-eye"></i></button>
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="hideShowPassword('hide','password')"><i class="fa fa-eye-slash"></i></button>
                </div>
            </div>
            <div class="form-group row">
                <label for="con_password" class="col-sm-2">Confirm Password</label>
                <div class="col-sm-4">
                    <input type="password" class="form-control" name="con_password">
                    <input type="hidden" name="csrf_token" value="<?php echo CsrfUtils::collectCsrfToken(); ?>">
                    <label class="badge text-danger mt-1 error-field" id="con_password"></label>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-outline-primary btn-sm"
                            onclick="hideShowPassword('show','con_password')"><i class="fa fa-eye"></i></button>
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="hideShowPassword('hide','con_password')"><i class="fa fa-eye-slash"></i></button>
                </div>
            </div>
            <div class="form-group mt-2">
                <div class="row">
                    <div class="col-6">
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" id="next" class="btn btn-primary"><i
                                class="fa fa-arrow-circle-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="<?php echo $logoarea; ?>">
            <div class="text-center login-title-label">
                <?php if ($GLOBALS['show_label_login']) { ?>
                    <?php echo text($openemr_name); ?>
                <?php } ?>
            </div>
            <h3 class="text-center text-light"><?php echo xlt('Making Healthcare Work!'); ?></h3>
            <br>
            <h5 class="text-center text-light"><?php echo xlt('You must create an account to access the database.'); ?></h5>
        </div>
    </div>
</form>
<form id="other-sumbit-form" class="d-none">
    <div class="<?php echo $loginrow; ?>">
        <div class="<?php echo $formarea; ?>">
            <div class="mb-4" style="font-size:15px;margin-left: -14px;">
                <label id="success-label" class="badge text-success error-label" style="line-height:16px;width:0px;"></label>
            </div>
            <div class="form-group row">
                <label for="fname" class="col-sm-2">Your First Name</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="fname">
                    <label class="badge text-danger mt-1 error-field" id="fname"></label>
                </div>
            </div>
            <div class="form-group row">
                <label for="lname" class="col-sm-2">Your Last Name</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="lname">
                    <label class="badge text-danger mt-1 error-field" id="lname"></label>
                </div>
            </div>
            <div class="form-group row">
                <label for="organization" class="col-sm-2">Organization (Optional)</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="organization">
                    <label class="badge text-danger mt-1 error-field" id="organization"></label>
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-2">E-mail</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="email">
                    <label class="badge text-danger mt-1 error-field" id="email"></label>
                </div>
            </div>
            <div class="form-group row">
                <label for="con_email" class="col-sm-2">Confirm e-mail</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="con_email">
                    <label class="badge text-danger mt-1 error-field" id="con_email"></label>
                </div>
            </div>
            <p>For two factor authentication please add your contact number.</p>
            <div class="form-group row">
                <label for="contact" class="col-sm-2">Number</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="contact">
                    <label class="badge text-danger mt-1 error-field" id="contact"></label>
                </div>
            </div>
            <div class="form-group row">
                <label for="dob" class="col-sm-4">Preferred method of contact</label>
                <div class="col-sm-4">
                    <div class="row">
                        <label class="col-5">
                            Phone :
                        </label>
                        <div class="col">
                            <input type="radio" class="custom-checkbox" name="two_factor" value="phone">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-5">
                            Email :
                        </label>
                        <div class="col">
                            <input type="radio" class="custom-checkbox" name="two_factor" value="email">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group mt-2">
                <div class="row">
                    <div class="col-6">
                        <button type="button" id="prev-btn" class="btn btn-success"><i class="fa fa-arrow-circle-left"></i></button>
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" id="submit-btn" class="btn btn-login"><?php echo xlt('Submit'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="<?php echo $logoarea; ?>">
            <div class="text-center login-title-label">
                <?php if ($GLOBALS['show_label_login']) { ?>
                    <?php echo text($openemr_name); ?>
                <?php } ?>
            </div>
            <h5 class="text-center text-light"><?php echo xlt('Please complete the following to create a MiDocs account'); ?></h5>
            </p>
        </div>
    </div>
</form>
<script>
    function hideShowPassword(evt, name) {
        if (evt == "hide") {
            //    hide password
            $("input[name=" + name + "]").attr('type', 'password');
        } else {
            //    show password
            $("input[name=" + name + "]").attr('type', 'text');
        }
    }

    // match password
    $("#create-other-form input[name='con_password']").keyup(function () {
        const con_pass = $(this).val();
        const pass = $("#create-other-form input[name='password']").val();
        const btn = $("#next");
        const errorMsg = $("#con_password");

        btn.attr('disabled', false);
        errorMsg.text('');

        if (con_pass !== pass) {
            btn.attr('disabled', true);
            errorMsg.fadeIn().text("password doesn't match");
        }
    })

    // match email
    $("#other-sumbit-form input[name='con_email']").keyup(function () {
        const con_email = $(this).val();
        const email = $("#other-sumbit-form input[name='email']").val();
        const btn = $("#submit_provider_btn");
        const errorMsg = $("#con_email");

        btn.attr('disabled', false);
        errorMsg.text('');

        if (con_email !== email) {
            btn.attr('disabled', true);
            errorMsg.fadeIn().text("email doesn't match");
        }
    });

    $("#next").click(function(){
        var btn = $(this);
        var error = false;

        $("#create-other-form input").each(function(i){
            var id = $(this).attr('name');
            if($(this).val() == "" && id !== "form"){
                error = true;
                $("#"+id).fadeIn().text("This field can not be empty");

                setTimeout(function(){
                    $("#"+id).fadeOut();
                },3000)
            }
        })


        if(!error){
            btn.text("Loading..");
            var url = "midocsController.php";
            var data = $("#create-other-form").serialize();

            ajPost(url,data,function(r){
                if(r.error == 0){
                    //    show other form
                    $("#create-other-form").addClass("d-none").removeClass("d-block");
                    $("#other-sumbit-form").removeClass("d-none");
                }else{
                    $(".error-label").fadeIn().html("This username already exists.  If you would like to create a new MiDocs account, please choose a different username. <br> If you already have a MiDocs account, but forgot your username or password, please click Forgot Username/Password.</br>  If you already have an account, and know your username and password, please click here to Login");
                    setTimeout(function () {
                        $(".error-label").fadeOut();
                    }, 10000)
                }

                btn.html("<i class='fa fa-arrow-circle-right'></i>");
            })
        }
    })

    //prev form
    $("#prev-btn").click(function(){
        $("#create-other-form").removeClass("d-none");
        $("#other-sumbit-form").addClass("d-none");
    });

    //submit form
    $("#submit-btn").click(function(){
        var error = false;
        var btn = $("#submit-btn");

        $("#other-sumbit-form input").each(function(){
            var id = $(this).attr("name");

            if($(this).val() == ""){
                error = true;
                $("#"+id).fadeIn().text("This field can not be empty");

                setTimeout(function(){
                    $("#"+id).fadeOut();
                },3000)
            }
        });

        if(!error){
            btn.text("Loading..");
            var url = "midocsController.php";
            var data = {
                "form" : "other-submit",
                "username" : $("#create-other-form input[name='username']").val(),
                "password" : $("#create-other-form input[name='password']").val(),
                "fname" : $("#other-sumbit-form input[name='fname']").val(),
                "lname" : $("#other-sumbit-form input[name='lname']").val(),
                "organization" : $("#other-sumbit-form input[name='organization']").val(),
                "email" : $("#other-sumbit-form input[name='email']").val(),
                "contact" : $("#other-sumbit-form input[name='contact']").val(),
                "preferred_contact": $("#other-sumbit-form input[name='two_factor']:checked").val(),
                "csrf_token" : $("#create-other-form input[name='csrf_token']").val()
            };

            ajPost(url,data,function(r){
                if(r.error == 0){
                    $("#success-label").fadeIn().text("Successfully created, Thank you for using Midocs");
                    setTimeout(function(){
                        location.href = "<?php echo $url?>";
                    },3000)
                }
            })
        }
    })

    function ajPost(url, data, callBack) {
        $.ajax({
            url: url,
            type: 'post',
            data: data,
            dataType: 'json',
            success: function (r) {
                callBack(r);
            }
        })
    }
</script>
</body>
</html>
