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
   RECEIVE VOLUNTEER ID
========================================== */

$volunteerID = trim($_POST["Volunteer_ID"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(empty($volunteerID))
{

    echo json_encode(array(

        "success" => false,
        "message" => "Volunteer ID is required."

    ));

    exit();

}


/* ==========================================
   CHECK IF VOLUNTEER EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Volunteer_ID

FROM volunteer

WHERE Volunteer_ID=?"

);

$check->bind_param(

"s",

$volunteerID

);

$check->execute();

$result = $check->get_result();

if($result->num_rows == 0)
{

    echo json_encode(array(

        "success" => false,
        "message" => "Volunteer not found."

    ));

    $check->close();

    $conn->close();

    exit();

}

$check->close();


/* ==========================================
   DELETE VOLUNTEER
========================================== */

$stmt = $conn->prepare(

"DELETE

FROM volunteer

WHERE Volunteer_ID=?"

);

$stmt->bind_param(

"s",

$volunteerID

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

            "message" => "Volunteer Deleted Successfully."

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