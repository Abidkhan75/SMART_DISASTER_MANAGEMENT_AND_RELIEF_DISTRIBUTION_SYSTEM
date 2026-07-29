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

$inventoryID = trim($_POST["Inventory_ID"] ?? "");

$shelterID = trim($_POST["Shelter_ID"] ?? "");

$resourceID = trim($_POST["Resource_ID"] ?? "");

$organizationID = trim($_POST["Organization_ID"] ?? "");

$zoneID = trim($_POST["Zone_ID"] ?? "");

$quantity = trim($_POST["Quantity"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(

empty($inventoryID) ||
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
   CHECK INVENTORY EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Inventory_ID
 FROM inventory
 WHERE Inventory_ID=?"

);

$check->bind_param("s",$inventoryID);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Inventory record not found."

    ));

    $check->close();
    $conn->close();
    exit();

}

$check->close();


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
   UPDATE INVENTORY
========================================== */

$stmt = $conn->prepare(

"UPDATE inventory

SET

Shelter_ID=?,
Resource_ID=?,
Organization_ID=?,
Zone_ID=?,
Quantity=?,
Last_Updated=NOW()

WHERE Inventory_ID=?"

);

$stmt->bind_param(

"ssssis",

$shelterID,
$resourceID,
$organizationID,
$zoneID,
$quantity,
$inventoryID

);


/* ==========================================
   EXECUTE UPDATE
========================================== */

if($stmt->execute())
{

    echo json_encode(array(

        "success"=>true,

        "message"=>"Inventory Updated Successfully."

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