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
   RECEIVE DATA
========================================== */

$disID = trim($_POST["Dis_ID"] ?? "");

$victimID = trim($_POST["Victim_ID"] ?? "");

$zoneID = trim($_POST["Zone_ID"] ?? "");

$volunteerID = trim($_POST["Volunteer_ID"] ?? "");

$organizationID = trim($_POST["Organization_ID"] ?? "");

$resourceID = trim($_POST["Resource_ID"] ?? "");

$quantity = trim($_POST["Quantity"] ?? "");

$disDate = trim($_POST["Dis_Date"] ?? "");

$disStatus = trim($_POST["Dis_Status"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(

empty($disID) ||
empty($victimID) ||
empty($zoneID) ||
empty($volunteerID) ||
empty($organizationID) ||
empty($resourceID) ||
empty($quantity) ||
empty($disDate) ||
empty($disStatus)

)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Please fill all required fields."

    ));

    exit();

}


/* ==========================================
   CHECK RECORD EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Dis_ID

FROM relief_distribution

WHERE Dis_ID=?"

);

$check->bind_param(

"s",

$disID

);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Relief Distribution record not found."

    ));

    $check->close();

    $conn->close();

    exit();

}

$check->close();


/* ==========================================
   CHECK VICTIM EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Victim_ID

FROM victim

WHERE Victim_ID=?"

);

$check->bind_param("s",$victimID);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Victim ID does not exist."

    ));

    $check->close();
    $conn->close();
    exit();

}

$check->close();


/* ==========================================
   CHECK ZONE EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Zone_ID

FROM zone

WHERE Zone_ID=?"

);

$check->bind_param("s",$zoneID);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Zone ID does not exist."

    ));

    $check->close();
    $conn->close();
    exit();

}

$check->close();


/* ==========================================
   CHECK VOLUNTEER EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Volunteer_ID

FROM volunteer

WHERE Volunteer_ID=?"

);

$check->bind_param("s",$volunteerID);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Volunteer ID does not exist."

    ));

    $check->close();
    $conn->close();
    exit();

}

$check->close();


/* ==========================================
   CHECK ORGANIZATION EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Organization_ID

FROM organization

WHERE Organization_ID=?"

);

$check->bind_param("s",$organizationID);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Organization ID does not exist."

    ));

    $check->close();
    $conn->close();
    exit();

}

$check->close();


/* ==========================================
   CHECK RESOURCE EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Resource_ID

FROM resource

WHERE Resource_ID=?"

);

$check->bind_param("s",$resourceID);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Resource ID does not exist."

    ));

    $check->close();
    $conn->close();
    exit();

}

$check->close();


/* ==========================================
   QUANTITY VALIDATION
========================================== */

if(!is_numeric($quantity) || $quantity <= 0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Quantity must be greater than 0."

    ));

    exit();

}


/* ==========================================
   DATE VALIDATION
========================================== */

$date = DateTime::createFromFormat(

'Y-m-d',

$disDate

);

if(!$date || $date->format('Y-m-d') != $disDate)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Invalid Distribution Date."

    ));

    exit();

}


/* ==========================================
   UPDATE RELIEF DISTRIBUTION
========================================== */

$stmt = $conn->prepare(

"UPDATE relief_distribution

SET

Victim_ID=?,
Zone_ID=?,
Volunteer_ID=?,
Organization_ID=?,
Resource_ID=?,
Quantity=?,
Dis_Date=?,
Dis_Status=?

WHERE Dis_ID=?"

);

$stmt->bind_param(

"sssssisss",

$victimID,
$zoneID,
$volunteerID,
$organizationID,
$resourceID,
$quantity,
$disDate,
$disStatus,
$disID

);


/* ==========================================
   EXECUTE UPDATE
========================================== */

if($stmt->execute())
{

    echo json_encode(array(

        "success"=>true,

        "message"=>"Relief Distribution Updated Successfully."

    ));

}
else
{

    echo json_encode(array(

        "success"=>false,

        "message"=>$stmt->error

    ));

}

$stmt->close();

$conn->close();

?>