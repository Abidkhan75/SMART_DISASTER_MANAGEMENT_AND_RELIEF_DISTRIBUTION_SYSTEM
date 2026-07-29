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
   RECEIVE ORGANIZATION ID
========================================== */

$organizationID = trim($_POST["Organization_ID"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(empty($organizationID))
{

    echo json_encode(array(

        "success" => false,
        "message" => "Organization ID is required."

    ));

    exit();

}


/* ==========================================
   CHECK IF ORGANIZATION EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Organization_ID

FROM organization

WHERE Organization_ID=?"

);

$check->bind_param(

"s",

$organizationID

);

$check->execute();

$result = $check->get_result();

if($result->num_rows == 0)
{

    echo json_encode(array(

        "success" => false,
        "message" => "Organization not found."

    ));

    $check->close();

    $conn->close();

    exit();

}

$check->close();


/* ==========================================
   DELETE ORGANIZATION
========================================== */

$stmt = $conn->prepare(

"DELETE

FROM organization

WHERE Organization_ID=?"

);

$stmt->bind_param(

"s",

$organizationID

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

            "message" => "Organization Deleted Successfully."

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