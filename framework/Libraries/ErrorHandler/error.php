<?php http_response_code(500) ?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>500 - Internal Server Error</title>
        <style>
            <?php require_once 'style.css' ?>
        </style>
    </head>

    <body>

        <div class="box">
            <h2>Internal Server Error</h2>
            <p>Oops... Something went wrong.</p>
        </div>

    </body>

</html>

<?php exit(1) ?>