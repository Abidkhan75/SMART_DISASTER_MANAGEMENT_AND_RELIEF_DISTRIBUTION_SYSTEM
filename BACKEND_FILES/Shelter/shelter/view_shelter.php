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

    "Shelter_ID",
    "Zone_ID",
    "Capacity",
    "Current_Occupancy",
    "Address",
    "Contact_No",
    "Status"

);

$sort = "Shelter_ID";

if(isset($_GET["sort"]))
{
    if(in_array($_GET["sort"], $allowedSort))
    {
        $sort = $_GET["sort"];
    }
}

/* ==========================================
   SEARCH
========================================== */

if(isset($_GET["search"]) && !empty(trim($_GET["search"])))
{

    $search = "%".trim($_GET["search"])."%";

    $stmt = $conn->prepare(

    "SELECT *

     FROM shelter

     WHERE Shelter_ID LIKE ?
        OR Zone_ID LIKE ?
        OR Address LIKE ?
        OR Status LIKE ?

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

    "SELECT *

     FROM shelter

     ORDER BY $sort";

    $result = $conn->query($query);

}

/* ==========================================
   STORE DATA
========================================== */

$shelters = array();

while($row = $result->fetch_assoc())
{
    $shelters[] = $row;
}

/* ==========================================
   RETURN RESPONSE
========================================== */

echo json_encode(array(

    "success" => true,

    "total_records" => count($shelters),

    "data" => $shelters

), JSON_PRETTY_PRINT);

if(isset($stmt))
{
    $stmt->close();
}

$conn->close();

?>