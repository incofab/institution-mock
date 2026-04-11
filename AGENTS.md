# Repository Guidelines

## Project Structure & Module Organization

- `app/`: Application code (controllers, models, services).
- `routes/`: Route definitions.
- `resources/`: Blade templates, JS/CSS source assets.
- `public/`: Web root and built assets.
- `database/`: Migrations, factories, seeders.
- `tests/`: `Feature/` and `Unit/` tests.
- `config/`, `bootstrap/`, `storage/`: Laravel configuration, bootstrapping,
  runtime files.

## Build, Test, and Development Commands

- `composer install`: Install PHP dependencies.
- `npm install`: Install JS tooling dependencies.
- `npm run dev`: Build assets for local development (Laravel Mix).
- `npm run watch`: Rebuild assets on change.
- `npm run prod`: Production asset build.
- `vendor/bin/phpunit`: Run PHPUnit test suites (Unit + Feature).
- `php artisan test`: Laravel test runner wrapper (if preferred).
- `docker-compose up`: Start services defined in `docker-compose.yml` (if using
  Docker).

## Coding Style & Naming Conventions

- PHP style: StyleCI with the Laravel preset (`.styleci.yml`). Avoid unused
  import cleanup is disabled.
- Formatting: Prettier with `@prettier/plugin-php`, 2-space indent, single
  quotes, 80-char print width (`.prettierrc`).
- Naming: Follow Laravel conventions (StudlyCase classes, camelCase methods,
  snake_case database fields).

## Testing Guidelines

- Framework: PHPUnit (configured in `phpunit.xml`), Pest plugin is present via
  Composer.
- Location: `tests/Unit` and `tests/Feature`, files ending with `Test.php`.
- Coverage: PHPUnit includes `app/` for coverage; prefer adding tests for new
  application logic.

## Commit & Pull Request Guidelines

- Commit history uses short, lowercase messages (e.g., `fix`, `fixes`,
  `fixed excel`). Keep commits concise and descriptive, preferably imperative.
- PRs should include: a summary of changes, testing notes (commands run), and
  screenshots for UI changes when applicable.

## Configuration & Security Notes

- Environment files: `.env`, `.env.testing`, `.env.example`. Do not commit
  secrets; update `.env.example` when adding new env vars.
- Autoload helpers are registered in `composer.json`
  (`bootstrap/my_helpers/general.php`, `app/helpers.php`).

## Important Note

- Before you perform any task, Study the style, theme, structure and convention
  followed in this project.
- Your implementation should follow this structure and coding style,
- Try to reuse what has already been defined, but when you need to create one,
  Make your code resuable and
- If you have any questions or need clarifications, ask the questions at once
  before you start. Responses will be provided to all your questions.
- Where possible, Make reasonable assumptions where necessary

## Application Documentation

- Start with [public/docs/index.html](public/docs/index.html) for a comprehensive overview of the application structure, route groups, feature areas, and runtime flow.
- Feature-specific docs are in [public/docs/admin.html](public/docs/admin.html), [public/docs/institution.html](public/docs/institution.html), [public/docs/ccd.html](public/docs/ccd.html), [public/docs/exams.html](public/docs/exams.html), [public/docs/api.html](public/docs/api.html), and [public/docs/data-model.html](public/docs/data-model.html).
- Keep these docs updated when changing routes, controllers, models, exam workflows, or major Blade pages so future AI agents can understand the project quickly.
