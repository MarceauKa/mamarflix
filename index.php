<?php require 'src/helpers.php'; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>movies2sheet</title>
    <link rel="stylesheet" href="frontend/dist/app.css">
</head>
<body>
    <div id="app">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <app></app>
          </div>
        </div>
      </div>
    </div>

    <script>
      const m2s = <?php echo file_get_contents(base_path('data/database.json')); ?>
    </script>
    <script src="frontend/dist/app.js" defer></script>
</body>
</html>