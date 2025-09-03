# Technology Stack

This document outlines the primary technologies, frameworks, and libraries used in the EOLF Sains project.

## Backend

*   **Framework:** [Laravel 11](https://laravel.com/)
*   **Language:** PHP 8.2
*   **Database:** The project uses a SQL database, likely MySQL or MariaDB, as suggested by the `.sql` files in the `database` directory.
*   **Package Manager:** [Composer](https://getcomposer.org/)

### Key PHP Libraries

*   **Spatie Laravel Permission:** For managing user roles and permissions.
*   **Spatie Laravel Backup:** For handling automated backups of the application and database.
*   **Spatie Laravel Activitylog:** For logging user activities and model events.
*   **PhpSpreadsheet:** For reading and writing spreadsheet files (e.g., Excel, CSV).

## Frontend

*   **UI Framework:** [AdminLTE 3](https://adminlte.io/)
*   **CSS Framework:** [Tailwind CSS](https://tailwindcss.com/)
*   **JavaScript Libraries:**
    *   [Alpine.js](https://alpinejs.dev/): For lightweight, declarative JavaScript interactivity.
*   **Build Tool:** [Vite](https://vitejs.dev/)
*   **Package Manager:** [NPM](https://www.npmjs.com/)

## Development & Tooling

*   **Testing:** [PHPUnit](https://phpunit.de/)
*   **Code Styling:** [Laravel Pint](https://laravel.com/docs/11.x/pint) for ensuring a consistent code style.
