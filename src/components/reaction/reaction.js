(function (API, $) {
    API.register(`incrementReaction`, async (context) => {
        const reactionId = $(context.element).data('reaction-id');
        const result = await context.post({ reactionId });
        $(`.reaction[data-reaction-id="${reactionId}"] .reaction-count`).text(result.count);
    });
})(window.API, jQuery);