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
   RECEIVE RESOURCE ID
========================================== */

$resourceID = trim($_POST["Resource_ID"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(empty($resourceID))
{

    echo json_encode(array(

        "success" => false,
        "message" => "Resource ID is required."

    ));

    exit();

}


/* ==========================================
   CHECK IF RESOURCE EXISTS
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

        "success" => false,
        "message" => "Resource not found."

    ));

    $check->close();

    $conn->close();

    exit();

}

$check->close();


/* ==========================================
   DELETE RESOURCE
========================================== */

$stmt = $conn->prepare(

"DELETE

FROM resource

WHERE Resource_ID=?"

);

$stmt->bind_param(

"s",

$resourceID

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

            "message" => "Resource Deleted Successfully."

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