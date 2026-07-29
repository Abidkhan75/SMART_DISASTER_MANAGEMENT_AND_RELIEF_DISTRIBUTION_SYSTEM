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

    "Victim_ID",
    "NID",
    "Shelter_ID",
    "Zone_ID",
    "Full_Name",
    "Age",
    "Family_Size"

);

$sort = "Victim_ID";

if(isset($_GET["sort"]))
{
    if(in_array($_GET["sort"], $allowedSort))
    {
        $sort = $_GET["sort"];
    }
}


/* ==========================================
   SEARCH VICTIM
========================================== */

if(isset($_GET["search"]) && !empty(trim($_GET["search"])))
{

    $search = "%" . trim($_GET["search"]) . "%";

    $stmt = $conn->prepare(

    "SELECT

    Victim_ID,
    NID,
    Shelter_ID,
    Zone_ID,
    Full_Name,
    Age,
    Gender,
    Family_Size,
    Medical_Status,
    Contact_No

    FROM victim

    WHERE

    Victim_ID LIKE ?
    OR NID LIKE ?
    OR Shelter_ID LIKE ?
    OR Zone_ID LIKE ?
    OR Full_Name LIKE ?
    OR Medical_Status LIKE ?

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

    Victim_ID,
    NID,
    Shelter_ID,
    Zone_ID,
    Full_Name,
    Age,
    Gender,
    Family_Size,
    Medical_Status,
    Contact_No

    FROM victim

    ORDER BY $sort";

    $result = $conn->query($query);

}


/* ==========================================
   STORE DATA
========================================== */

$victims = array();

while($row = $result->fetch_assoc())
{

    $victims[] = $row;

}


/* ==========================================
   RETURN RESPONSE
========================================== */

echo json_encode(array(

    "success" => true,

    "total_records" => count($victims),

    "data" => $victims

), JSON_PRETTY_PRINT);


if(isset($stmt))
{
    $stmt->close();
}

$conn->close();

?>