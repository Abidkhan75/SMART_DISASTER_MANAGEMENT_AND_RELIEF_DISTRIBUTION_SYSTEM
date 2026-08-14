import os
import secrets
import string
from flask import Flask, render_template, request, redirect, url_for, flash, session
from functools import wraps

import db
from schema import SCHEMA, NAV_GROUPS, find_status_field, status_color, row_accent


def generate_password(length=8):
    """Random alphanumeric password used when an admin adds a login-capable
    record (volunteer/victim/organization) without typing one in."""
    alphabet = string.ascii_letters + string.digits
    return "".join(secrets.choice(alphabet) for _ in range(length))

app = Flask(__name__)
app.secret_key = os.urandom(24)  # random each run -> forces re-login after restart


# Routes that are allowed WITHOUT being logged in.
PUBLIC_ENDPOINTS = {"login", "static"}


@app.before_request
def require_login():
    if request.endpoint in PUBLIC_ENDPOINTS or request.endpoint is None:
        return  # allow login page and static files (css/js/images) through
    if "role" not in session:
        return redirect(url_for("login"))


def login_required(f):
    """Kept for clarity on sensitive routes; before_request already covers this."""
    @wraps(f)
    def wrapper(*args, **kwargs):
        if "role" not in session:
            flash("Please log in to make changes.", "err")
            return redirect(url_for("login"))
        return f(*args, **kwargs)
    return wrapper


def display_for(table, record_id):
    """Human-friendly label for a foreign-key value, e.g. Zone_ID -> 'Dhaka (Z001)'."""
    if not record_id:
        return {"main": "\u2014", "sub": ""}
    cfg = SCHEMA[table]
    rec = db.fetch_one(table, cfg["pk"], record_id)
    if not rec:
        return {"main": record_id, "sub": ""}
    main = rec[cfg["display"]] if cfg["display"] else record_id
    return {"main": main or record_id, "sub": record_id}


# make helpers available inside every Jinja template
app.jinja_env.globals.update(
    status_color=status_color,
    row_accent=row_accent,
    display_for=display_for,
    SCHEMA=SCHEMA,
    NAV_GROUPS=NAV_GROUPS,
    table_count=lambda t: len(db.fetch_all(t)),
    ref_rows=lambda t: db.fetch_all(t),
)


@app.route("/")
def overview():
    zones = db.fetch_all("ZONE")
    disasters = db.fetch_all("DISASTER")
    shelters = db.fetch_all("SHELTER")
    victims = db.fetch_all("VICTIM")
    volunteers = db.fetch_all("VOLUNTEER")
    distributions = list(reversed(db.fetch_all("RELIEF_DISTRIBUTION")))

    ongoing_disasters = sum(1 for d in disasters if d["Status"] == "Ongoing")
    full_shelters = sum(1 for s in shelters if s["Status"] == "Full")
    avail_volunteers = sum(1 for v in volunteers if v["Availability"] == "Available")
    high_risk_zones = sum(1 for z in zones if z["Risk_Level"] == "High")

    zone_cards = []
    for z in zones:
        z_shelters = [s for s in shelters if s["Zone_ID"] == z["Zone_ID"]]
        cap = sum(s["Capacity"] for s in z_shelters)
        occ = sum(s["Current_Occupancy"] for s in z_shelters)
        pct = round((occ / cap) * 100) if cap else 0
        bar_color = "var(--red)" if pct >= 95 else ("var(--amber)" if pct >= 70 else "var(--green)")
        zone_cards.append({**z, "shelter_count": len(z_shelters), "cap": cap, "occ": occ, "pct": pct, "bar_color": bar_color})

    return render_template(
        "index.html",
        active_view="overview", page_title="Overview",
        page_desc="Live snapshot of all zones, shelters and relief activity",
        zones=zones, disasters=disasters, shelters=shelters, victims=victims,
        volunteers=volunteers, distributions=distributions,
        ongoing_disasters=ongoing_disasters, full_shelters=full_shelters,
        avail_volunteers=avail_volunteers, high_risk_zones=high_risk_zones,
        zone_cards=zone_cards,
    )


