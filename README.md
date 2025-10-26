# Recapet Coding Challenge

## Installation Instructions

1. Ensure your PHP version is **8.4**.
2. Open the project and navigate to the **root directory** in your terminal.
3. Run the following command to install dependencies:
   ```bash
   composer install
   ```
4. Copy the `.env.example` file located in the root directory and rename it to `.env`.
5. Generate an application key by running:
   ```bash
   php artisan key:generate
   ```
6. Migrate the database tables and seed initial data using:
   ```bash
   php artisan migrate --seed
   ```
7. Start the development server with:
   ```bash
   php artisan serve
   ```
   If you’re using **Laravel Valet**, you can access the project at:
   ```
   http://project-name.test
   ```
8. The project should now be running successfully on your machine.

---

## Testing Credentials

- **Email:** moustafaalwakil@gmail.com
- **Password:** Password@123
  > Note: Each seeded user has an associated wallet that includes a predefined balance '$10,000.00', making it easy to test wallet-related features such as deposits, withdrawals, and transfers.
  > 
  > All seeded users share the same password: Password@123.
  >
  > You can find the **Postman collection** within the project files at the root directory under the name **`Recapet Coding Challenge.postman_collection.json`**.

---

## Design Decisions

- Used **Action Classes** for each specific action in the application to keep the code modular and maintainable.
- All **controllers are invokable**, aligning with Laravel's preferred design pattern for cleaner and more focused controllers.
- **API route files are separated** to ensure a more stable, consistent, and organized project structure.
- Used **Model Observers** to automatically create ledger entries for every successful transaction, ensuring data integrity and consistency.
- Utilized the **Service Container** to handle fee calculation logic. The **fee rates** are stored in a configuration file and **bound to the service** when initialized in the `AppServiceProvider`.
- Implemented **Custom Exceptions** for all non-validation error scenarios, providing clearer error handling and better debugging.
- Introduced a **Data Transfer Object (DTO) layer** between the request and action layers. For example, user creation after the register request is handled through a DTO for cleaner and more structured data flow.

---

## Used Packages

- **[infinitypaul/idempotency-laravel](https://github.com/infinitypaul/idempotency-laravel)** — Handles request idempotency to prevent duplicate operations.
- **[nunomaduro/mock-final-classes](https://github.com/nunomaduro/mock-final-classes)** — Allows mocking of `final` classes during testing.
  > Note: All non-extendable classes are marked as `final`, and a Pint rule is configured in `pint.json` to enforce this convention.
