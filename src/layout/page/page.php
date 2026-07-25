<?php
    /** @var array $page */
    /** @var string $route */

    // Workaround for trying to get css before the actual page.php content is rendered.
    ob_start();
    require $page;
    $content = ob_get_clean();
?>

<!DOCTYPE html>
<html>
    <head>

    <!-- Load CSS files -->
    <?php Page::renderStyles($route) ?>

    <!-- Import jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    </head>

    <body>
        <?php 
            require_once __DIR__ . '/../header/header.php';
            echo $content;
            require_once __DIR__ . '/../footer/footer.php'; 
        ?>
    </body>

    <!-- Load JS files -->
    <script src="/assets/API.js"></script>
    <script src="/assets/events.js"></script>
    <?php Page::renderScripts($route) ?>
</html>