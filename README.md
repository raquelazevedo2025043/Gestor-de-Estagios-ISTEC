<h1 align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="public/images/logo-dark.png" />
    <img src="public/images/logo.png" alt="ISTEConnect - Portal de Estágios" width="500" />
  </picture>
</h1>

<p align="center">
  A web platform to manage internships at ISTEC: companies publish offers, students apply, and supervisors follow each internship from placement to final grade.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?logo=laravel&logoColor=white" alt="Laravel" />
  <img src="https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/Supabase-3FCF8E?logo=supabase&logoColor=white" alt="Supabase" />
  <img src="https://img.shields.io/badge/PostgreSQL-4169E1?logo=postgresql&logoColor=white" alt="PostgreSQL" />
  <img src="https://img.shields.io/badge/React-61DAFB?logo=react&logoColor=black" alt="React" />
  <img src="https://img.shields.io/badge/Tailwind%20CSS-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/Vite-646CFF?logo=vite&logoColor=white" alt="Vite" />
</p>

<p align="center">
  <img src="https://img.shields.io/badge/license-All%20Rights%20Reserved-red" alt="License" />
</p>

> Final project for the Software Development CTeSP at ISTEC Porto (academic year 2026/2027). Developed as a group project. Not an official ISTEC service (see [Disclaimer](#disclaimer)).

## Tech stack

| Area | Technology |
| --- | --- |
| Backend | [Laravel](https://laravel.com/) 13 (PHP `^8.3`) |
| Frontend | React, [Tailwind CSS](https://tailwindcss.com/) 4, [Vite](https://vite.dev/) |
| Database | PostgreSQL ([Supabase](https://supabase.com/)) |
| Authentication | Email and password (all users) or Google OAuth2 with the ISTEC institutional email (students, professors and coordinators) |
| Hosting | [Vercel](https://vercel.com/) |
| Testing | PHPUnit |
| Prototyping | Figma |

## Project structure

```
Gestor-de-Estagios-ISTEC/
├── app/              # models, controllers, policies
├── config/           # application configuration
├── database/
│   ├── factories/    # model factories
│   ├── migrations/   # database schema
│   └── seeders/      # sample data
├── public/           # web root
├── resources/        # frontend (views, JS, CSS)
├── routes/           # web and API routes
└── tests/            # automated tests
```

## Acknowledgements

- [Laravel](https://laravel.com/), [React](https://react.dev/), [Tailwind CSS](https://tailwindcss.com/) and the open-source packages listed in `composer.json` and `package.json`.

## License

The source code of this project was written for a course assignment; all rights reserved by the authors. The ISTEC name and logo belong to ISTEC and are not covered by this license.

## Disclaimer

ISTEConnect is an academic project made for educational purposes only. It is not an official ISTEC service and is not endorsed by ISTEC.
