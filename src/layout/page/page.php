<?php
    /** @var string $page */
    /** @var string $route */

    // Workaround for trying to get css before the actual page.php content is rendered.
    ob_start();
    require $page;
    $content = ob_get_clean();
?>

<!DOCTYPE html>
<html>
    <head>

    <?php
        foreach (Page::getAssetsByType('css') as $css) {
            $url = $css . "&page=$route";
            echo "<link rel=\"stylesheet\" href=\"$url\"></link>";
        }
    ?>

    </head>
    <body>
        <?php 
            require_once __DIR__ . '/../header/header.php';
            echo $content;
            require_once __DIR__ . '/../footer/footer.php'; 
        ?>
    </body>

    <?php
        foreach (Page::getAssetsByType('js') as $js) {
            $url = $js . "&page=$route";
            echo "<script src=\"$url\"></script>";
        }
    ?>
</html>