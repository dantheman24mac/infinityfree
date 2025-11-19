# Repository Guidelines

## Project Structure & Module Organization
- `index.php` boots the application; route handlers live in `admin/` for dashboard pages and `src/views/` for storefront templates.
- Domain logic is grouped under `src/`: authentication helpers in `Auth/`, reusable utilities in `helpers.php`, repositories for data access in `Repositories/`, and customer flows in `Services/`.
- Static assets reside in `assets/css`, `assets/js`, and `assets/img`.
- Database connection settings live in `config/database.php`. Schema and seed data are stored in `database/schema.sql` and `database/seed.sql`.
- Composer-managed dependencies are locked in `vendor/`; do not edit generated code directly.

## Build, Test, and Development Commands
- `composer install` — install PHP dependencies and refresh the autoloader before the first run or after dependency changes.
- `php -S 127.0.0.1:8000 -t .` — lightweight dev server pointed at the project root; mirror the production InfinityFree structure.
- `composer dump-autoload` — regenerate autoload metadata when adding classes under `src/`.
- `vendor/bin/phpunit` — executes the test suite once tests are added; use `--coverage-text` locally when validating larger changes.

## Coding Style & Naming Conventions
- Follow PSR-12: four-space indentation, brace-on-new-line for classes and methods, and snake_case for function parameters.
- PHP classes in `src/` use StudlyCase (`ProductRepository`) and one class per file. Service functions in `helpers.php` stay camelCase.
- View templates under `src/views` use kebab-case filenames (`order-confirmation.php`) and keep presentation logic light.

## Testing Guidelines
- Add PHPUnit feature tests in `tests/Feature` and unit tests in `tests/Unit`; name files `*Test.php`.
- Prepare database fixtures with dedicated seed classes or reuse snippets from `database/seed.sql`.
- Run `vendor/bin/phpunit --colors=always` before opening a PR; target meaningful coverage around repositories and services that touch the database.

## Commit & Pull Request Guidelines
- Craft commit subjects in the imperative mood and keep them under 72 characters (e.g., `feat: add eco-point redemption service`).
- Reference related issues in the body, summarizing scope, validation steps, and any database migrations executed.
- Pull requests must include a concise summary, screenshots/GIFs for UI updates, and notes about configuration or seeding changes.

## Configuration & Security Tips
- Copy `.env.example` to `.env` for local secrets; never commit `.env` or production credentials.
- Regenerate database passwords and API keys when sharing access; document new keys in the deployment channel rather than source control.
