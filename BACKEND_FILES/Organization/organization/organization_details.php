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
   CHECK ORGANIZATION ID
========================================== */

if(!isset($_GET["id"]))
{
    http_response_code(400);

    echo json_encode(array(

        "success" => false,
        "message" => "Organization ID is required."

    ));

    exit();
}

$organizationID = trim($_GET["id"]);

if(empty($organizationID))
{
    http_response_code(400);

    echo json_encode(array(

        "success" => false,
        "message" => "Invalid Organization ID."

    ));

    exit();
}


/* ==========================================
   RETRIEVE ORGANIZATION DETAILS
========================================== */

$stmt = $conn->prepare(

"SELECT

Organization_ID,
Organization_Name,
Address,
Contact_No,
Email

FROM organization

WHERE Organization_ID = ?"

);

$stmt->bind_param(

"s",

$organizationID

);

$stmt->execute();

$result = $stmt->get_result();


/* ==========================================
   CHECK RECORD EXISTS
========================================== */

if($result->num_rows == 0)
{
    http_response_code(404);

    echo json_encode(array(

        "success" => false,
        "message" => "Organization not found."

    ));

    $stmt->close();

    $conn->close();

    exit();
}


/* ==========================================
   RETURN DATA
========================================== */

$organization = $result->fetch_assoc();

echo json_encode(array(

    "success" => true,

    "data" => $organization

), JSON_PRETTY_PRINT);


$stmt->close();

$conn->close();

?>