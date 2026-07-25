(function(API, $) {
    API.register(`tableTest`, async (context) => {
        const todos = await context.send();
        console.log(todos);
    });
})(API, jQuery);