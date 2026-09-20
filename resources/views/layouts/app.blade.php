<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
</head>
<body>

    <nav>
        <a href="/">Home</a> | 
        <a href="/tentang">Tentang</a> | 
        <a href="/kontak">Kontak</a>
    </nav>

    <hr>

    <main>
        @yield('content')
    </main>

</body>
</html>