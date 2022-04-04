<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <noscript
        <div class="alert alert-danger" role="alert">
        <strong>Warning!</strong>
        <p>You need to enable JavaScript to use this application.</p>
        </div>
    </noscript>
    <div class="row" hidden>
        <x-samlserviceprovider-login :sessionId="$session_id" :relayState="$relay_state"/>
    </div>
</body>
</html>
