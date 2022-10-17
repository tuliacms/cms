<!doctype html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 Page not found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
</head>
<body class="d-flex h-100 text-center text-bg-dark">
    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <header class="mb-auto"></header>
        <main class="px-3">
            <h1 class="mb-5">404 Page not found</h1>
            <p><?php echo sprintf('Cannot find website for domain <code>%s</code> at path <code>%s</code>.', $host, $path); ?></p>
            <p>This is not expected error? Try this:</p>
            <p>
                1. Check if domain defined is Administration Panel is same as the requested (<code><?= $host ?></code>)
                <br />2. Check if website is active
                <br />3. Check if website's path prefix and locale prefix are set properly and exists in requested URL (<code><?= $path ?></code>)
            </p>
        </main>

        <footer class="mt-auto text-white-50">
            <p>Powered by <a href="https://tuliacms.org" target="_blank" class="text-white">Tulia CMS</a></p>
        </footer>
    </div>
</body>
</html>
