<html>
<head>
  <meta charset="utf-8">
  <title>Callback</title>
  <script>
    window.opener.postMessage({
    	api_token: "<?php echo e($api_token); ?>",
    	current_user: "<?php echo e(json_encode($current_user)); ?>",
    	error: "<?php echo e($error); ?>"
    }, "<?php echo e(config('app.url')); ?>");
    window.close();
  </script>
</head>
<body>
</body>
</html><?php /**PATH D:\Web Development\chibog\web\resources\views/socialite-callback.blade.php ENDPATH**/ ?>