@app.route("/analytics")
def analytics():
    zones = db.fetch_all("ZONE")
    disasters = db.fetch_all("DISASTER")
    shelters = db.fetch_all("SHELTER")
    victims = db.fetch_all("VICTIM")
    volunteers = db.fetch_all("VOLUNTEER")
    resources = db.fetch_all("RESOURCE")
    distributions = db.fetch_all("RELIEF_DISTRIBUTION")

    def count_by(rows, field):
        counts = {}
        for r in rows:
            key = r.get(field) or "Unknown"
            counts[key] = counts.get(key, 0) + 1
        return counts

    # --- Pie: Zones by Risk Level ---
    risk_counts = count_by(zones, "Risk_Level")

    # --- Pie: Victims by Medical Status ---
    medical_counts = count_by(victims, "Medical_Status")

    # --- Donut: Volunteer Availability ---
    availability_counts = count_by(volunteers, "Availability")

    # --- Bar: Disaster Status ---
    disaster_status_counts = count_by(disasters, "Status")

    # --- Bar: Relief Distribution Status ---
    relief_status_counts = count_by(distributions, "Dis_Status")

    # --- Bar: Shelter Occupancy vs Capacity per zone ---
    zone_name = {z["Zone_ID"]: z["City"] for z in zones}
    shelter_labels, shelter_occ, shelter_cap = [], [], []
    for s in shelters:
        shelter_labels.append(f"{s['Shelter_ID']} ({zone_name.get(s['Zone_ID'], s['Zone_ID'])})")
        shelter_occ.append(s["Current_Occupancy"])
        shelter_cap.append(s["Capacity"])

    # --- Bar: Resource quantity distributed by category ---
    resource_category = {r["Resource_ID"]: r["Category"] for r in resources}
    category_totals = {}
    for d in distributions:
        cat = resource_category.get(d["Resource_ID"], "Other")
        category_totals[cat] = category_totals.get(cat, 0) + (d["Quantity"] or 0)

    chart_data = {
        "risk": {"labels": list(risk_counts.keys()), "values": list(risk_counts.values())},
        "medical": {"labels": list(medical_counts.keys()), "values": list(medical_counts.values())},
        "availability": {"labels": list(availability_counts.keys()), "values": list(availability_counts.values())},
        "disaster_status": {"labels": list(disaster_status_counts.keys()), "values": list(disaster_status_counts.values())},
        "relief_status": {"labels": list(relief_status_counts.keys()), "values": list(relief_status_counts.values())},
        "shelter_occupancy": {"labels": shelter_labels, "occupied": shelter_occ, "capacity": shelter_cap},
        "resource_categories": {"labels": list(category_totals.keys()), "values": list(category_totals.values())},
    }

    return render_template(
        "analytics.html",
        active_view="analytics", page_title="Analytics",
        page_desc="Charts and trends across zones, shelters and relief activity",
        chart_data=chart_data,
        totals={
            "zones": len(zones), "disasters": len(disasters), "shelters": len(shelters),
            "victims": len(victims), "volunteers": len(volunteers), "distributions": len(distributions),
        },
    )


@app.route("/login", methods=["GET", "POST"])
def login():
    if request.method == "POST":
        role = request.form.get("role")
        user_id = request.form.get("user_id", "").strip()
        password = request.form.get("password", "")
        user = db.verify_login(role, user_id, password)
        if user:
            session["role"] = role
            session["user_id"] = user["id"]
            session["display_name"] = user["name"]
            flash(f"Logged in as {user['name']} ({role.title()}).", "ok")
            return redirect(url_for("overview"))
        flash("Invalid ID or password.", "err")
        return redirect(url_for("login"))

    return render_template(
        "login.html", active_view="login",
        page_title="Login", page_desc="Sign in to access the system"
    )


@app.route("/logout")
def logout():
    session.clear()
    flash("Logged out.", "ok")
    return redirect(url_for("login"))


