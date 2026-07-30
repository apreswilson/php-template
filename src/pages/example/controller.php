<?php

namespace Pages\Example;

use Database;

function createExampleTable() {
    return Database::query("
        CREATE TABLE IF NOT EXISTS example_messages (
            id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
            body TEXT NOT NULL,
            pinned BOOLEAN NOT NULL DEFAULT FALSE
        )
    ");
}

function loadMessages() {
    return Database::query("
        SELECT *
        FROM example_messages
        ORDER BY id DESC
    ");
}

function addMessage(string $body, bool $pinned = false) {
    return Database::query("
        INSERT INTO example_messages (
            body,
            pinned
        )
        VALUES (
            :body,
            :pinned
        )
    ", [
        "body" => $body,
        "pinned" => $pinned
    ]);
}

function togglePinMessage(int $id) {
    return Database::query("
        UPDATE
            example_messages
        SET
            pinned = NOT pinned
        WHERE
            id = :id
    ", [
        "id" => $id
    ]);
}

function deleteMessage(int $id) {
    return Database::query("
        DELETE FROM
            example_messages
        WHERE
            id = :id
    ", [
        "id" => $id
    ]);
}