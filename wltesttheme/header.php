<?php
/**
 * Header template
 */
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>WL Test</title>
    <?php wp_head();?>
</head>
<body>
<header>
    <div style="display: flex; flex-direction: row; justify-content: space-between">
        <div>
            <h4>Номер телефона: <?= get_theme_mod('custom_phone_number', ''); ?></h4>
        </div>
         <div>
             <img src="<?= get_theme_mod('custom_site_logo', ''); ?>" alt="Logo">
         </div>
    </div>
</header>