@app.route("/table/<table>")
def table_view(table):
    if table not in SCHEMA:
        return "Unknown table", 404
    cfg = SCHEMA[table]
    q = request.args.get("q", "").strip().lower()

    rows = db.fetch_all(table)
    if q:
        def matches(r):
            for c in cfg["columns"]:
                v = r.get(c["key"], "")
                if c.get("type") == "fk":
                    d = display_for(c["ref"], v)
                    v = f"{d['main']} {v}"
                if q in str(v).lower():
                    return True
            return False
        rows = [r for r in rows if matches(r)]

    status_field = find_status_field(cfg["columns"])

    return render_template(
        "table.html",
        active_view=table, page_title=cfg["label"], page_desc=cfg["desc"],
        table=table, cfg=cfg, rows=rows, q=q,
        status_field=status_field, total_count=len(db.fetch_all(table)),
        next_id=db.next_id(table, cfg["pk"], cfg["prefix"]),
    )


@app.route("/table/<table>/add", methods=["POST"])
@login_required
def add_record(table):
    if table not in SCHEMA:
        return "Unknown table", 404
    cfg = SCHEMA[table]

    new_id = db.next_id(table, cfg["pk"], cfg["prefix"])
    columns = [cfg["pk"]]
    values = [new_id]
    generated_password = None

    for c in cfg["columns"]:
        if c.get("pk"):
            continue
        val = request.form.get(c["key"], "")
        if c.get("type") == "number":
            val = int(val) if str(val).strip() != "" else 0
        elif c.get("type") == "password" and str(val).strip() == "":
            # Safety net only: if the admin leaves the password field blank,
            # generate one rather than inserting an empty/NOT-NULL-violating
            # value. Normally the admin types the password themselves.
            val = generate_password()
            generated_password = val
        columns.append(c["key"])
        values.append(val)

    try:
        db.insert_record(table, columns, values)
        if generated_password:
            flash(
                f"Record {new_id} added successfully. "
                f"Auto-generated password: {generated_password} "
                f"(share this with them — it won't be shown again).",
                "ok",
            )
        else:
            flash(f"Record {new_id} added successfully.", "ok")
    except Exception as e:
        flash(f"Could not add record: {e}", "err")

    return redirect(url_for("table_view", table=table))


@app.route("/table/<table>/edit/<record_id>", methods=["POST"])
@login_required
def edit_record(table, record_id):
    if table not in SCHEMA:
        return "Unknown table", 404
    cfg = SCHEMA[table]

    columns = []
    values = []
    for c in cfg["columns"]:
        if c.get("pk"):
            continue
        val = request.form.get(c["key"], "")
        if c.get("type") == "number":
            val = int(val) if str(val).strip() != "" else 0
        elif c.get("type") == "password" and str(val).strip() == "":
            # Leave the existing password untouched if the edit form
            # doesn't supply a new one.
            continue
        columns.append(c["key"])
        values.append(val)

    try:
        db.update_record(table, cfg["pk"], record_id, columns, values)
        flash(f"Record {record_id} updated successfully.", "ok")
    except Exception as e:
        flash(f"Could not update record: {e}", "err")

    return redirect(url_for("table_view", table=table))


@app.route("/table/<table>/delete/<record_id>")
@login_required
def delete_record(table, record_id):
    if table not in SCHEMA:
        return "Unknown table", 404
    cfg = SCHEMA[table]
    try:
        db.delete_record(table, cfg["pk"], record_id)
        flash(f"Record {record_id} deleted.", "ok")
    except Exception:
        flash("Could not delete — other records still reference it.", "err")
    return redirect(url_for("table_view", table=table))


@app.route("/reset", methods=["POST"])
@login_required
def reset():
    """Admin-only: drop and rebuild the MySQL database from schema.sql,
    wiping all local edits and restoring the original seed data."""
    if session.get("role") != "admin":
        flash("Only admins can reset the database.", "err")
        return redirect(url_for("overview"))

    ok, error = db.reset_database()
    if ok:
        session.clear()
        flash("Database reset — all local changes wiped and schema.sql reloaded. Please log in again.", "ok")
        return redirect(url_for("login"))
    else:
        flash(f"Reset failed: {error}", "err")
        return redirect(url_for("overview"))

if __name__ == "__main__":
    app.run(debug=True)
