<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>xx</title>
    <link rel="icon" href="{{ URL::to('/').'/favicon.ico' }}">

    {{ HTML::style('css/bootstrap.min.css') }}
    {{ HTML::style('font-awesome/css/font-awesome.min.css') }}

    {{ HTML::style('css/style.min.css') }}

    {{ HTML::script('js/jquery-3.1.1.min.js') }}
    {{ HTML::script('js/bootstrap.min.js') }}
</head>
<body class="gray-bg">

<div class="middle-box text-center">
    <h1>500</h1>
    <h3 class="font-bold">Dogodila se greška</h3>
    <div class="error-desc">
        Ispričavamo se, dogodila se neočekivana greška u aplikaciji te nije moguće izvršiti traženu radnju.
    </div>
    <div class="m-t-lg">
        <a href="{{ route('LoginPage') }}" class="btn btn-primary">Početna stranica</a>
    </div>
</div>

</body>
</html>