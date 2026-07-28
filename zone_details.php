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
   CHECK ZONE ID
========================================== */

if(!isset($_GET["id"]))
{
    http_response_code(400);

    echo json_encode(array(

        "success" => false,
        "message" => "Zone ID is required."

    ));

    exit();
}

$zoneID = trim($_GET["id"]);

if(empty($zoneID))
{
    http_response_code(400);

    echo json_encode(array(

        "success" => false,
        "message" => "Invalid Zone ID."

    ));

    exit();
}

/* ==========================================
   RETRIEVE ZONE DETAILS
========================================== */

$stmt = $conn->prepare(

"SELECT

Zone_ID,
City,
District,
Division,
Population,
Risk_Level

FROM zone

WHERE Zone_ID = ?"

);

$stmt->bind_param(

"s",

$zoneID

);

$stmt->execute();

$result = $stmt->get_result();

/* ==========================================
   CHECK RECORD
========================================== */

if($result->num_rows == 0)
{
    http_response_code(404);

    echo json_encode(array(

        "success" => false,
        "message" => "Zone not found."

    ));

    $stmt->close();

    $conn->close();

    exit();
}

/* ==========================================
   RETURN DATA
========================================== */

$zone = $result->fetch_assoc();

echo json_encode(array(

    "success" => true,

    "data" => $zone

), JSON_PRETTY_PRINT);

$stmt->close();

$conn->close();

?>