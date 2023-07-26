# Laravel E-commerce Demo with TALL Stack

This is a demo e-commerce website built with the TALL stack (Tailwind CSS, Alpine.js, Laravel, and Livewire).

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

- PHP >= 8.1
- Composer
- Node.js & npm
- A local SQL database

### Installation

Clone the repository locally:

```bash
git clone https://github.com/username/project.git
cd project
```
Install PHP dependencies:
```bash
composer install
```
Install NPM dependencies:
```bash
npm install
npm run build
```
Create a local `.env` file:
```bash
cp .env.example .env
```
Generate an app encryption key:
```bash
php artisan key:generate
```
Create an empty SQL database for the project. Then, in the `.env` file, add database information to allow Laravel to connect to the database.

Migrate the database:
```bash
php artisan migrate --seed
```
Start the local development server:
```bash
php artisan serve
```
You can now access the server at http://localhost:8000
