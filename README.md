# Disaster Relief Command — Python (Flask + SQLite)

A working CRUD dashboard for the Disaster Management schema, built with
Flask and plain SQLite — no external database server needed, so it just runs.

## Run it in VS Code

1. Open this folder in VS Code (`File → Open Folder…`).
2. Open a terminal in VS Code (`` Ctrl+` ``) and create a virtual environment (recommended):
   ```
   python -m venv venv
   venv\Scripts\activate        # Windows
   source venv/bin/activate     # macOS/Linux
   ```
3. Install dependencies:
   ```
   pip install -r requirements.txt
   ```
4. Run the app:
   ```
   python app.py
   ```
5. Open the link shown in the terminal — normally **http://127.0.0.1:5000**

The first run automatically creates `disaster_management.db` (SQLite) from
`schema.sql`, seeded with the same sample data as the project's `.sql` file.
No MySQL/XAMPP needed for this version — it's fully self-contained.

## What's inside

- `app.py` — Flask routes: dashboard (`/`), generic table view/search
  (`/table/<name>`), add (`/table/<name>/add`), delete
  (`/table/<name>/delete/<id>`), and a `/reset` route to reseed sample data.
- `schema.py` — one config dict describing every table's columns, types and
  foreign keys — the templates and routes are driven entirely from this.
- `db.py` — tiny SQLite helper layer (no ORM, just `sqlite3`).
- `schema.sql` — table definitions + the same seed data as
  `DisasterManagementDatabase.sql`, adapted for SQLite.
- `templates/` — Jinja2 templates (`base.html`, `index.html`, `table.html`).
- `static/style.css` — the same dark "ops board" theme as the PHP version.

## Notes

- Foreign key dropdowns are populated live from the referenced table.
- IDs (Z006, V006, RD006, ...) auto-generate following the existing pattern.
- To switch this to MySQL later (e.g. to share the exact same database as
  the PHP/XAMPP version), swap `db.py`'s `sqlite3` calls for
  `mysql-connector-python` or `PyMySQL` — the rest of the app (`schema.py`,
  routes, templates) doesn't need to change.
- Visit `/reset` any time to wipe local edits and reload the original sample data.
