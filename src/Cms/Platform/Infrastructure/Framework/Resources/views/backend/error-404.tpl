<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 Page Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
</head>
<body class="d-flex h-100 text-center text-bg-dark">
<div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
    <header class="mb-auto"></header>
    <main class="px-3">
        <a class="mb-5 d-block" href="{{ path('backend.homepage') }}">
            <img src="{{ asset('/assets/core/backend/theme/images/logo-reverse.svg') }}" alt="Tulia CMS" style="width:260px" />
        </a>
        <h1 class="mb-5">404 Page Not Found</h1>
        <a href="{{ path('backend.homepage') }}" class="text-white">Go to Homepage</a>
    </main>

    <footer class="mt-auto text-white-50">
        <p>Powered by <a href="https://tuliacms.org" target="_blank" class="text-white">Tulia CMS</a></p>
    </footer>
</div>
</body>
</html>
