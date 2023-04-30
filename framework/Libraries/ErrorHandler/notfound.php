<?php http_response_code(404) ?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Not Found</title>
    <style>
      <?php require_once 'style.css' ?>
    </style>
  </head>

  <body>
    <h1>Page Not Found</h1>
    <p>The page you're looking for does not seem to exist.</p>
  </body>

</html>

<?php exit(0) ?>