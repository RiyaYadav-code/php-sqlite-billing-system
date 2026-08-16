# PHP + SQLite Product Billing System

Converted from the browser-only advanced billing system.

Features: multiple products, add/edit/delete products, customer details, bill numbers, ₹ currency, discount, custom GST, SQLite persistence, bill search, view/delete bills, daily/monthly reports, customer history and printing.

Local run:
1. Install PHP 8+ with PDO SQLite enabled.
2. In this folder run: `php -S localhost:8000`
3. Open http://localhost:8000

The database is automatically created at `data/billing.sqlite`.

Render deployment:
The project includes Dockerfile and render.yaml. A Render persistent disk is mounted at `/var/www/html/data` so the SQLite file can persist across service restarts. Push the project to GitHub and create a Render Web Service from the repository.
