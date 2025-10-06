<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <title>{{ $title ?? 'People Mangement System' }}</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <div class="container">
    @yield('content')
  </div>
</body>
</html>