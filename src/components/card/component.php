<?php
/** @var Props $props*/

$title = $props->get('title');
$body = $props->get('body');
?>

<div class="example-card">
    <h4><?= htmlspecialchars($title) ?></h4>
    <p><?= htmlspecialchars($body) ?></p>
</div>