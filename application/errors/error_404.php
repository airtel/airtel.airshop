<!DOCTYPE html>
<html lang="en" class="error_page">
    <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Error Page - 404</title>
        <!-- Bootstrap framework -->
        <link rel="stylesheet" href="<?php echo config_item('base_url');?>bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" href="<?php echo config_item('base_url');?>bootstrap/css/bootstrap-responsive.min.css" />
        <!-- main styles -->
        <link rel="stylesheet" href="<?php echo config_item('base_url');?>css/error.css" />
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Jockey+One" />
    </head>
    <body>

        <div class="error_box">
            <h1><?php echo $heading; ?></h1>
            <p><?php echo $message; ?></p>
            <a href="javascript:history.back()" class="back_link btn btn-small">Go back</a>
        </div>

    </body>
</html>