<?php

require_once("../database.php");

/* ==========================================
   ONLY ALLOW GET REQUEST
========================================== */

if($_SERVER["REQUEST_METHOD"] != "GET")
{
    die("Only GET requests are allowed.");
}

/* ==========================================
   SORTING
========================================== */

$allowedSort = array(

    "Disaster_ID",
    "Disaster_Name",
    "Severity_Level",
    "Start_Time",
    "End_Time",
    "Status"

);

$sort = "Disaster_ID";

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

if(isset($_GET["search"]))
{

    $search = trim($_GET["search"]);

    $stmt = $conn->prepare(

    "SELECT *
     FROM disaster
     WHERE Disaster_ID LIKE ?
        OR Disaster_Name LIKE ?
     ORDER BY $sort"

    );

    $keyword = "%".$search."%";

    $stmt->bind_param(

        "ss",

        $keyword,
        $keyword

    );

    $stmt->execute();

    $result = $stmt->get_result();

}
else
{

    $query =

    "SELECT *
     FROM disaster
     ORDER BY $sort";

    $result = $conn->query($query);

}

/* ==========================================
   STORE RESULTS
========================================== */

$disasters = array();

while($row = $result->fetch_assoc())
{
    $disasters[] = $row;
}

/* ==========================================
   RETURN JSON
========================================== */

header("Content-Type: application/json");

echo json_encode(

$disasters,

JSON_PRETTY_PRINT

);

if(isset($stmt))
{
    $stmt->close();
}

$conn->close();

?>