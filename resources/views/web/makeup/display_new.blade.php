<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>test</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" type="text/css">
    <script>
        window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
<div id="app">

</div>

<script src="{{ asset('js/main.js') }}"></script>
<script>

</script>
</body>
</html>