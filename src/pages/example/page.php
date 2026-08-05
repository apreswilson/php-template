<?php
Page::loadAssets([
    __DIR__,
]);
?>

<div class="doc">

    <header class="doc-hero">
        <span class="doc-eyebrow">// rootphp reference</span>
        <h1>Example Page</h1>
        <p class="doc-lede">
            This page exists as a live reference. Every core feature of the framework is used
            somewhere below, next to a short explanation of what's happening.
        </p>
    </header>

    <section class="doc-section">
        <div class="doc-label">Components</div>
        <p>
            Components live under src/components/{name}/component.php and are rendered with
            Component::render(name, props). Props are instance-based, so the same component can be
            rendered multiple times on one page with different data each time.
        </p>

        <div class="card-row">
            <?php
            Component::render('card', [
                "title" => "First Card",
                "body"  => "This card was rendered with one set of props."
            ]);

            Component::render('card', [
                "title" => "Second Card",
                "body"  => "This is the same component, rendered again with different props."
            ]);
            ?>
        </div>

        <p>
            A component can be just as involved as a page. The reaction widget below bundles
            its own controller.php, reaction.js, and reaction.css, and manages its own table
            independently of the page around it. Both instances below track separate counts.
        </p>

        <div class="reaction-row">
            <?php
            Component::render('reaction', [
                "id"    => "example-star",
                "label" => "Star This Page"
            ]);

            Component::render('reaction', [
                "id"    => "example-fire",
                "label" => "This Is Fire"
            ]);
            ?>
        </div>
    </section>

    <section class="doc-section">
        <div class="doc-label">API Round Trip</div>
        <p>
            Everything below is a live example of a full front-to-back API call. Each button has a
            data-action attribute, whose value matches a function name in controller.php. That same
            name is registered in example.js via window.API.register(), which is what actually fires
            the POST request and handles the response.
        </p>

        <div class="step-track">

            <div class="step">
                <div class="step-index">01</div>
                <div class="step-body">
                    <h3>Setup</h3>
                    <p>
                        Calls createExampleTable() in the controller, which runs a Database::query()
                        using CREATE TABLE IF NOT EXISTS. Click this once before trying the buttons below.
                    </p>
                    <button data-action="createExampleTable">Create Example Table</button>
                </div>
            </div>

            <div class="step">
                <div class="step-index">02</div>
                <div class="step-body">
                    <h3>Read</h3>
                    <p>Calls loadMessages() in the controller, which runs a Database::query() and returns the rows.</p>
                    <button data-action="loadMessages">Load Messages</button>
                    <ul id="example-message-list" class="console"></ul>
                </div>
            </div>

            <div class="step">
                <div class="step-index">03</div>
                <div class="step-body">
                    <h3>Create</h3>
                    <p>Sends the text below as a payload to addMessage(string $body) in the controller.</p>
                    <div class="control-row">
                        <input type="text" id="example-message-input" placeholder="Write a message">
                        <button data-action="addMessage">Add Message</button>
                    </div>
                </div>
            </div>

            <div class="step">
                <div class="step-index">04</div>
                <div class="step-body">
                    <h3>Update</h3>
                    <p>Sends the id of the first message to togglePinMessage(int $id) in the controller.</p>
                    <button data-action="togglePinMessage">Toggle Pin On First Message</button>
                </div>
            </div>

            <div class="step">
                <div class="step-index">05</div>
                <div class="step-body">
                    <h3>Delete</h3>
                    <p>Sends the id of the first message to deleteMessage(int $id) in the controller.</p>
                    <button data-action="deleteMessage" class="button-danger">Delete First Message</button>
                </div>
            </div>

        </div>
    </section>
</div>