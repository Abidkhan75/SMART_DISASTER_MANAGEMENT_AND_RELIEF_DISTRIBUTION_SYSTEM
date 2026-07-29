<?php

require_once("../database.php");

header("Content-Type: application/json");

/* ==========================================
   ONLY ALLOW POST REQUEST
========================================== */

if($_SERVER["REQUEST_METHOD"] != "POST")
{
    http_response_code(405);

    echo json_encode(array(

        "success" => false,
        "message" => "Only POST requests are allowed."

    ));

    exit();
}


/* ==========================================
   RECEIVE LOGIN DATA
========================================== */

$volunteerID = trim($_POST["Volunteer_ID"] ?? "");

$password = trim($_POST["Volunteer_Password"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(

empty($volunteerID) ||
empty($password)

)
{

    echo json_encode(array(

        "success" => false,
        "message" => "Volunteer ID and Password are required."

    ));

    exit();

}


/* ==========================================
   FIND VOLUNTEER
========================================== */

$stmt = $conn->prepare(

"SELECT

Volunteer_ID,
Full_Name,
Organization_ID,
Zone_ID,
Volunteer_Password

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
   VOLUNTEER NOT FOUND
========================================== */

if($result->num_rows == 0)
{

    echo json_encode(array(

        "success" => false,
        "message" => "Invalid Volunteer ID or Password."

    ));

    $stmt->close();

    $conn->close();

    exit();

}


$row = $result->fetch_assoc();


/* ==========================================
   VERIFY PASSWORD
========================================== */

if(password_verify($password,$row["Volunteer_Password"]))
{

    echo json_encode(array(

        "success" => true,

        "message" => "Login Successful.",

        "Volunteer_ID" => $row["Volunteer_ID"],

        "Full_Name" => $row["Full_Name"],

        "Organization_ID" => $row["Organization_ID"],

        "Zone_ID" => $row["Zone_ID"]

    ));

}
else
{

    echo json_encode(array(

        "success" => false,

        "message" => "Invalid Volunteer ID or Password."

    ));

}


$stmt->close();

$conn->close();

?>