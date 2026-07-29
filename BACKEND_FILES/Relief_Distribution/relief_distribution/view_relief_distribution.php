<?php

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

    "Dis_ID",
    "Victim_ID",
    "Zone_ID",
    "Volunteer_ID",
    "Organization_ID",
    "Resource_ID",
    "Quantity",
    "Dis_Date",
    "Dis_Status"

);

$sort = "Dis_ID";

if(isset($_GET["sort"]))
{
    if(in_array($_GET["sort"],$allowedSort))
    {
        $sort = $_GET["sort"];
    }
}


/* ==========================================
   SEARCH RELIEF DISTRIBUTION
========================================== */

if(isset($_GET["search"]) && !empty(trim($_GET["search"])))
{

    $search = "%" . trim($_GET["search"]) . "%";

    $stmt = $conn->prepare(

    "SELECT

    Dis_ID,
    Victim_ID,
    Zone_ID,
    Volunteer_ID,
    Organization_ID,
    Resource_ID,
    Quantity,
    Dis_Date,
    Dis_Status

    FROM relief_distribution

    WHERE

    Dis_ID LIKE ?
    OR Victim_ID LIKE ?
    OR Zone_ID LIKE ?
    OR Volunteer_ID LIKE ?
    OR Organization_ID LIKE ?
    OR Resource_ID LIKE ?
    OR Dis_Status LIKE ?

    ORDER BY $sort"

    );

    $stmt->bind_param(

    "sssssss",

    $search,
    $search,
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

    Dis_ID,
    Victim_ID,
    Zone_ID,
    Volunteer_ID,
    Organization_ID,
    Resource_ID,
    Quantity,
    Dis_Date,
    Dis_Status

    FROM relief_distribution

    ORDER BY $sort";

    $result = $conn->query($query);

}


/* ==========================================
   STORE DATA
========================================== */

$distribution = array();

while($row = $result->fetch_assoc())
{

    $distribution[] = $row;

}


/* ==========================================
   RETURN RESPONSE
========================================== */

echo json_encode(array(

    "success" => true,

    "total_records" => count($distribution),

    "data" => $distribution

), JSON_PRETTY_PRINT);


if(isset($stmt))
{
    $stmt->close();
}

$conn->close();

?>