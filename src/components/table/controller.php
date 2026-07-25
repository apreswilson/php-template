<?php

namespace Components\Table;

use Database;

function tableTest() {
    return Database::query("SELECT * FROM todos");
}