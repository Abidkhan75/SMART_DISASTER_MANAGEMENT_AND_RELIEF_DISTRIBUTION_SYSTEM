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

    "Zone_ID",
    "City",
    "District",
    "Division",
    "Population",
    "Risk_Level"

);

$sort = "Zone_ID";

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

     FROM zone

     WHERE Zone_ID LIKE ?
        OR City LIKE ?
        OR District LIKE ?
        OR Division LIKE ?

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

     FROM zone

     ORDER BY $sort";

    $result = $conn->query($query);

}

/* ==========================================
   STORE DATA
========================================== */

$zones = array();

while($row = $result->fetch_assoc())
{
    $zones[] = $row;
}

/* ==========================================
   RETURN RESPONSE
========================================== */

echo json_encode(array(

    "success" => true,

    "total_records" => count($zones),

    "data" => $zones

), JSON_PRETTY_PRINT);

if(isset($stmt))
{
    $stmt->close();
}

$conn->close();

?>