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

    "Organization_ID",
    "Organization_Name",
    "Email"

);

$sort = "Organization_ID";

if(isset($_GET["sort"]))
{
    if(in_array($_GET["sort"], $allowedSort))
    {
        $sort = $_GET["sort"];
    }
}


/* ==========================================
   SEARCH ORGANIZATION
========================================== */

if(isset($_GET["search"]) && !empty(trim($_GET["search"])))
{

    $search = "%" . trim($_GET["search"]) . "%";

    $stmt = $conn->prepare(

    "SELECT

        Organization_ID,
        Organization_Name,
        Address,
        Contact_No,
        Email

     FROM organization

     WHERE

        Organization_ID LIKE ?
        OR Organization_Name LIKE ?
        OR Email LIKE ?

     ORDER BY $sort"

    );

    $stmt->bind_param(

        "sss",

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

        Organization_ID,
        Organization_Name,
        Address,
        Contact_No,
        Email

     FROM organization

     ORDER BY $sort";

    $result = $conn->query($query);

}


/* ==========================================
   STORE DATA
========================================== */

$organizations = array();

while($row = $result->fetch_assoc())
{
    $organizations[] = $row;
}


/* ==========================================
   RETURN RESPONSE
========================================== */

echo json_encode(array(

    "success" => true,

    "total_records" => count($organizations),

    "data" => $organizations

), JSON_PRETTY_PRINT);


if(isset($stmt))
{
    $stmt->close();
}

$conn->close();

?>