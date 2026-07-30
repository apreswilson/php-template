(function () {
    function renderMessages(messages) {
        const $list = $('#example-message-list');
        $list.empty();
        messages.forEach((message) => {
            const badge = message.pinned ? '<span class="pin-badge">pinned</span>' : '';
            $list.append(`<li data-id="${message.id}"><span>${message.body}</span>${badge}</li>`);
        });
    }

    function refresh() {
        $('[data-action="loadMessages"]').trigger('click');
    }

    API.register(`createExampleTable`, async (context) => {
        await context.post();
    });

    API.register(`loadMessages`, async (context) => {
        const messages = await context.post();
        renderMessages(messages);
    });

    API.register(`addMessage`, async (context) => {
        const body = $('#example-message-input').val();
        await context.post({ body });
        $('#example-message-input').val('');
        refresh();
    });

    API.register(`togglePinMessage`, async (context) => {
        const firstId = $('#example-message-list li').first().data('id');
        await context.post({ id: firstId });
        refresh();
    });

    API.register(`deleteMessage`, async (context) => {
        const firstId = $('#example-message-list li').first().data('id');
        await context.post({ id: firstId });
        refresh();
    });

})();