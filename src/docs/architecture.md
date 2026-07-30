# Background

This is the RootPHP framework.

This framework is pretty opinionated and strict about structuring conventions.

The nice thing about this framework is that nothing is abstracted out, WYSIWYG. 

You can modify it in any you need to suit your project. This framework is purely just meant to be a nice starting point that offers some out of the box architecture at your disposal.

# Architecture

Refer to src/pages/example for a live example of a page utilizing all the builtin
code features of this framework.

For a more high level overview, read this doc.

## Page structuring

Routes in this are built based on file structure. So for example:

`pages/todos` will route to `{url}/todos` in the browser.

As of right now there is not support for nesting page routes, that may be added in the future.

## page.php

Inside your pages/{directory} file, there **MUST** be a page.php file. 
The page.php file serves as the main entry point file for that page.

## controller.php

Inside your pages/{directory} file, you can create a controller.php file that is used to serve as your backend
logic for database operations. It must be named controller.php.

Note: All controller.php files must have a namespace of Pages\{your_page_name} in it.

## Other page assets

This framework allows you to contain all of your source code for a page in one directory, which is great!

This means you can include static assets like your pages css or js in the same directory as your dynamic assets.

To load all assets of the page, use the Page class and call `Page::loadAssets([__DIR__])` and thats it. The Page class
handles the rest of it for you. 

You can include other files in the assets array in there by just supplying the file path relative to that page.php file.
Classes will be caught by the autoloader so you don't need to worry about that.

## API

This framework has a builtin API architecture for you to utilize as well that makes it very easy to make a full data
round trip.

Under the hood, everything is just POST. This framework does not use PATCH, PUT, or GET for API calls.

In order to set up a functional endpoint front to back, you must have this established:

1. In your page.php file, the html html html html html html html html html element triggering the api call must have a `data-action` attribute. The value **MUST** be the exact same as the name of the backend function.

2. In your controller.php file, define a new function that matches the value of the `data-action` attribute. Then have any logic in there that you need for that specific function. The function must return an array. That includes an empty array if
there isn't any data needed in the response.

3. Inside the JS file, call `window.API.register()`. The function accepts 2 parameters. The first is the name of the 
backend function which is also the name of the `data-action` attribute. The second is a callback function that takes
a single context parameter. In the background, the context parameter is an object that contains information about the 
element, action name, and a function `.post()`. When you want to actually make your POST request, inside the callback
you call the `post()` function, and pass the payload in an object key-value format. 

And thats it. You have a full API endpoint setup!

## Components

Yes, this framework has components logic built into it as well. If you want to make a new component, it must live under
src/components/*. The name of the directory will be the name of the component. The entry point file for the component **MUST**
be component.php.

You can then in your page.php file call all the code for the component by using the `Component::render()` function. The 
props param should be used in the same philosophy as react components.

Inside the component, you can define the vars in comments for your IDE to not complain at you, but this is not required.
Just call them inside the component using the Props class `get()` method. Since the props class is instance based and not
static, you can call the same component to be on your page as many times as you want.

Components are also autoloaded by the Page class, so you don't need to worry about using the Page class inside components.

**IMPORTANT**: Any html element inside a component that has a data-action api call attribute also needs a data-component attribute. This value should be the same as the name of the component itself.

## Classes

This framework has some out of the box classes that you can utilize that include:

- Database: A singleton designed Database class. You only need to use the `Database::query()` function.
- Page: This handles all the logic for getting assets for the current page. You really only need to use the `Page::loadAssets()` function.
- Props / Component: These two classes are used for the component related logic this framework offers. Read the component
section for more information.
- EnvironmentVariables: This just autoloads any environment variables and stores them in the $_ENV superglobal.
- Router: This handles all routing for GET and POST requests from the client.
- RouterResponse: A unified format of response data.