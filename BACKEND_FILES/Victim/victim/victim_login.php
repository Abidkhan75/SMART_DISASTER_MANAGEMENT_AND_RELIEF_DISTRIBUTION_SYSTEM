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

$victimID = trim($_POST["Victim_ID"] ?? "");

$password = trim($_POST["Victim_Password"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(

empty($victimID) ||
empty($password)

)
{

    echo json_encode(array(

        "success" => false,
        "message" => "Victim ID and Password are required."

    ));

    exit();

}


/* ==========================================
   FIND VICTIM
========================================== */

$stmt = $conn->prepare(

"SELECT

Victim_ID,
Full_Name,
Zone_ID,
Shelter_ID,
Victim_Password

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
   VICTIM NOT FOUND
========================================== */

if($result->num_rows == 0)
{

    echo json_encode(array(

        "success" => false,
        "message" => "Invalid Victim ID or Password."

    ));

    $stmt->close();

    $conn->close();

    exit();

}


$row = $result->fetch_assoc();


/* ==========================================
   VERIFY PASSWORD
========================================== */

if(password_verify($password,$row["Victim_Password"]))
{

    echo json_encode(array(

        "success" => true,

        "message" => "Login Successful.",

        "Victim_ID" => $row["Victim_ID"],

        "Full_Name" => $row["Full_Name"],

        "Zone_ID" => $row["Zone_ID"],

        "Shelter_ID" => $row["Shelter_ID"]

    ));

}
else
{

    echo json_encode(array(

        "success" => false,

        "message" => "Invalid Victim ID or Password."

    ));

}


$stmt->close();

$conn->close();

?>