import os
import subprocess

import mysql.connector
from mysql.connector import Error


MYSQL_HOST = "localhost"
MYSQL_PORT = 3306
MYSQL_USER = "root"
MYSQL_PASSWORD = ""
MYSQL_DB = "DisasterManagementDB"

# Path to the mysql command-line client, used only by reset_database() below
# (needed because schema.sql's triggers use DELIMITER blocks that the Python
# connector can't execute). If "mysql" isn't on your PATH, set the MYSQL_CLI
# environment variable to the full path, e.g. on Windows/XAMPP:
#   C:\xampp\mysql\bin\mysql.exe
MYSQL_CLI = os.environ.get("MYSQL_CLI", "mysql")

SCHEMA_SQL_PATH = os.path.join(os.path.dirname(os.path.abspath(__file__)), "schema.sql")

ROLE_TABLES = {
    "admin":        ("admin", "Admin_ID", "Admin_Password", "Admin_Name"),
    "victim":       ("victim", "Victim_ID", "Victim_Password", "Full_Name"),
    "volunteer":    ("volunteer", "Volunteer_ID", "Volunteer_Password", "Full_Name"),
    "organization": ("organization", "Organization_ID", "Org_Password", "Organization_Name"),
}


def get_db_connection():
    try:
        conn = mysql.connector.connect(
            host=MYSQL_HOST,
            user=MYSQL_USER,
            password=MYSQL_PASSWORD,
            database=MYSQL_DB,
            port=MYSQL_PORT
        )
        return conn
    except Error as e:
        print(f"Database connection failed: {e}")
        return None


def fetch_all(table, order_by=None):
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    sql = f"SELECT * FROM {table}"
    if order_by:
        sql += f" ORDER BY {order_by}"
    cursor.execute(sql)
    rows = cursor.fetchall()
    cursor.close()
    conn.close()
    return rows


def fetch_one(table, pk_col, value):
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    cursor.execute(f"SELECT * FROM {table} WHERE {pk_col} = %s", (value,))
    row = cursor.fetchone()
    cursor.close()
    conn.close()
    return row


def next_id(table, pk_col, prefix):
    rows = fetch_all(table)
    max_n = 0
    for r in rows:
        raw = str(r[pk_col]).replace(prefix, "")
        if raw.isdigit():
            max_n = max(max_n, int(raw))
    return f"{prefix}{max_n + 1:03d}"


def insert_record(table, columns, values):
    conn = get_db_connection()
    cursor = conn.cursor()
    placeholders = ",".join(["%s"] * len(columns))
    col_list = ",".join(columns)
    cursor.execute(f"INSERT INTO {table} ({col_list}) VALUES ({placeholders})", values)
    conn.commit()
    cursor.close()
    conn.close()


def delete_record(table, pk_col, value):
    conn = get_db_connection()
    cursor = conn.cursor()
    cursor.execute(f"DELETE FROM {table} WHERE {pk_col} = %s", (value,))
    conn.commit()
    cursor.close()
    conn.close()


def update_record(table, pk_col, record_id, columns, values):
    conn = get_db_connection()
    cursor = conn.cursor()
    set_clause = ",".join([f"{c} = %s" for c in columns])
    cursor.execute(
        f"UPDATE {table} SET {set_clause} WHERE {pk_col} = %s",
        values + [record_id]
    )
    conn.commit()
    cursor.close()
    conn.close()


def verify_login(role, user_id, password):
    if role not in ROLE_TABLES:
        return None
    table, pk_col, pw_col, name_col = ROLE_TABLES[role]
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)
    cursor.execute(f"SELECT * FROM {table} WHERE {pk_col} = %s", (user_id,))
    row = cursor.fetchone()
    cursor.close()
    conn.close()
    if row and str(row.get(pw_col)) == password:
        return {"id": user_id, "name": row.get(name_col) or user_id}
    return None


def reset_database():
    """Drop and rebuild MYSQL_DB from schema.sql (tables, seed data, triggers).

    Returns (True, None) on success, or (False, error_message) on failure.
    """
    # 1) Drop and recreate an empty database (fast, no CLI needed for this part).
    try:
        conn = mysql.connector.connect(
            host=MYSQL_HOST, user=MYSQL_USER, password=MYSQL_PASSWORD, port=MYSQL_PORT
        )
        cursor = conn.cursor()
        cursor.execute(f"DROP DATABASE IF EXISTS {MYSQL_DB}")
        cursor.execute(f"CREATE DATABASE {MYSQL_DB}")
        cursor.close()
        conn.close()
    except Error as e:
        return False, f"Could not drop/recreate database: {e}"

    # 2) Re-import schema.sql via the mysql CLI, since it (unlike the Python
    #    connector) understands the DELIMITER blocks used by the triggers.
    if not os.path.exists(SCHEMA_SQL_PATH):
        return False, f"schema.sql not found at {SCHEMA_SQL_PATH}"

    cmd = [MYSQL_CLI, "-h", MYSQL_HOST, "-P", str(MYSQL_PORT), "-u", MYSQL_USER]
    if MYSQL_PASSWORD:
        cmd.append(f"-p{MYSQL_PASSWORD}")
    cmd.append(MYSQL_DB)

    try:
        with open(SCHEMA_SQL_PATH, "rb") as f:
            result = subprocess.run(cmd, stdin=f, capture_output=True, timeout=60)
        if result.returncode != 0:
            return False, result.stderr.decode(errors="replace")
    except FileNotFoundError:
        return False, (
            f"Could not find the '{MYSQL_CLI}' command-line client. "
            "Set the MYSQL_CLI environment variable to its full path "
            "(e.g. C:\\xampp\\mysql\\bin\\mysql.exe on Windows)."
        )
    except Exception as e:
        return False, str(e)

    return True, None
