<!doctype html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
</head>
<body>


<div>
    <header class="bg-primary text-white d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom" style="--bs-bg-opacity: .2; padding-left: 100px; padding-right: 50px">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
            <img width="34" height="34" src="https://cdn-icons-png.flaticon.com/512/1104/1104982.png">
            <span class="text-primary" style="padding-left: 10px" class="fs-4"><b>BD FUTURES MANAGEMENT</b></span>
        </a>

        <ul class="nav nav-pills">
            <li class="nav-item"><a href="#" class="nav-link active text-white" aria-current="page">Home</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Settings</a></li>
            <li class="nav-item"><a href="#" class="nav-link">FAQs</a></li>
            <li class="nav-item"><a href="#" class="nav-link">About</a></li>
        </ul>
    </header>
</div>



<div class="container">
    @yield('content')
</div>
</header>
</body>
</html>
