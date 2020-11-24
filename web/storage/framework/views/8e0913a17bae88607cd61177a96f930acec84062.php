<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <title>Chibog - Home</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Vuetify Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />

        <!-- CSS -->
        <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    </head>
    <body>
        <div id="app"></div>

        <script type="text/javascript">
            
        </script>
        <script src="<?php echo e(asset('js/manifest.js')); ?>"></script>
        <script src="<?php echo e(asset('js/vendor.js')); ?>"></script>
        <script src="<?php echo e(asset('js/app.js')); ?>"></script>
    </body>
</html><?php /**PATH D:\Web Development\chibog\web\resources\views/spa.blade.php ENDPATH**/ ?>