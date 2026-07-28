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

        "success"=>false,
        "message"=>"Only POST requests are allowed."

    ));

    exit();
}


/* ==========================================
   GENERATE DISASTER_ZONE ID
========================================== */

$query = "SELECT DisasterZone_ID
          FROM disaster_zone
          ORDER BY DisasterZone_ID DESC
          LIMIT 1";


$result = $conn->query($query);


if($result && $result->num_rows > 0)
{

    $row = $result->fetch_assoc();

    $lastID = $row["DisasterZone_ID"];

    $number = intval(substr($lastID,2));

    $number++;

    $nextID = "DZ".str_pad($number,3,"0",STR_PAD_LEFT);

}
else
{

    $nextID = "DZ001";

}


/* ==========================================
   RECEIVE DATA
========================================== */


$disasterID = trim($_POST["Disaster_ID"] ?? "");

$zoneID = trim($_POST["Zone_ID"] ?? "");

$affectedPopulation = trim($_POST["Affected_Population"] ?? "");

$estimatedBudget = trim($_POST["Estimated_Budget"] ?? "");

$reliefStatus = trim($_POST["Relief_Status"] ?? "");

$damageLevel = trim($_POST["Damage_Level"] ?? "");



/* ==========================================
   VALIDATION
========================================== */


if(

empty($disasterID) ||
empty($zoneID) ||
empty($affectedPopulation) ||
empty($estimatedBudget) ||
empty($reliefStatus) ||
empty($damageLevel)

)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"All fields are required."

    ));

    exit();

}



/* ==========================================
   CHECK DISASTER FOREIGN KEY
========================================== */


$check = $conn->prepare(

"SELECT Disaster_ID
 FROM disaster
 WHERE Disaster_ID=?"

);


$check->bind_param("s",$disasterID);

$check->execute();


$result = $check->get_result();



if($result->num_rows==0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Disaster ID does not exist."

    ));

    exit();

}


$check->close();



/* ==========================================
   CHECK ZONE FOREIGN KEY
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

    exit();

}


$check->close();



/* ==========================================
   INSERT DATA
========================================== */


$stmt = $conn->prepare(

"INSERT INTO disaster_zone

(
DisasterZone_ID,
Disaster_ID,
Zone_ID,
Affected_Population,
Estimated_Budget,
Relief_Status,
Damage_Level
)

VALUES

(?,?,?,?,?,?,?)

"

);



$stmt->bind_param(

"sssisss",

$nextID,
$disasterID,
$zoneID,
$affectedPopulation,
$estimatedBudget,
$reliefStatus,
$damageLevel

);



if($stmt->execute())
{

    echo json_encode(array(

        "success"=>true,

        "message"=>"Disaster Zone Added Successfully.",

        "DisasterZone_ID"=>$nextID

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