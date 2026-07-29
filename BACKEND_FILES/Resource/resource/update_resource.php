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
   RECEIVE DATA
========================================== */

$resourceID = trim($_POST["Resource_ID"] ?? "");

$resourceName = trim($_POST["Resource_Name"] ?? "");

$category = trim($_POST["Category"] ?? "");

$unit = trim($_POST["Unit"] ?? "");

$unitCost = trim($_POST["Unit_Cost"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(

empty($resourceID) ||
empty($resourceName) ||
empty($category) ||
empty($unit) ||
empty($unitCost)

)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Please fill all required fields."

    ));

    exit();

}


/* ==========================================
   CHECK RESOURCE EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Resource_ID

FROM resource

WHERE Resource_ID=?"

);

$check->bind_param(

"s",

$resourceID

);

$check->execute();

$result = $check->get_result();

if($result->num_rows == 0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Resource not found."

    ));

    $check->close();

    $conn->close();

    exit();

}

$check->close();


/* ==========================================
   CHECK DUPLICATE RESOURCE NAME
========================================== */

$check = $conn->prepare(

"SELECT Resource_ID

FROM resource

WHERE Resource_Name=? AND Resource_ID<>?"

);

$check->bind_param(

"ss",

$resourceName,
$resourceID

);

$check->execute();

$result = $check->get_result();

if($result->num_rows > 0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Resource Name already exists."

    ));

    $check->close();

    $conn->close();

    exit();

}

$check->close();


/* ==========================================
   UNIT COST VALIDATION
========================================== */

if(!is_numeric($unitCost) || $unitCost <= 0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Unit Cost must be greater than 0."

    ));

    exit();

}


/* ==========================================
   UPDATE RESOURCE
========================================== */

$stmt = $conn->prepare(

"UPDATE resource

SET

Resource_Name=?,
Category=?,
Unit=?,
Unit_Cost=?

WHERE Resource_ID=?"

);

$stmt->bind_param(

"sssds",

$resourceName,
$category,
$unit,
$unitCost,
$resourceID

);


/* ==========================================
   EXECUTE UPDATE
========================================== */

if($stmt->execute())
{

    echo json_encode(array(

        "success"=>true,

        "message"=>"Resource Updated Successfully."

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