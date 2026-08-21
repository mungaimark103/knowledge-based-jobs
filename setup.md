KBSystem — Setup Guide (WSL)

Runs entirely in Docker via Laravel Sail. No local PHP, Composer, Node, or MySQL required.

Requirements:
- Docker Desktop on Windows (with WSL 2 integration enabled in Settings > Resources > WSL integration)
- Git (installed inside WSL)

Setup Steps (run all commands in your WSL terminal):

1. Navigate to WSL, then to your projects folder, clone the repo & copy environment file

cd projects
git clone https://github.com/mungaimark103/knowledge-based-jobs.git
cd knowledge-based-jobs
cp .env.example .env

2. Install PHP dependencies via Docker (Docker Desktop needs to be running before you run this command)
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/app" composer:latest composer install --ignore-platform-reqs

3. Start Sail containers
./vendor/bin/sail up -d

4. Setup database & build assets
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build

Access & Login:

Web Application: http://localhost
Super Admin: admin@job-sync.com | password
Candidate: client@job-sync.com | password
Employer: safaricom@employer.com | password

phpMyAdmin (Database GUI): http://localhost:8080
Username: sail
Password: password

Daily Workflow:

Terminal 1 — Start app in background:
./vendor/bin/sail up -d

Terminal 2 — Vite hot reload (for frontend edits):
./vendor/bin/sail npm run dev

Stop containers when done:
./vendor/bin/sail down

Common Issues:

- Docker command not found in WSL: Open Docker Desktop Settings > Resources > WSL integration and toggle ON your WSL distro (e.g. Ubuntu).
- Connection refused: Make sure Docker Desktop is open and running on Windows.
- Port 8080 in use: Change APP_PORT in .env, then run ./vendor/bin/sail down && ./vendor/bin/sail up -d