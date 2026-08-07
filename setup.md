# KBSystem — Setup Guide

This project runs entirely in Docker via Laravel Sail, so you don't need PHP, Composer, MySQL, or Node installed on your machine — just Docker.

## Requirements

- Docker Desktop ([download here](https://www.docker.com/products/docker-desktop/)) — open it once after installing so the daemon is running
- Git

## First-time setup

```bash
# 1. Clone the repo
git clone <REPO_URL>
cd kbsystem

# 2. Copy the environment file
cp .env.example .env

# 3. Install PHP dependencies (via a temporary Docker container, no local PHP needed)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd)":/var/www/html \
    -w /var/www/html \
    laravelsail/php85-composer:latest \
    composer install --ignore-platform-reqs

# 4. Start the containers
./vendor/bin/sail up -d

# 5. Generate the app key
./vendor/bin/sail artisan key:generate

# 6. Run migrations and seed sample data
./vendor/bin/sail artisan migrate --seed

# 7. Install and build frontend assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

Visit **http://localhost:8080** (see note on ports below).

## Daily development

Two things need to run alongside each other:

```bash
# Terminal 1 — PHP, MySQL (runs in background)
./vendor/bin/sail up -d

# Terminal 2 — Vite, for CSS/JS hot-reloading while you edit
./vendor/bin/sail npm run dev
```

Stop everything when done for the day:

```bash
./vendor/bin/sail down
```

## Useful commands

| Task | Command |
|---|---|
| Run artisan commands | `./vendor/bin/sail artisan <command>` |
| Run a migration | `./vendor/bin/sail artisan migrate` |
| Re-seed the database | `./vendor/bin/sail artisan db:seed` |
| Open a MySQL shell | `./vendor/bin/sail mysql` |
| Run tests | `./vendor/bin/sail artisan test` |
| View logs | `./vendor/bin/sail logs` |

Tip: add this alias to your shell config so you can type `sail` instead of `./vendor/bin/sail`:

```bash
echo "alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'" >> ~/.zshrc
source ~/.zshrc
```

## Ports

- App: **http://localhost:8080** (set via `APP_PORT` in `.env`)
- MySQL: `localhost:3306` (if you want to connect with a GUI tool like TablePlus)
- Vite dev server: `localhost:5173` (only used internally by the browser for hot-reload, don't visit directly)

## Troubleshooting

**"Connection refused" in the browser**
Run `docker ps` — if no containers are listed, run `./vendor/bin/sail up -d`. If Docker Desktop itself isn't open, open it first.

**"SQLSTATE... getaddrinfo for mysql failed"**
This means something tried to connect to the database outside of Sail's Docker network. Make sure you're running commands through `sail` (`sail artisan ...`, not plain `php artisan ...`), and that containers are actually running (`docker ps`).

**Port already in use**
If port 8080 or 3306 is taken by something else on your machine, change `APP_PORT` or `FORWARD_DB_PORT` in `.env`, then run `sail down && sail up -d`.

## Project structure notes

- **Auth:** Laravel's built-in authentication, no Teams/Jetstream multi-tenancy — access control is via a `role` column on `users` (`agency`, `employer`, `candidate`)
- **Frontend:** Vue via Inertia.js (server-driven routing, not Vue Router) + Tailwind CSS
- **Matching logic:** see `KNOWLEDGE_BASE_DESIGN.md` for how the rule-based matching engine is structured