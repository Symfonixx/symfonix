<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Page Not Found</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f5f5f5;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #333;
        }
        .container {
            text-align: center;
            padding: 2rem 3rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        p {
            margin: 0.25rem 0;
        }
        a {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.5rem 1.25rem;
            border-radius: 999px;
            background: #0d6efd;
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p>The page you are looking for could not be found.</p>
        <p>Please check the URL or return to the homepage.</p>
        <a href="<?php echo e(url('/')); ?>">Go Home</a>
    </div>
</body>
</html>



<?php /**PATH D:\websites\symfonix\resources\views/errors/404.blade.php ENDPATH**/ ?>