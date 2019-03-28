<html>
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>
    <script>
      window.opener.postMessage({route: "{{ route_i18n('home') }}"}, '*')
      window.close()
    </script>
</head>
<body>
</body>
</html>
