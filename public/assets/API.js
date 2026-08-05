class API {

    // For now I'm making people pass a component string for component API calls.
    // I need to think about a smarter strategy to figure out routing somehow.
    static post(action, payload = {}, component = null) {
        return $.ajax({
            type        : "POST",
            url         : `${window.location.href}?action=${action}${component ? `&component=${component}` : ``}`,
            contentType : "application/json",
            data        : JSON.stringify(payload),
        });
    }
}

window.API = API;