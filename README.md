<h1 align="center">Gestor de Estágios ISTEC</h1>

<p align="center">
  A web platform to manage internships at ISTEC: companies publish offers, students apply, and supervisors follow each internship from placement to final grade.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?logo=laravel&logoColor=white" alt="Laravel" />
  <img src="https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white" alt="PHP" />
    <img src="https://img.shields.io/badge/SQL%20Server-CC2927?logo=microsoftsqlserver&logoColor=white" alt="SQL Server" />
  <img src="https://img.shields.io/badge/React-61DAFB?logo=react&logoColor=black" alt="React" />
  <img src="https://img.shields.io/badge/Tailwind%20CSS-06B6D4?logo=tailwindcss&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/Vite-646CFF?logo=vite&logoColor=white" alt="Vite" />
</p>

<p align="center">
  <img src="https://img.shields.io/badge/license-All%20Rights%20Reserved-red" alt="License" />
</p>

> Final project for the Software Development CTeSP at ISTEC Porto (academic year 2025/2026). Developed as a group project. Not an official ISTEC service (see [Disclaimer](#disclaimer)).

## Tech stack

| Area | Technology |
| --- | --- |
| Backend | [Laravel](https://laravel.com/) 13 (PHP `^8.3`) |
| Frontend | React, [Tailwind CSS](https://tailwindcss.com/) 4, [Vite](https://vite.dev/) |
| Database | Microsoft SQL Server |
| Authentication | OAuth2 with the ISTEC institutional email |
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

Gestor de Estágios ISTEC is an academic project made for educational purposes only. It is not an official ISTEC service and is not endorsed by ISTEC.
