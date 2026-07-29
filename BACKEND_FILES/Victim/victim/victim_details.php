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
   CHECK VICTIM ID
========================================== */

if(!isset($_GET["id"]))
{

    http_response_code(400);

    echo json_encode(array(

        "success" => false,
        "message" => "Victim ID is required."

    ));

    exit();

}

$victimID = trim($_GET["id"]);

if(empty($victimID))
{

    http_response_code(400);

    echo json_encode(array(

        "success" => false,
        "message" => "Invalid Victim ID."

    ));

    exit();

}


/* ==========================================
   RETRIEVE VICTIM DETAILS
========================================== */

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

WHERE Victim_ID=?"

);

$stmt->bind_param(

"s",

$victimID

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
        "message" => "Victim not found."

    ));

    $stmt->close();

    $conn->close();

    exit();

}


/* ==========================================
   RETURN DATA
========================================== */

$victim = $result->fetch_assoc();

echo json_encode(array(

    "success" => true,

    "data" => $victim

), JSON_PRETTY_PRINT);


$stmt->close();

$conn->close();

?>