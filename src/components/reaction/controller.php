<?php

namespace Components\Reaction;

use Database;

function incrementReaction(string $reactionId) {
    Database::query("
        CREATE TABLE IF NOT EXISTS example_reactions (
            reaction_id VARCHAR(191) PRIMARY KEY,
            count INTEGER NOT NULL DEFAULT 0
        )
    ");

    Database::query("
        INSERT INTO example_reactions (reaction_id, count)
        VALUES (:reaction_id, 1)
        ON CONFLICT (reaction_id)
        DO UPDATE SET count = example_reactions.count + 1
    ", [
        "reaction_id" => $reactionId
    ]);

    $row = Database::query("
        SELECT count
        FROM example_reactions
        WHERE reaction_id = :reaction_id
    ", [
        "reaction_id" => $reactionId
    ]);

    return [
        "count" => $row[0]["count"] ?? 0
    ];
}