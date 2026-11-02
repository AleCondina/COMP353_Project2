# COMP 353 — Database Course Project

A PHP and MySQL course project with interfaces for managing club members, family members, personnel, locations, teams, team formations, payments, and reporting queries.

## Project status

This repository preserves an academic project. It originally connected to a university-hosted database provided for the course. The original course credentials are no longer valid, and that database is no longer accessible to the author.

The current code reads database connection settings from environment variables rather than embedding credentials in source files. Historical commits may contain obsolete course configuration.

## Repository contents

- Root PHP files: administration forms, CRUD operations, and reporting queries.
- [database/schema.sql](database/schema.sql): related schema draft imported from [MVC_database](https://github.com/AleCondina/MVC_database), source blob `26c680308c383b1a6a068b7735a72d6ade77aa69`.
- `db.php`: shared MySQL connection configured through environment variables.

CRUD means creating, reading, updating, and deleting database records. The reporting pages use SQL joins and filters to retrieve related information, such as team sessions and players for a location and date range.

## Related schema draft

The imported draft defines nine tables: Location, Personnel, ClubMember, FamilyMember, Hobby, Payment, WorksAt, HasHobby, and RelatedTo. Primary keys identify records, unique constraints restrict duplicates, and foreign keys link related tables. Junction tables represent relationships such as members and hobbies.

The import removes a trailing comma in the Payment definition and adds the whitespace required after SQL `--` comment markers. The original table and column design is otherwise preserved. The source repository is unchanged.

**This draft is incomplete for the PHP application.** For example, `teams.php` requires Team, TeamLocated, and InTeam; `Q10.php` also requires TeamFormation and FormationPlayers. These tables are absent from the draft. The ClubMember definition also lacks the `status` and `gender` columns used by `clubMember.php`. This list illustrates known differences; it is not a complete compatibility audit.

The WorksAt composite primary key permits multiple personnel/location/start-date combinations; it does not enforce the draft comment's one-location-per-person intent.

The draft contains table definitions only, with no seed records. No MySQL runtime import or application integration test has been performed for this consolidation.

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

A related schema draft is now included at `database/schema.sql`, but the final application schema and seed data are still unavailable. Running the full application requires completing and reconciling that draft with the SQL queries in the PHP files. Importing the draft or setting connection variables alone will not make the full application functional.

This is an academic code sample, not a production deployment.
