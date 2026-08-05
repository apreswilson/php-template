(function () {
    function renderMessages(messages) {
        const $list = $('#example-message-list');
        $list.empty();
        messages.forEach((message) => {
            const badge = message.pinned ? '<span class="pin-badge">pinned</span>' : '';
            $list.append(`<li data-id="${message.id}"><span>${message.body}</span>${badge}</li>`);
        });
    }

    async function loadMessages() {
        const messages = await API.post(`loadMessages`);
        renderMessages(messages);
    }

    $(`[data-action="createExampleTable"]`).on(`click`, async () => {
        await API.post(`createExampleTable`);
    });

    $(`[data-action="loadMessages"]`).on(`click`, async () => {
        await loadMessages();
    });

    $(`[data-action="addMessage"]`).on(`click`, async () => {
        const body = $('#example-message-input').val();
        await API.post(`addMessage`, { body });
        $('#example-message-input').val('');
        await loadMessages();
    });

    $(`[data-action="togglePinMessage"]`).on(`click`, async () => {
        const firstId = $('#example-message-list li').first().data('id');
        await API.post(`togglePinMessage`, { id: firstId });
        await loadMessages();
    });

    $(`[data-action="deleteMessage"]`).on(`click`, async () => {
        const firstId = $('#example-message-list li').first().data('id');
        await API.post(`deleteMessage`, { id: firstId });
        await loadMessages();
    });
})()