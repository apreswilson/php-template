(function () {
    $(`[data-action="incrementReaction"]`).on(`click`, async function () {
        const reactionId = $(this).data('reaction-id');
        const result = await API.post(`incrementReaction`, { reactionId }, `reaction`);
        $(`.reaction[data-reaction-id="${reactionId}"] .reaction-count`).text(result.count);
    });
})();