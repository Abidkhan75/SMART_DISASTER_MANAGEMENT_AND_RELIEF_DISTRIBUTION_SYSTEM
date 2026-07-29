<?php
require_once("../admin/admin_auth.php");
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

$shelterID = trim($_POST["Shelter_ID"] ?? "");

$zoneID = trim($_POST["Zone_ID"] ?? "");

$capacity = trim($_POST["Capacity"] ?? "");

$currentOccupancy = trim($_POST["Current_Occupancy"] ?? "");

$address = trim($_POST["Address"] ?? "");

$contactNo = trim($_POST["Contact_No"] ?? "");

$status = trim($_POST["Status"] ?? "");

/* ==========================================
   VALIDATION
========================================== */

if(

empty($shelterID) ||
empty($zoneID) ||
empty($capacity) ||
empty($currentOccupancy) ||
empty($address) ||
empty($contactNo) ||
empty($status)

)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"All fields are required."

    ));

    exit();

}

/* ==========================================
   CHECK SHELTER EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Shelter_ID
 FROM shelter
 WHERE Shelter_ID=?"

);

$check->bind_param("s",$shelterID);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Shelter ID not found."

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
   VALIDATE NUMBERS
========================================== */

if(!is_numeric($capacity) || $capacity < 0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Capacity must be positive."

    ));

    exit();

}

if(!is_numeric($currentOccupancy) || $currentOccupancy < 0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Current Occupancy must be positive."

    ));

    exit();

}

if($currentOccupancy > $capacity)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Current Occupancy cannot exceed Capacity."

    ));

    exit();

}

/* ==========================================
   CONTACT VALIDATION
========================================== */

if(!preg_match('/^[0-9]{11}$/',$contactNo))
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Contact Number must contain exactly 11 digits."

    ));

    exit();

}

/* ==========================================
   VALID STATUS
========================================== */

$validStatus = array(

"Available",
"Full",
"Closed"

);

if(!in_array($status,$validStatus))
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Invalid Shelter Status."

    ));

    exit();

}

/* ==========================================
   UPDATE SHELTER
========================================== */

$stmt = $conn->prepare(

"UPDATE shelter

SET

Zone_ID=?,
Capacity=?,
Current_Occupancy=?,
Address=?,
Contact_No=?,
Status=?

WHERE Shelter_ID=?"

);

$stmt->bind_param(

"siissss",

$zoneID,
$capacity,
$currentOccupancy,
$address,
$contactNo,
$status,
$shelterID

);

if($stmt->execute())
{

    echo json_encode(array(

        "success"=>true,

        "message"=>"Shelter Updated Successfully."

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