class API {
    static actions = {};

    static execute(action, element, component = null) {
        const callback = this.actions[action];

        if (!callback) {
            throw new Error(`API action '${action}' is not registered`);
        }

        const context = {
            action,
            element,

            send(payload = {}) {
                return $.ajax({
                    type        : "POST",
                    url         : `${window.location.href}?action=${action}${component ? `&component=${component}` : ``}`,
                    contentType : "application/json",
                    data        : JSON.stringify(payload),
                });
            }
        }

        return callback(context);
    }

    static register(action, callback) {
        this.actions[action] = callback
    }
}

window.API = API;