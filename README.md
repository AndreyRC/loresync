# LoreSync

LoreSync is a SaaS platform for RPG game masters and players to organize campaigns, manage worldbuilding, track characters, and maintain session history.

This repository is the initial bootstrap: Laravel + authentication + a clean domain-oriented structure and the first core entities (Campaign, NPC, Location, Session, Map, MapPin).

## Tech Stack

- PHP 8.3+
- Laravel (latest stable at bootstrap time)
- MySQL (phpMyAdmin-friendly)
- Blade + Laravel Breeze (auth scaffolding)
- Alpine.js (lightweight UI interactivity)
- Tailwind CSS + Vite

## Local Setup

### Prerequisites

- PHP 8.3+ with `pdo_mysql` enabled
- Composer
- Node.js + npm
- MySQL running locally (e.g. via XAMPP/WAMP/Docker) + phpMyAdmin optional

### Install

1. Install PHP dependencies:

	```bash
	composer install
	```

2. Install frontend dependencies:

	```bash
	npm install
	npm run build
	```

3. Configure environment:

	```bash
	cp .env.example .env
	php artisan key:generate
	```

4. Configure MySQL in `.env`:

	```env
	DB_CONNECTION=mysql
	DB_HOST=127.0.0.1
	DB_PORT=3306
	DB_DATABASE=loresync
	DB_USERNAME=root
	DB_PASSWORD=
	```

	Create the `loresync` database in MySQL (phpMyAdmin is fine).

5. Run migrations:

	```bash
	php artisan migrate
	```

6. Start the app:

	```bash
	php artisan serve
	```

Then visit `http://localhost:8000`.

## What’s Included

- Laravel Breeze auth (Register/Login/Logout)
- Alpine.js initialized globally in `resources/js/app.js`
- Base domain entities + migrations:
  - User (Laravel default)
  - Campaign
  - NPC
  - Location
  - Session
  - Map
  - MapPin
- Minimal Blade UI:
  - Dashboard (Breeze)
  - Campaigns index
  - Create campaign

## Product Vision

LoreSync aims to become a collaborative “source of truth” for RPG campaigns:

- Campaign workspaces for GMs and players
- Worldbuilding tools for locations, factions, and lore
- Character and NPC tracking
- Session logs with searchable history
- Map management with pins and references

This bootstrap focuses on a clean, scalable foundation—no advanced business features yet.
