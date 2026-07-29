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
   GENERATE NEXT SHELTER ID
========================================== */

$query = "SELECT Shelter_ID
          FROM shelter
          ORDER BY Shelter_ID DESC
          LIMIT 1";

$result = $conn->query($query);

if($result && $result->num_rows > 0)
{
    $row = $result->fetch_assoc();

    $lastID = $row["Shelter_ID"];

    $number = intval(substr($lastID,1));

    $number++;

    $nextID = "S".str_pad($number,3,"0",STR_PAD_LEFT);
}
else
{
    $nextID = "S001";
}


/* ==========================================
   RECEIVE DATA
========================================== */

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
        "message"=>"Please fill all required fields."

    ));

    exit();

}


/* ==========================================
   CAPACITY VALIDATION
========================================== */

if(!is_numeric($capacity) || $capacity < 0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Capacity must be a positive number."

    ));

    exit();

}


if(!is_numeric($currentOccupancy) || $currentOccupancy < 0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Current Occupancy must be a positive number."

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
   STATUS VALIDATION
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
   CHECK FOREIGN KEY
========================================== */

$check = $conn->prepare(

"SELECT Zone_ID
 FROM zone
 WHERE Zone_ID=?"

);

$check->bind_param("s",$zoneID);

$check->execute();

$result = $check->get_result();

if($result->num_rows == 0)
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
   INSERT DATA
========================================== */

$stmt = $conn->prepare(

"INSERT INTO shelter

(
Shelter_ID,
Zone_ID,
Capacity,
Current_Occupancy,
Address,
Contact_No,
Status
)

VALUES

(?,?,?,?,?,?,?)"

);

$stmt->bind_param(

"ssiisss",

$nextID,
$zoneID,
$capacity,
$currentOccupancy,
$address,
$contactNo,
$status

);


if($stmt->execute())
{

    echo json_encode(array(

        "success"=>true,

        "message"=>"Shelter Added Successfully.",

        "Shelter_ID"=>$nextID

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