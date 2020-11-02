<?php
  require 'vendor/autoload.php';
  $assetsPath = isset($exporting) ? '.mamarflix/' : 'data/images/';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mamarflix</title>
    <style type="text/css"><<?= file_get_contents(base_path('frontend/dist/app.css')); ?></style>
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
    <script type="text/javascript">
      const assetsPath = '<?= $assetsPath; ?>';
      const m2s = <?= file_get_contents(base_path('data/database.json')); ?>;
    </script>
    <script type="text/javascript" defer>
      <?= file_get_contents(base_path('frontend/dist/app.js')); ?>
    </script>
</body>
</html>