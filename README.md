It seems like there's an extra backtick (\`) at the end of the file, which is causing the formatting issue. To fix that, simply remove the backtick at the end of your file (after the last "Contact" section).

Here’s the corrected version without the extra backtick:

---

# Translation Management API

A Laravel-based API for managing multi-language translations with token authentication and advanced features like tag filtering and JSON export.

---

## Features

* Multi-language support
* Tag-based filtering
* JSON export
* Token authentication using Laravel Sanctum
* 100k+ seed records for testing and development

---

## Table of Contents

* [Setup](#setup)
* [Docker Setup](#docker-setup)
* [Running the Application](#running-the-application)
* [Database Commands](#database-commands)
* [Testing](#testing)
* [API Documentation](#api-documentation)
* [Contributing](#contributing)
* [License](#license)
* [Contact](#contact)

---

## Setup

Follow these steps to get the application running locally without Docker:

1. Clone the repository:

   ```bash
   git clone https://github.com/Ahmedkhnunar/Translation-Management-API.git
   cd Translation-Management-API
   ```

2. Install PHP dependencies with Composer:

   ```bash
   composer install
   ```

3. Copy the example environment file and generate an application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Run database migrations and seeders:

   ```bash
   php artisan migrate --seed
   ```

5. Serve the application locally:

   ```bash
   php artisan serve
   ```

---

## Docker Setup

To run the application using Docker for easier environment setup:

1. Build the Docker image:

   ```bash
   docker build -t translation-api .
   ```

2. Run the container exposing port 8000:

   ```bash
   docker run -p 8000:8000 translation-api
   ```

3. Run migrations and seeders inside the running container:

   ```bash
   docker exec -it <CONTAINER_ID> php artisan migrate --seed
   ```

   Replace `<CONTAINER_ID>` with the actual container ID from `docker ps`.

---

## Running the Application

Access the API documentation and test endpoints here:
[http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

---

## Database Commands

* Run migrations:

  ```bash
  php artisan migrate
  ```

* Seed the database:

  ```bash
  php artisan db:seed
  ```

---

## Testing

Run the test cases with artisan:

```bash
php artisan test
```

Or inside the Docker container:

```bash
docker exec -it <CONTAINER_ID> php artisan test
```

---

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes (`git commit -m 'Add some feature'`)
4. Push to the branch (`git push origin feature/my-feature`)
5. Open a Pull Request

---

## Contact

Ahmed Khan - [ahmedkhnunar@gmail.com](mailto:ahmedkhnunar@gmail.com)
Project Link: [https://github.com/Ahmedkhnunar/Translation-Management-API](https://github.com/Ahmedkhnunar/Translation-Management-API)

---

That should resolve the display issue you're experiencing. Let me know if there's anything else you'd like to adjust!
