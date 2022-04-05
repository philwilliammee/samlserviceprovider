<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Saml Login</title>
</head>
<body>
    <x-samlserviceprovider-login :sessionId="$session_id" :relayState="$relay_state"/>
</body>
</html>
