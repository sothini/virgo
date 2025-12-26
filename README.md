# Project Setup Guide

This document explains how to set up and run the project locally using Docker for the Laravel backend and Vite for the Vue frontend.

---

## Prerequisites

Make sure you have the following installed on your machine:

* Git
* Docker & Docker Compose
* Node.js (LTS recommended)
* npm

---

## Backend (Laravel + Docker)

### 1. Clone the Repository

```bash
git clone <repository-url>
cd <repository-name>
```

---

### 2. Navigate to the Backend Folder

```bash
cd larabackend
```

---

### 3. Environment Configuration

Copy the example environment file. It already contains default Docker-ready settings:

```bash
cp .env.example .env
```

---

### 4. Start Docker Containers

```bash
docker compose up -d
```

---

### 5. Install PHP Dependencies

```bash
docker compose exec app composer install
```

---

### 6. Generate Application Key

```bash
docker compose exec app php artisan key:generate
```

---

### 7. Run Migrations and Seeders

```bash
docker compose exec app php artisan migrate --seed
```

---

### 8. Generate Swagger Documentation

To generate Swagger API documentation:

```bash
docker compose exec app php artisan l5-swagger:generate
```

---

### 9. Access the Backend

The Laravel backend will be available at:

```
http://localhost:8000
```

---

## Frontend (Vue)

### 1. Navigate to the Frontend Folder

```bash
cd ../vuefrontend
```

---

### 2. Install Dependencies

```bash
npm install
```

---

### 3. Run the Development Server

```bash
npm run dev
```

---

## Authentication

You can log in using the **default Laravel user** created by the database seeders. Use the credentials provided in the seeder configuration.

---

## Notes

* Ensure Docker is running before starting the backend setup.
* If you change environment variables, restart the containers:

```bash
docker compose down
docker compose up -d
```

---

Happy coding 🚀
