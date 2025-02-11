Certainly! I'll apply the changes to the README file. Here's the updated content for the file:
README.md
# MCPI Applicant Registration System

This project is a Laravel-based application for managing applicant registrations.

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/mcpi-repo.git
   cd mcpi-repo/applicant-app
2. Install PHP dependencies:
    ```bash 
    composer install
3. Install JavaScript dependencies:
    ```bash
   npm install
4. Create a copy of the .env.example file and rename it to .env:
    ```bash
    cp .env.example .env
5. Generate an application key:
    ```bash
    php artisan key:generate
6. Configure your database settings in the .env file.

7. Run database migrations:
    ```bash
    php artisan migrate
   
## Running the Project
1. Start the Laravel development server:
    ```bash
    php artisan serve
2. Compile assets:
    ```bash
    npm run dev
3. Visit http://localhost:8000 in your web browser to access the application.

## Contributing
Please read our Contributing Guide before submitting a Pull Request to the project.
