import mysql.connector
from mysql.connector import Error


ROLE_TABLES = {
    "admin":        ("admin", "Admin_ID", "Admin_Password", "Admin_Name"),
    "victim":       ("victim", "Victim_ID", "Victim_Password", "Full_Name"),
    "volunteer":    ("volunteer", "Volunteer_ID", "Volunteer_Password", "Full_Name"),
    "organization": ("organization", "Organization_ID", "Org_Password", "Organization_Name"),
}


def get_db_connection():
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="DisasterManagementDB",
            port=3306
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