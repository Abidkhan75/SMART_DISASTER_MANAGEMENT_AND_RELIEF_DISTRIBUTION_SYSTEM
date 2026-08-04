# ---------------------------------------------------------------------------
# Single source of truth describing every table: label, primary key, the ID
# prefix used for auto-generated IDs, which field to show when this table is
# referenced as a foreign key elsewhere, and the column list.
# ---------------------------------------------------------------------------

SCHEMA = {
    "ZONE": {
        "label": "Zones", "desc": "Administrative zones being monitored",
        "pk": "Zone_ID", "prefix": "Z", "display": "City",
        "columns": [
            {"key": "Zone_ID", "label": "ID", "pk": True},
            {"key": "City", "label": "City", "type": "text"},
            {"key": "District", "label": "District", "type": "text"},
            {"key": "Division", "label": "Division", "type": "text"},
            {"key": "Population", "label": "Population", "type": "number"},
            {"key": "Risk_Level", "label": "Risk", "type": "select", "options": ["Low", "Medium", "High"]},
        ],
    },
    "DISASTER": {
        "label": "Disasters", "desc": "Logged disaster events",
        "pk": "Disaster_ID", "prefix": "D", "display": "Disaster_Name",
        "columns": [
            {"key": "Disaster_ID", "label": "ID", "pk": True},
            {"key": "Disaster_Name", "label": "Name", "type": "text"},
            {"key": "Severity_Level", "label": "Severity", "type": "select", "options": ["Low", "Medium", "High"]},
            {"key": "Start_Time", "label": "Start", "type": "date"},
            {"key": "End_Time", "label": "End", "type": "date"},
            {"key": "Status", "label": "Status", "type": "select", "options": ["Ongoing", "Completed"]},
        ],
    },
    "SHELTER": {
        "label": "Shelters", "desc": "Shelter capacity and status",
        "pk": "Shelter_ID", "prefix": "S", "display": "Address",
        "columns": [
            {"key": "Shelter_ID", "label": "ID", "pk": True},
            {"key": "Zone_ID", "label": "Zone", "type": "fk", "ref": "ZONE"},
            {"key": "Capacity", "label": "Capacity", "type": "number"},
            {"key": "Current_Occupancy", "label": "Occupied", "type": "number"},
            {"key": "Address", "label": "Address", "type": "text"},
            {"key": "Contact_No", "label": "Contact", "type": "text"},
            {"key": "Status", "label": "Status", "type": "select", "options": ["Active", "Full", "Closed"]},
        ],
    },
    "ORGANIZATION": {
        "label": "Organizations", "desc": "Partner NGOs and agencies",
        "pk": "Organization_ID", "prefix": "O", "display": "Organization_Name",
        "columns": [
            {"key": "Organization_ID", "label": "ID", "pk": True},
            {"key": "Organization_Name", "label": "Name", "type": "text"},
            {"key": "Address", "label": "Address", "type": "text"},
            {"key": "Contact_No", "label": "Contact", "type": "text"},
            {"key": "Email", "label": "Email", "type": "text"},
        ],
    },
    "RESOURCE": {
        "label": "Resources", "desc": "Relief resource catalog",
        "pk": "Resource_ID", "prefix": "R", "display": "Resource_Name",
        "columns": [
            {"key": "Resource_ID", "label": "ID", "pk": True},
            {"key": "Resource_Name", "label": "Name", "type": "text"},
            {"key": "Category", "label": "Category", "type": "text"},
            {"key": "Unit", "label": "Unit", "type": "text"},
            {"key": "Unit_Cost", "label": "Unit Cost", "type": "number"},
        ],
    },
    "VICTIM": {
        "label": "Victims", "desc": "Registered affected individuals",
        "pk": "Victim_ID", "prefix": "V", "display": "Full_Name",
        "columns": [
            {"key": "Victim_ID", "label": "ID", "pk": True},
            {"key": "NID", "label": "NID", "type": "text"},
            {"key": "Shelter_ID", "label": "Shelter", "type": "fk", "ref": "SHELTER"},
            {"key": "Zone_ID", "label": "Zone", "type": "fk", "ref": "ZONE"},
            {"key": "Full_Name", "label": "Name", "type": "text"},
            {"key": "Age", "label": "Age", "type": "number"},
            {"key": "Gender", "label": "Gender", "type": "select", "options": ["Male", "Female", "Other"]},
            {"key": "Family_Size", "label": "Family", "type": "number"},
            {"key": "Medical_Status", "label": "Medical", "type": "select", "options": ["Stable", "Injured", "Critical"]},
            {"key": "Contact_No", "label": "Contact", "type": "text"},
        ],
    },
    "DISASTER_ZONE": {
        "label": "Disaster \u21c4 Zone", "desc": "Impact of each disaster per zone",
        "pk": "DisasterZone_ID", "prefix": "DZ", "display": None,
        "columns": [
            {"key": "DisasterZone_ID", "label": "ID", "pk": True},
            {"key": "Disaster_ID", "label": "Disaster", "type": "fk", "ref": "DISASTER"},
            {"key": "Zone_ID", "label": "Zone", "type": "fk", "ref": "ZONE"},
            {"key": "Affected_Population", "label": "Affected Pop.", "type": "number"},
            {"key": "Estimated_Budget", "label": "Budget", "type": "number"},
            {"key": "Relief_Status", "label": "Relief", "type": "select", "options": ["Ongoing", "Completed"]},
            {"key": "Damage_Level", "label": "Damage", "type": "select", "options": ["Low", "Medium", "High", "Severe"]},
        ],
    },
    "VOLUNTEER": {
        "label": "Volunteers", "desc": "Field volunteers by zone",
        "pk": "Volunteer_ID", "prefix": "VL", "display": "Full_Name",
        "columns": [
            {"key": "Volunteer_ID", "label": "ID", "pk": True},
            {"key": "Organization_ID", "label": "Org", "type": "fk", "ref": "ORGANIZATION"},
            {"key": "Zone_ID", "label": "Zone", "type": "fk", "ref": "ZONE"},
            {"key": "Full_Name", "label": "Name", "type": "text"},
            {"key": "Phone", "label": "Phone", "type": "text"},
            {"key": "Gender", "label": "Gender", "type": "select", "options": ["Male", "Female", "Other"]},
            {"key": "Skill", "label": "Skill", "type": "text"},
            {"key": "Availability", "label": "Status", "type": "select", "options": ["Available", "Busy"]},
        ],
    },
    "INVENTORY": {
        "label": "Inventory", "desc": "Resource stock by shelter/zone",
        "pk": "Inventory_ID", "prefix": "I", "display": None,
        "columns": [
            {"key": "Inventory_ID", "label": "ID", "pk": True},
            {"key": "Shelter_ID", "label": "Shelter", "type": "fk", "ref": "SHELTER"},
            {"key": "Resource_ID", "label": "Resource", "type": "fk", "ref": "RESOURCE"},
            {"key": "Organization_ID", "label": "Org", "type": "fk", "ref": "ORGANIZATION"},
            {"key": "Zone_ID", "label": "Zone", "type": "fk", "ref": "ZONE"},
            {"key": "Quantity", "label": "Qty", "type": "number"},
            {"key": "Last_Updated", "label": "Updated", "type": "date"},
        ],
    },
    "RELIEF_DISTRIBUTION": {
        "label": "Relief Distribution", "desc": "Dispatch log to victims",
        "pk": "Dis_ID", "prefix": "RD", "display": None,
        "columns": [
            {"key": "Dis_ID", "label": "ID", "pk": True},
            {"key": "Victim_ID", "label": "Victim", "type": "fk", "ref": "VICTIM"},
            {"key": "Zone_ID", "label": "Zone", "type": "fk", "ref": "ZONE"},
            {"key": "Volunteer_ID", "label": "Volunteer", "type": "fk", "ref": "VOLUNTEER"},
            {"key": "Organization_ID", "label": "Org", "type": "fk", "ref": "ORGANIZATION"},
            {"key": "Resource_ID", "label": "Resource", "type": "fk", "ref": "RESOURCE"},
            {"key": "Quantity", "label": "Qty", "type": "number"},
            {"key": "Dis_Date", "label": "Date", "type": "date"},
            {"key": "Dis_Status", "label": "Status", "type": "select", "options": ["Pending", "Delivered"]},
        ],
    },
}

NAV_GROUPS = {
    "Ground Data": ["ZONE", "DISASTER", "SHELTER", "VICTIM", "DISASTER_ZONE"],
    "Response Network": ["VOLUNTEER", "ORGANIZATION", "RESOURCE", "INVENTORY", "RELIEF_DISTRIBUTION"],
}

STATUS_FIELD_ORDER = [
    "Risk_Level", "Severity_Level", "Status", "Medical_Status",
    "Relief_Status", "Damage_Level", "Availability", "Dis_Status",
]


def find_status_field(columns):
    keys = [c["key"] for c in columns]
    for f in STATUS_FIELD_ORDER:
        if f in keys:
            return f
    return None


def status_color(val):
    if not val:
        return "b-muted"
    v = str(val).lower()
    if v in ("high", "critical", "full", "severe", "busy"):
        return "b-red"
    if v in ("medium", "pending", "ongoing", "injured"):
        return "b-amber"
    if v in ("low", "active", "available", "stable", "delivered", "completed"):
        return "b-green"
    return "b-muted"


def row_accent(val):
    c = status_color(val)
    return {"b-red": "var(--red)", "b-amber": "var(--amber)", "b-green": "var(--green)"}.get(c, "transparent")
