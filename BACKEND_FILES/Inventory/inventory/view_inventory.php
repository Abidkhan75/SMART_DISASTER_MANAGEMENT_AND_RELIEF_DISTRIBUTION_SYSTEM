<?php
require_once("../admin/admin_auth.php");
require_once("../database.php");

header("Content-Type: application/json");

/* ==========================================
   ONLY ALLOW GET REQUEST
========================================== */

if($_SERVER["REQUEST_METHOD"] != "GET")
{
    http_response_code(405);

    echo json_encode(array(

        "success" => false,
        "message" => "Only GET requests are allowed."

    ));

    exit();
}


/* ==========================================
   ALLOWED SORT COLUMNS
========================================== */

$allowedSort = array(

    "Inventory_ID",
    "Shelter_ID",
    "Resource_ID",
    "Organization_ID",
    "Zone_ID",
    "Quantity",
    "Last_Updated"

);

$sort = "Inventory_ID";

if(isset($_GET["sort"]))
{
    if(in_array($_GET["sort"], $allowedSort))
    {
        $sort = $_GET["sort"];
    }
}


/* ==========================================
   SEARCH INVENTORY
========================================== */

if(isset($_GET["search"]) && !empty(trim($_GET["search"])))
{

    $search = "%" . trim($_GET["search"]) . "%";

    $stmt = $conn->prepare(

    "SELECT

    Inventory_ID,
    Shelter_ID,
    Resource_ID,
    Organization_ID,
    Zone_ID,
    Quantity,
    Last_Updated

    FROM inventory

    WHERE

    Inventory_ID LIKE ?
    OR Shelter_ID LIKE ?
    OR Resource_ID LIKE ?
    OR Organization_ID LIKE ?
    OR Zone_ID LIKE ?

    ORDER BY $sort"

    );

    $stmt->bind_param(

    "sssss",

    $search,
    $search,
    $search,
    $search,
    $search

    );

    $stmt->execute();

    $result = $stmt->get_result();

}
else
{

    $query =

    "SELECT

    Inventory_ID,
    Shelter_ID,
    Resource_ID,
    Organization_ID,
    Zone_ID,
    Quantity,
    Last_Updated

    FROM inventory

    ORDER BY $sort";

    $result = $conn->query($query);

}


/* ==========================================
   STORE DATA
========================================== */

$inventory = array();

while($row = $result->fetch_assoc())
{

    $inventory[] = $row;

}


/* ==========================================
   RETURN RESPONSE
========================================== */

echo json_encode(array(

    "success" => true,

    "total_records" => count($inventory),

    "data" => $inventory

), JSON_PRETTY_PRINT);


if(isset($stmt))
{
    $stmt->close();
}

$conn->close();

?>