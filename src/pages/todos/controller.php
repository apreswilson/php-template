<?php

namespace Pages\Todos;

use Database;

function getAllTodos() {
    return Database::query("SELECT * FROM todos");
}

function createNewTodo(string $title, string $description) {
    return Database::query("
        INSERT INTO todos (
            title,
            description
        )
        VALUES (
            :title,
            :description
        )
    ", ["title" => $title, "description" => $description]);
}

function updateTodo(bool $completed, string $title) {
    return Database::query("
        UPDATE 
            todos
        SET 
            completed = :completed
        WHERE
            title = :title
    ", ["completed" => $completed, "title" => $title]);
}

function deleteTodo(string $title) {
    return Database::query("
        DELETE FROM
            todos
        WHERE
            title = :title
    ", ["title" => $title]);
}