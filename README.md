# Project Description: EOLF Trading Sales and Inventory System 2024

DevTime: 3-4 months

Date: 13-Mar-2024


[Library assets](https://drive.google.com/drive/folders/1YQFwJKJAOsyqeei3FZ_5CjWtQgmQa3cA?usp=drive_link)


How to setup? Follow the instructions.

1. **Install Docker**

Laravel Sail is a lightweight command-line interface for interacting with Docker. You'll need to have Docker installed on your machine. You can download and install Docker Desktop from the official website (https://www.docker.com/products/docker-desktop).

2. **Install Laravel**

Next, you'll need to install the latest version of Laravel. Open your terminal or command prompt and run the following command:

```bash
curl -s https://laravel.build/laravel-11.x | bash
```

This will create a new Laravel 11 project in the current directory.

3. **Initialize Laravel Sail**

Navigate into the newly created Laravel project directory and run the following command to initialize Laravel Sail:

```bash
cd your-project-name
./vendor/bin/sail:install
```

This command will create the necessary Docker configuration files for Laravel Sail.

4. **Start Laravel Sail**

Once the installation is complete, you can start the Docker containers using the following command:

```bash
./vendor/bin/sail up
```

This command will build the Docker containers and start the Laravel development server.

5. **Configure Laravel Sail**

By default, Laravel Sail comes pre-configured with a MySQL database container. However, you can customize the Docker configuration files (`docker-compose.yml` and `Dockerfile`) to suit your project's needs.

6. **Share the Project with Your Team**

To share the project with your team members, you can commit the entire project directory to a version control system like Git. Your team members can then clone the repository and follow the same steps (install Docker, run `./vendor/bin/sail:install`, and `./vendor/bin/sail up`) to set up the development environment on their machines.

7. **Access the Application**

Once the Docker containers are up and running, you can access the Laravel application by visiting `http://localhost` in your web browser. Any changes you make to the code will be reflected immediately, thanks to the built-in file watcher in Laravel Sail.

By following these instructions, you'll have a Laravel 11 project set up with Laravel Sail, ready for development and easy to share with your team members. Additionally, Laravel Sail provides a consistent development environment across different machines, ensuring that your team members won't face any compatibility issues.

If you or your team members encounter any issues during the setup process, feel free to consult the official Laravel Sail documentation (https://laravel.com/docs/10.x/sail) or reach out for further assistance.

** Links
[Git download](https://git-scm.com/download/win)
[Composer download](https://getcomposer.org/Composer-Setup.exe)
[Laravel Documentation](https://laravel.com/docs/11.x/installation)

Run

```
composer install
```
