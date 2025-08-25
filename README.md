# Sloth Project

Full-stack application split into frontend (sloth-web) and backend (sloth-api) components.

## Prerequisites

- Docker and Docker Compose
- Node.js (v18 or higher)
- npm (v9 or higher)

## Installation

### Backend (sloth-api)

1. Navigate to the API directory:
   ```bash
   cd sloth-api
   ```

2. Copy environment file:
   ```bash
   cp .env.example .env
   ```

3. Build Docker containers:
   ```bash
   docker compose build
   ```

### Frontend (sloth-web)

1. Navigate to the web directory:
   ```bash
   cd sloth-front
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Copy environment file:
   ```bash
   cp .env.example .env
   ```

## Running with Docker

1. Start the backend services:
   ```bash
   cd sloth-api
   docker compose up -d
   ```

2. Start the frontend development server:
   ```bash
   cd sloth-front
   npm run dev
   ```

## Development Setup

### Backend Development

- Access the API container shell:
  ```bash
  docker compose exec sloth bash
  ```

- Generate application key:
  ```bash
  php artisan key:generate
  ```

- Link storage directory:
  ```bash
  php artisan storage:link
  ```

- Run database migrations:
  ```bash
  php artisan migrate
  ```

- Run database seeder (if available):
  ```bash
  php artisan db:seed --class=DatabaseSeeder
  ```

- Configure environment variables in .env:
  ```bash
  APP_URL=http://localhost:8876
  SANCTUM_STATEFUL_DOMAINS=localhost:5173
  SESSION_DOMAIN=localhost
  FRONTEND_URL=http://localhost:5173
  ```

### Frontend Development

- Access the application at: `http://localhost:5173`
- Build for production:
  ```bash
  npm run build
  ```

## Additional Information

- Database adminer interface: `http://localhost:8080`
