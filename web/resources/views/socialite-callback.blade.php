<html>
<head>
  <meta charset="utf-8">
  <title>Callback</title>
  <script>
    window.opener.postMessage({
    	api_token: "{{ $api_token }}",
    	current_user: "{{ json_encode($current_user) }}",
    	error: "{{ $error }}"
    }, "{{ config('app.url') }}");
    window.close();
  </script>
</head>
<body>
</body>
</html>