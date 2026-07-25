(function ($) {
    "use strict";

    // Initializer function
    function init() {
        bindActions();
    }

    function bindActions() {
        // Assign all data-action attribute elements to make ajax post calls on click
        $(`[data-action]`).each(function() {
            const $element = $(this);

            $element.on(`click`, function (e) {
                // You only put data-component attributes on component elements making API calls
                const component = $(this).data(`component`);
                API.execute($(this).data(`action`), $(this), component);
            });
        });
    }

    init();

})(jQuery);