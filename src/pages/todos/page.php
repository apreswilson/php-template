<?php
Page::loadAssets([__DIR__]);
$js_namespace = "todos";
?>

<p>Test abc</p>

<button data-action="getAllTodos">Get All Todos</button>
<button data-action="getAllTodos">Get All Todos</button>

<?php
Component::render('table');
?>
<button data-action="createNewTodo">CreateNewTodo</button>
<button data-action="updateTodo">UpdateTodo</button>
<button data-action="deleteTodo">deleteTodo</button>