<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://getbootstrap.com/docs/5.0/dist/css/bootstrap.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .competitionImage1 {
            content: "";
            background: url('../i/sprites/global-sprite.png') -432px -530px no-repeat;
            width: 170px;
            height: 28px;
            display: block;
        }

        .logo-img {
            margin-left: -20px;
        }

    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <a class="navbar-brand" href="{{ route('new') }}">
                    <img class="logo-img" height="80"
                        src="https://lh3.googleusercontent.com/proxy/wgNWvNFiyHNw4iXbxdQjJ1w6F_wf85cDvfShWTFg9cz7Sf5bxXiW70bQON8PlUcwYP_EYVBTIqnrZvzMq1ztRudJ0G-50qttMOenImP34K1fc5Hf2FCOPfOIW9m6gtOwy1UPWal4zw"
                        alt="">
                </a>
            </div>
        </nav>
    </header>
    <main class="d-flex align-items-center">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    @yield('page_script')
</body>

</html>
