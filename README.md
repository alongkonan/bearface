# BearCheck (Prototype)

Simple PHP prototype for student attendance with face-scan verification.

Features
- SQLite backend
- Roles: admin, teacher, student
- Teacher: create session, enroll student by uploading a face (descriptor computed client-side using face-api.js)
- Student: check-in by scanning face with webcam; client computes descriptor and server verifies by distance

Quick start

1. Initialize database (creates `data/database.sqlite` and seeds accounts):

```bash
php init_db.php
```

2. Serve with PHP built-in server from repo root:

```bash
php -S 0.0.0.0:8000 -t .
```

3. Open http://localhost:8000/public/login.php

Seed users:
- admin / admin123 (role: admin)
- teacher / teacher123 (role: teacher)

Notes about face models
----------------------
This prototype expects face-api.js models to be available under `/models` path. You can download the models from the face-api.js repo and place them in a `models/` folder at the project root (next to `public/`). Alternatively, change `assets/face.js` to load models from a CDN or a hosted location.

Security
--------
This is a minimal prototype. Do NOT use in production without adding proper authentication, CSRF protection, input validation, rate limits, HTTPS, and secure storage for descriptors.

Next steps
- Add enrollment UI for teachers to view enrolled students
- Add session view to show attendance list
- Improve frontend UX and host face-api models for convenience
# bearcheck