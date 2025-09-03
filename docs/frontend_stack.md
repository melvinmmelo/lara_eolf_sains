# Frontend Stack & Design System

This project's frontend is built on a combination of a popular admin template and several modern JavaScript libraries. The assets are compiled and bundled using Vite.

## Core Technologies

*   **AdminLTE 3:** The primary design system and UI framework for the application's backend and administrative interfaces. AdminLTE provides a responsive and reusable set of components, including dashboards, tables, forms, and modals. The main AdminLTE assets are likely included directly in the main Blade layout files.

*   **jQuery:** The project includes jQuery, which is a dependency for AdminLTE and is used for various DOM manipulations and event handling.

*   **Vite:** The build tool used for compiling and bundling frontend assets (CSS and JavaScript).

## Asset Loading

The main entry points for the application's assets are:

*   `resources/css/app.css`: Imports Tailwind CSS.
*   `resources/js/app.js`: Initializes the Axios HTTP library.

The AdminLTE and other vendor libraries are likely included directly in the main Blade layout files from the `public/vendor` or `public/plugins` directories.
