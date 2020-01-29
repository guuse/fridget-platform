# Fridget platform

The API platform of the fridget

## Project startup
Copy `.env` to `.env.local` and adjust `.env.local` to your needs.

```bash
docker-compose up
```

Make sure the frontend is build on first startup
```bash
docker-compose run --rm front-end npm run build
```

## Backend
Load fixtures:
```bash
docker-compose run app bin/console doctrine:fixtures:load
```

Edit/make entities:
```bash
docker-compose run app bin/console make:entity
```

Create migration:
```bash
docker-compose run app bin/console doctrine:migrate:diff
```

Run migrations:
```bash
docker-compose run app bin/console doctrine:migrate:migrate
```
## Frontend
Build frontend during development:
```bash
docker-compose run --rm node
```

Build frontend for production (done by docker image):
```bash
docker-compose run --rm node npm run build
```

## Code fixes
### Fix code style
```bash
docker-compose exec app composer fix
```

### Analyse code quality
```bash
docker-compose exec app composer analyse
```

### Test code
```bash
docker-compose exec app composer test
```
