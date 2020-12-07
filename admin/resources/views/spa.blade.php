<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>Chibog - Home</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Vuetify Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <!-- CSS -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body>
        <div id="app"></div>

        <script type="text/javascript">
            window.latitude = null;
            window.longitude = null;

            function showPosition(position) {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;

                window.latitude = latitude;
                window.longitude = longitude;
            }

            function errorHandler(err) {
                if (err.code == 1) {
                   // alert("Location Error: Access is denied!");
                } else if (err.code == 2) {
                   alert("Location Error: Position is unavailable!");
                }
            }

            if (navigator.geolocation) {
                // timeout at 60000 milliseconds (60 seconds)
                var options = { timeout: 60000 };
                navigator.geolocation.getCurrentPosition(showPosition, errorHandler, options);
            } else { 
                console.log("Geolocation is not supported by this browser.");
            }
        </script>
        <script src="{{ asset('js/manifest.js') }}"></script>
        <script src="{{ asset('js/vendor.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>