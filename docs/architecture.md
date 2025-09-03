# Application Architecture

This project follows a standard **Model-View-Controller (MVC)** architecture, which is the default pattern for Laravel applications. However, it has been extended with a **Service Layer** to better organize and encapsulate business logic.

## Core Architectural Components

*   **Models:** Located in `app/Models`, these classes represent the application's data and interact with the database. They define the relationships between different data entities and may contain simple data formatting logic through accessors and mutators.

*   **Views:** Located in `resources/views`, these are the Blade templates responsible for rendering the user interface. They receive data from the controllers and display it to the user.

*   **Controllers:** Located in `app/Http/Controllers`, these classes handle incoming HTTP requests, retrieve data from the models (or services), and pass it to the views. They act as the intermediary between the user's actions and the application's response.

*   **Service Layer:** Located in `app/Services`, this layer contains the application's core business logic. By abstracting complex business processes into services, the controllers can remain lean and focused on handling HTTP requests. This makes the code more reusable, maintainable, and easier to test.

    *   **Example:** The `InboundService` and `InboundProductsService` contain the logic for managing inbound orders and their associated products. The `InboundController` then uses these services to perform its tasks.

*   **Routes:** Defined in the `routes/` directory, these files map incoming URLs to specific controller actions. The application uses separate files for web routes (`web.php`) and AJAX routes (`ajaxreq.php`).

## Architectural Flow

A typical request flows through the application as follows:

1.  An HTTP request is sent to a URL.
2.  The routing engine in `routes/web.php` or `routes/ajaxreq.php` matches the URL to a controller action.
3.  The controller receives the request and may call upon a service from the `app/Services` directory to execute the required business logic.
4.  The service interacts with the models in `app/Models` to retrieve or modify data in the database.
5.  The service returns the result to the controller.
6.  The controller passes the data to a view in `resources/views`.
7.  The view renders the final HTML, which is sent back to the user's browser.

## Design Notes

*   The use of a service layer is a key architectural decision that helps to keep the codebase organized and scalable.
*   The application makes extensive use of Laravel's features, including Eloquent ORM, Blade templating, and middleware for authentication and authorization.
*   The frontend is tightly coupled with the backend, with Blade views rendering the HTML directly. This is a traditional server-side rendering approach.
