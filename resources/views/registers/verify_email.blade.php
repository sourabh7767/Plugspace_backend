<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ env('PUBLIC_PATH') }}images/logo.png" type="image/x-icon" />

    <title>{{env('APP_NAME')}}</title>
    <style>
        .text-red{color: red; clear: both;text-align: center; font-size: 24px}
        .boder{width: 32%;padding: 1%;border:3px #000 solid}
        .text{color: #6d786d; clear: both;text-align: center; font-size: 24px}
        .img{width:45%; height:30%}
        @media screen and (max-width: 700px) {
            .boder{width: 80%;padding: 1%;border:3px #000 solid}
            .text-red{color: red; clear: both;text-align: center; font-size:80%}
            .text{color: #6d786d; clear: both;text-align: center; font-size: 80%}
            .img{width:70%; height:30%}
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <br /><br />
            <center>
                <div class='boder'><img class="logosize img" src="{{ env('PUBLIC_PATH') }}images/logo.png" />
                <br/>

                {{--<h2>Hello, <?= $data1['name']?></h2>--}}
                <div style="width: 71%;">
            <?php
            if($data1['code'] == 0){
            ?>
                <p class='text-red'>
                    <?php  echo $request->session()->get('error'); ?></p>
                <?php }else{?>
                <p class='text' >
                    <?php echo $request->session()->get('success');?></p>
            <?php } ?>
                </div>
                </div>
            </center>
        </div>
    </div>
</body>

</html>
