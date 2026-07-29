<?php
require_once("../admin/admin_auth.php");
require_once("../organization/organization_auth.php");
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
   GENERATE NEXT INVENTORY ID
========================================== */

$query = "SELECT Inventory_ID
          FROM inventory
          ORDER BY Inventory_ID DESC
          LIMIT 1";

$result = $conn->query($query);

if($result && $result->num_rows > 0)
{

    $row = $result->fetch_assoc();

    $lastID = $row["Inventory_ID"];

    $number = intval(substr($lastID,1));

    $number++;

    $nextID = "I".str_pad($number,3,"0",STR_PAD_LEFT);

}
else
{

    $nextID = "I001";

}


/* ==========================================
   RECEIVE DATA
========================================== */

$shelterID = trim($_POST["Shelter_ID"] ?? "");

$resourceID = trim($_POST["Resource_ID"] ?? "");

$organizationID = trim($_POST["Organization_ID"] ?? "");

$zoneID = trim($_POST["Zone_ID"] ?? "");

$quantity = trim($_POST["Quantity"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(

empty($shelterID) ||
empty($resourceID) ||
empty($organizationID) ||
empty($zoneID) ||
empty($quantity)

)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Please fill all required fields."

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
        "message"=>"Shelter ID does not exist."

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
   INSERT INVENTORY
========================================== */

$stmt = $conn->prepare(

"INSERT INTO inventory

(

Inventory_ID,
Shelter_ID,
Resource_ID,
Organization_ID,
Zone_ID,
Quantity,
Last_Updated

)

VALUES

(?,?,?,?,?,?,NOW())"

);

$stmt->bind_param(

"sssssi",

$nextID,
$shelterID,
$resourceID,
$organizationID,
$zoneID,
$quantity

);


/* ==========================================
   EXECUTE INSERT
========================================== */

if($stmt->execute())
{

    echo json_encode(array(

        "success"=>true,

        "message"=>"Inventory Added Successfully.",

        "Inventory_ID"=>$nextID

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