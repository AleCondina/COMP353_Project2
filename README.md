# COMP 353 — Database Course Project

A PHP and MySQL course project with interfaces for managing club members, family members, personnel, locations, teams, team formations, payments, and reporting queries.

## Project status

This repository preserves an academic project. It originally connected to a university-hosted database provided for the course. The original course credentials are no longer valid, and that database is no longer accessible to the author.

The current code reads database connection settings from environment variables rather than embedding credentials in source files. Historical commits may contain obsolete course configuration.

## Database configuration

Requirements: PHP with the `mysqli` extension, and a MySQL-compatible database.

Set the following variables in the environment of the PHP process:

| Variable | Purpose |
| --- | --- |
| `DB_HOST` | Database hostname |
| `DB_NAME` | Database name |
| `DB_USER` | Database username |
| `DB_PASSWORD` | Database password |

For a local development session in Bash or a compatible shell:

```bash
export DB_HOST=127.0.0.1
export DB_NAME=club_database
export DB_USER=club_app
read -r -s -p "Database password: " DB_PASSWORD
export DB_PASSWORD
echo
php -S 127.0.0.1:8000
```

Open http://127.0.0.1:8000/index.php after starting the development server.

For Apache or PHP-FPM, configure the variables for that service instead. A `.env` file is **not loaded automatically** by this project. Local `.env` files are ignored by Git; do not commit real credentials.

## Reproducing the original application

The original database schema and seed data are not included in this repository. Running the application requires obtaining or recreating a compatible schema by inspecting the SQL queries in the PHP files. Setting the connection variables alone will not recreate the database.

This is an academic code sample, not a production deployment.
