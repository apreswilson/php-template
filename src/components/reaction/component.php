<?php
/** @var Props $props*/

$id    = $props->get('id');
$label = $props->get('label');

$existing = Database::query("
    SELECT count FROM example_reactions WHERE reaction_id = :reaction_id
", ["reaction_id" => $id]);

$count = $existing[0]['count'] ?? 0;
?>

<div class="reaction" data-reaction-id="<?= htmlspecialchars($id) ?>">
    <button data-action="incrementReaction" data-component="reaction" data-reaction-id="<?= htmlspecialchars($id) ?>">
        <span class="reaction-icon">&#9733;</span>
        <span><?= htmlspecialchars($label) ?></span>
    </button>
    <span class="reaction-count"><?= (int) $count ?></span>
</div>