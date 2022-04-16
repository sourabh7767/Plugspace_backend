<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset password</title>
    <link rel="icon" href="{{ env('PUBLIC_PATH') }}images/logo.png" type="image/x-icon" />

    <link href="{{ env('PUBLIC_PATH') }}assets/core/bootstrap.min.css" rel='stylesheet' type='text/css' />
    <link href="{{ env('PUBLIC_PATH') }}assets/core/style.css" rel='stylesheet' type='text/css' />
    <link href="{{ env('PUBLIC_PATH') }}assets/core/font-awesome.css" rel="stylesheet">
    <script src="{{ env('PUBLIC_PATH') }}assets/core/jquery.min.js"></script>
</head>
<?php
$email = base64_decode($_REQUEST['email']);
$logo = env('PUBLIC_PATH') . "images/logo.png"; ?>

<body class="sign-in-up" style="text-align: center;">
    <section>
        <div id="page-wrapper" class="sign-in-wrapper">
            <div class="graphs">
                <div class="sign-in-form">
                    <center><img border="0" align="absbottom" alt="" src="<?= $logo ?>" class="CToWUd" width="90" height="90">
                    </center>
                    <br />
                    <div class="reset_pwd_btn">
                        <p><span>Reset Password</span></p>
                    </div>
                    <div class="signin" style="position: static;">
                        <form method="post" id="form1">
                            <div class="log-input pass-relative">
                                <div class="log-input-left" style="width: 100%">
                                    <input type="hidden" value="<?= $email?>" name="email" id="email">
                                    <input type="password" placeholder="New Password *" name="npass" id="npass"
                                        class="lock" required="required" minlength="5" />
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="log-input">
                                <div class="log-input-left" style="width: 100%">
                                    <input type="password" class="lock" name="cpass" id="cpass"
                                        placeholder="Confirm Password *" required="required" />
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <center><input type="button" class="btn btn-default reset_pwd_btn1" name="reset_pass"
                                    value="Change" onclick="submitData()" />
                            </center>
                        </form>
                        <br />
                        <p id="show_msg"></p>
                        <br />
                        <br />
                    </div>
                </div>
            </div>
        </div>

    </section>
</body>

</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script type="text/javascript">

    function submitData() {
        var email = $('#email').val();
        var npass = $('#npass').val();
        var cpass = $('#cpass').val();
        var url = "{{ env('APP_URL') }}/resetPwdMail1";
        if (npass == '' && cpass == '') {
            alert('Please enter require field');
            return false;
        }

        $.ajax({
            url: url,
            type: "get",
            data: {
                npass: npass,
                cpass: cpass,
                email: email
            },
            success: function(data) {
                console.log(data);
                if (data == 1) {
                    window.location.href = "{{ env('APP_URL') }}/resetPwdDone";
                } else if (data == 2) {
                    $('#show_msg').html(
                        '<div style="color:red; text-align:center; font-size:20px"> Password mismatch, please enter same password.</div>'
                    );
                } else if (data == 3) {
                    $('#show_msg').html(
                        '<div style="color:red; text-align:center; font-size:20px"> It seems that you have already reset your password.</div>'
                    );
                } else if (data == 4) {
                    $('#show_msg').html(
                        '<div style="color:red; text-align:center; font-size:20px"> Unable to change password because of Invalid email.</div>'
                    );
                }
            }
        });
    }
</script>
