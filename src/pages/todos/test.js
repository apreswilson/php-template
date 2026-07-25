(function (API, $) {
    API.register(`getAllTodos`, async (context) => {
        const todos = await context.send();
    });

    API.register(`createNewTodo`, async (context) => {
        const title       = 'Alex Title';
        const description = 'Test Description';
        const todos       = await context.send({ title, description });
    });

    API.register(`updateTodo`, async (context) => {
        const completed = true;
        const title     = 'Alex Title';
        const todos     = await context.send({ completed, title });
    });

    API.register(`deleteTodo`, async (context) => {
        const title = 'Alex Title';
        const todos = await context.send({ title });
    });

})(window.API, jQuery);
