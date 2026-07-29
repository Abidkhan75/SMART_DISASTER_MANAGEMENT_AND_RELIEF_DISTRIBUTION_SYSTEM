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
   CHECK VOLUNTEER ID
========================================== */

if(!isset($_GET["id"]))
{
    http_response_code(400);

    echo json_encode(array(

        "success" => false,
        "message" => "Volunteer ID is required."

    ));

    exit();
}

$volunteerID = trim($_GET["id"]);

if(empty($volunteerID))
{
    http_response_code(400);

    echo json_encode(array(

        "success" => false,
        "message" => "Invalid Volunteer ID."

    ));

    exit();
}


/* ==========================================
   RETRIEVE VOLUNTEER DETAILS
========================================== */

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

WHERE Volunteer_ID=?"

);

$stmt->bind_param(

"s",

$volunteerID

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
        "message" => "Volunteer not found."

    ));

    $stmt->close();

    $conn->close();

    exit();

}


/* ==========================================
   RETURN DATA
========================================== */

$volunteer = $result->fetch_assoc();

echo json_encode(array(

    "success" => true,

    "data" => $volunteer

), JSON_PRETTY_PRINT);


$stmt->close();

$conn->close();

?>