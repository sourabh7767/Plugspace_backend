<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password Done</title>
    <link rel="icon" href="{{ env('PUBLIC_PATH') }}images/logo.png" type="image/x-icon" />
    <link href="{{ env('PUBLIC_PATH') }}assets/core/bootstrap.min.css" rel='stylesheet' type='text/css' />
    <link href="{{ env('PUBLIC_PATH') }}assets/core/style.css" rel='stylesheet' type='text/css' />
    <link href="{{ env('PUBLIC_PATH') }}assets/core/font-awesome.css" rel="stylesheet">
    <script src="{{ env('PUBLIC_PATH') }}assets/core/jquery.min.js"></script>
</head>

<body class="sign-in-up">
    <?php $logo = env('PUBLIC_PATH') . "images/logo.png"; ?>
    <section>
        <div id="page-wrapper" class="sign-in-wrapper">
            <div class="graphs">
                <div class="sign-in-form">
                    <center><img border="0" align="absbottom" alt="" src="<?= $logo ?>" class="CToWUd" width="90"
                            height="90">
                        <br> <br />
                        <div class="signin reset_pwd_btn1">
                            <p class="reset_pwd_btn">Password reset successfully.</p>
                        </div>
                    </center>
                </div>
            </div>
        </div>

        <!--footer section end-->
    </section>

    <script src="{{ env('PUBLIC_PATH') }}assets/core/bootstrap.min.js"></script>
</body>

</html>
