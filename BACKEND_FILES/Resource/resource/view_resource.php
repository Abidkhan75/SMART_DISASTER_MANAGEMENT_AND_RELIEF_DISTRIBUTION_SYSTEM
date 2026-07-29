<?php

require_once("../organization/organization_auth.php");
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

    "Resource_ID",
    "Resource_Name",
    "Category",
    "Unit",
    "Unit_Cost"

);

$sort = "Resource_ID";

if(isset($_GET["sort"]))
{
    if(in_array($_GET["sort"],$allowedSort))
    {
        $sort = $_GET["sort"];
    }
}


/* ==========================================
   SEARCH RESOURCE
========================================== */

if(isset($_GET["search"]) && !empty(trim($_GET["search"])))
{

    $search = "%" . trim($_GET["search"]) . "%";

    $stmt = $conn->prepare(

    "SELECT

    Resource_ID,
    Resource_Name,
    Category,
    Unit,
    Unit_Cost

    FROM resource

    WHERE

    Resource_ID LIKE ?
    OR Resource_Name LIKE ?
    OR Category LIKE ?
    OR Unit LIKE ?

    ORDER BY $sort"

    );

    $stmt->bind_param(

    "ssss",

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

    Resource_ID,
    Resource_Name,
    Category,
    Unit,
    Unit_Cost

    FROM resource

    ORDER BY $sort";

    $result = $conn->query($query);

}


/* ==========================================
   STORE DATA
========================================== */

$resources = array();

while($row = $result->fetch_assoc())
{

    $resources[] = $row;

}


/* ==========================================
   RETURN RESPONSE
========================================== */

echo json_encode(array(

    "success" => true,

    "total_records" => count($resources),

    "data" => $resources

), JSON_PRETTY_PRINT);


if(isset($stmt))
{
    $stmt->close();
}

$conn->close();

?>