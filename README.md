# Legal Case Management System

A fresher portfolio project built with HTML, CSS, JavaScript, PHP, and MySQL.

## Features
- Dashboard with case totals
- Add, view, edit, and delete clients
- Add, view, edit, and delete cases
- Search cases by case number, title, client, or status
- Track case status and next hearing date
- Prepared SQL statements and server-side validation

## Requirements
- XAMPP (Apache + PHP + MySQL/MariaDB)
- Web browser

## Setup
1. Copy the `legal-case-management` folder into `C:\xampp\htdocs\`.
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin at `http://localhost/phpmyadmin`.
4. Import `database/schema.sql`.
5. If your MySQL settings differ from the defaults, edit `config/db.php`.
6. Open `http://localhost/legal-case-management/`.

## Demo note
This is a learning portfolio project, not a production-ready legal system. Before real use, add robust authentication/authorization, CSRF protection, audit logging, backups, and deployment security. Do not enter real client-confidential information.

## Suggested GitHub description
A responsive PHP and MySQL legal case management dashboard for organizing clients, tracking cases, monitoring status, and managing hearing dates.
