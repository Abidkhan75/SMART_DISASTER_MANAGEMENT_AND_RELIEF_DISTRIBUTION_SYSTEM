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

    "Volunteer_ID",
    "Organization_ID",
    "Zone_ID",
    "Full_Name",
    "Skill",
    "Availability"

);

$sort = "Volunteer_ID";

if(isset($_GET["sort"]))
{
    if(in_array($_GET["sort"],$allowedSort))
    {
        $sort = $_GET["sort"];
    }
}


/* ==========================================
   SEARCH VOLUNTEER
========================================== */

if(isset($_GET["search"]) && !empty(trim($_GET["search"])))
{

    $search = "%".trim($_GET["search"])."%";

    $stmt = $conn->prepare(

    "SELECT

    Volunteer_ID,
    Organization_ID,
    Zone_ID,
    Full_Name,
    Phone,
    Gender,
    Skill,
    Availability

    FROM volunteer

    WHERE

    Volunteer_ID LIKE ?
    OR Organization_ID LIKE ?
    OR Zone_ID LIKE ?
    OR Full_Name LIKE ?
    OR Skill LIKE ?
    OR Availability LIKE ?

    ORDER BY $sort"

    );

    $stmt->bind_param(

    "ssssss",

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

    Volunteer_ID,
    Organization_ID,
    Zone_ID,
    Full_Name,
    Phone,
    Gender,
    Skill,
    Availability

    FROM volunteer

    ORDER BY $sort";

    $result = $conn->query($query);

}


/* ==========================================
   STORE DATA
========================================== */

$volunteers = array();

while($row = $result->fetch_assoc())
{

    $volunteers[] = $row;

}


/* ==========================================
   RETURN RESPONSE
========================================== */

echo json_encode(array(

    "success" => true,

    "total_records" => count($volunteers),

    "data" => $volunteers

), JSON_PRETTY_PRINT);


if(isset($stmt))
{
    $stmt->close();
}

$conn->close();

?>