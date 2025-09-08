## Travel Service

This repository contains a simple API for managing travel requests. This project was developed for job test application.

### Endpoints

Auth
- POST `/api/v1/auth/register` (public)
- POST `/api/v1/auth/login` (public)
- POST `/api/v1/auth/logout`
- GET `/api/v1/auth/me`

Travel Requests (JWT required)
- GET `/api/v1/travel-requests` — list current user’s requests with filters: `status`, `destination`, `departure_date`, `return_date`, `per_page` (returns `data` + `meta`)
- GET `/api/v1/travel-requests/{id}` — owner can view own; approver can view any
- POST `/api/v1/travel-requests` — create a new request (status coerced to `requested`)
- PATCH `/api/v1/travel-requests/{id}` — owner-only edit of non-status fields (requester_name, destination, departure_date, return_date)
- PATCH `/api/v1/travel-requests/{id}/status` — approver-only approve/cancel

Why two update endpoints?
- PATCH `/{id}`: owner-driven content changes (what/when/where), never status
- PATCH `/{id}/status`: privileged workflow state change (approve/cancel)

### Authorization model

- `is_approver` on `users` controls privilege:
  - Approvers: can view any request, and change status
  - Owners: can view and update non-status fields on their requests
- Centralized in `App\Policies\TravelRequestPolicy` (`view`, `updateOwner`, `updateStatus`)

### Run with Docker

Prereqs: Docker, Docker Compose

1) Configure env
```bash
cp .env.example .env
```
Ensure your `.env` matches the MySQL service:
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=travel
DB_USERNAME=travel
DB_PASSWORD=travel
```

Also set a strong JWT secret (≥ 256-bit). Example (64 hex characters):
```env
JWT_SECRET=0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef
```

If you see build warnings related to `WWWUSER`/`WWWGROUP`, export them before building:
```bash
export WWWUSER=1000 WWWGROUP=1000
docker compose up -d --build
```

2) Build and start
```bash
docker compose up -d --build
```

3) Migrate and seed
```bash
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
```

4) Run tests
```bash
docker compose exec app php artisan test
```

5) Base URL
- `http://localhost:8000`

### Example usage

Register and login to get JWT, then use `Authorization: Bearer <token>` for protected routes.

Create request
```bash
curl -X POST http://localhost:8000/api/v1/travel-requests \
  -H 'Authorization: Bearer <token>' -H 'Content-Type: application/json' \
  -d '{"order_id":"ORD12345678","requester_name":"John","destination":"Paris","departure_date":"2025-12-01","return_date":"2025-12-10"}'
```

Owner edit (non-status)
```bash
curl -X PATCH http://localhost:8000/api/v1/travel-requests/1 \
  -H 'Authorization: Bearer <token>' -H 'Content-Type: application/json' \
  -d '{"destination":"Rome"}'
```

Approver status update
```bash
curl -X PATCH http://localhost:8000/api/v1/travel-requests/1/status \
  -H 'Authorization: Bearer <approver-token>' -H 'Content-Type: application/json' \
  -d '{"status":"approved"}'
```

### Troubleshooting
- JWT key errors: ensure `JWT_SECRET` length is adequate
- DB connection: ensure compose MySQL is healthy and `.env` points to `mysql`

## Author

- [Claudio Emanuel](https://www.linkedin.com/in/claudio-emmanuel/) made with ❤️