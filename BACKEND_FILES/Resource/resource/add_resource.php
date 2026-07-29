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
   GENERATE NEXT RESOURCE ID
========================================== */

$query = "SELECT Resource_ID
          FROM resource
          ORDER BY Resource_ID DESC
          LIMIT 1";

$result = $conn->query($query);

if($result && $result->num_rows > 0)
{

    $row = $result->fetch_assoc();

    $lastID = $row["Resource_ID"];

    $number = intval(substr($lastID,1));

    $number++;

    $nextID = "R".str_pad($number,3,"0",STR_PAD_LEFT);

}
else
{

    $nextID = "R001";

}


/* ==========================================
   RECEIVE DATA
========================================== */

$resourceName = trim($_POST["Resource_Name"] ?? "");

$category = trim($_POST["Category"] ?? "");

$unit = trim($_POST["Unit"] ?? "");

$unitCost = trim($_POST["Unit_Cost"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(

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
   CHECK DUPLICATE RESOURCE NAME
========================================== */

$check = $conn->prepare(

"SELECT Resource_ID

FROM resource

WHERE Resource_Name=?"

);

$check->bind_param(

"s",

$resourceName

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
   INSERT RESOURCE
========================================== */

$stmt = $conn->prepare(

"INSERT INTO resource

(

Resource_ID,
Resource_Name,
Category,
Unit,
Unit_Cost

)

VALUES

(?,?,?,?,?)"

);

$stmt->bind_param(

"ssssd",

$nextID,
$resourceName,
$category,
$unit,
$unitCost

);


/* ==========================================
   EXECUTE INSERT
========================================== */

if($stmt->execute())
{

    echo json_encode(array(

        "success"=>true,

        "message"=>"Resource Added Successfully.",

        "Resource_ID"=>$nextID

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