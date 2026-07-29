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
   GENERATE NEXT DISTRIBUTION ID
========================================== */

$query = "SELECT Dis_ID
          FROM relief_distribution
          ORDER BY Dis_ID DESC
          LIMIT 1";

$result = $conn->query($query);

if($result && $result->num_rows > 0)
{

    $row = $result->fetch_assoc();

    $lastID = $row["Dis_ID"];

    $number = intval(substr($lastID,3));

    $number++;

    $nextID = "DIS".str_pad($number,3,"0",STR_PAD_LEFT);

}
else
{

    $nextID = "DIS001";

}


/* ==========================================
   RECEIVE DATA
========================================== */

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
   CHECK FOREIGN KEYS
========================================== */

$tables = array(

    "victim"=>"Victim_ID",
    "zone"=>"Zone_ID",
    "volunteer"=>"Volunteer_ID",
    "organization"=>"Organization_ID",
    "resource"=>"Resource_ID"

);

$values = array(

    $victimID,
    $zoneID,
    $volunteerID,
    $organizationID,
    $resourceID

);

$i = 0;

foreach($tables as $table=>$column)
{

    $stmt = $conn->prepare(

    "SELECT $column FROM $table WHERE $column=?"

    );

    $stmt->bind_param(

    "s",

    $values[$i]

    );

    $stmt->execute();

    $res = $stmt->get_result();

    if($res->num_rows==0)
    {

        echo json_encode(array(

            "success"=>false,
            "message"=>$column." does not exist."

        ));

        $stmt->close();

        $conn->close();

        exit();

    }

    $stmt->close();

    $i++;

}


/* ==========================================
   QUANTITY VALIDATION
========================================== */

if(!is_numeric($quantity) || $quantity<=0)
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
   INSERT RECORD
========================================== */

$stmt = $conn->prepare(

"INSERT INTO relief_distribution

(

Dis_ID,
Victim_ID,
Zone_ID,
Volunteer_ID,
Organization_ID,
Resource_ID,
Quantity,
Dis_Date,
Dis_Status

)

VALUES

(?,?,?,?,?,?,?,?,?)"

);

$stmt->bind_param(

"ssssssiss",

$nextID,
$victimID,
$zoneID,
$volunteerID,
$organizationID,
$resourceID,
$quantity,
$disDate,
$disStatus

);


/* ==========================================
   EXECUTE
========================================== */

if($stmt->execute())
{

    echo json_encode(array(

        "success"=>true,

        "message"=>"Relief Distribution Added Successfully.",

        "Dis_ID"=>$nextID

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