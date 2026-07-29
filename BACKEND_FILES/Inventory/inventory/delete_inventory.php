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
   RECEIVE INVENTORY ID
========================================== */

$inventoryID = trim($_POST["Inventory_ID"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(empty($inventoryID))
{

    echo json_encode(array(

        "success" => false,
        "message" => "Inventory ID is required."

    ));

    exit();

}


/* ==========================================
   CHECK IF INVENTORY EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Inventory_ID

FROM inventory

WHERE Inventory_ID=?"

);

$check->bind_param(

"s",

$inventoryID

);

$check->execute();

$result = $check->get_result();

if($result->num_rows == 0)
{

    echo json_encode(array(

        "success" => false,
        "message" => "Inventory record not found."

    ));

    $check->close();

    $conn->close();

    exit();

}

$check->close();


/* ==========================================
   DELETE INVENTORY
========================================== */

$stmt = $conn->prepare(

"DELETE

FROM inventory

WHERE Inventory_ID=?"

);

$stmt->bind_param(

"s",

$inventoryID

);


/* ==========================================
   EXECUTE DELETE
========================================== */

if($stmt->execute())
{

    if($stmt->affected_rows > 0)
    {

        echo json_encode(array(

            "success" => true,

            "message" => "Inventory Deleted Successfully."

        ));

    }
    else
    {

        echo json_encode(array(

            "success" => false,

            "message" => "Deletion Failed."

        ));

    }

}
else
{

    echo json_encode(array(

        "success" => false,

        "message" => $stmt->error

    ));

}

$stmt->close();

$conn->close();

?>