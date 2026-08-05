<?php
/** @var array $page */
/** @var string $route */

// Execute the page first so Page::loadAssets() registers page assets before the layout imports CSS.
// The only other workaround to this would be doing another require $page, which we don't want to do.
ob_start();
require $page;
$content = ob_get_clean();

// Load layout components
// Todo: Maybe just strip the page.php after the last / from $page and just have it automatically include the directory
// so that not every page.php needs to call page::loadAssets redundantly on its own directory.
// I'm undecided on this for now though so I'm leaving it for now.
Page::loadAssets([
    __DIR__ . '/../header',
    __DIR__ . '/../footer',
    __DIR__
]);
?>

<!DOCTYPE html>
<html>
<head>

    <?php Page::importAssets('css'); ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</head>

<body>

<?php
require_once __DIR__ . '/../header/header.php';

echo $content;

require_once __DIR__ . '/../footer/footer.php';
?>

<script src="/assets/API.js"></script>

<?php Page::importAssets('js'); ?>

</body>
</html>