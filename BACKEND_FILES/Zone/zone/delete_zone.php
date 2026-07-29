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
   RECEIVE ZONE ID
========================================== */

$zoneID = trim($_POST["Zone_ID"] ?? "");

/* ==========================================
   VALIDATION
========================================== */

if(empty($zoneID))
{
    echo json_encode(array(

        "success" => false,
        "message" => "Zone ID is required."

    ));

    exit();
}

/* ==========================================
   CHECK IF ZONE EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Zone_ID
 FROM zone
 WHERE Zone_ID = ?"

);

$check->bind_param("s", $zoneID);

$check->execute();

$result = $check->get_result();

if($result->num_rows == 0)
{
    echo json_encode(array(

        "success" => false,
        "message" => "Zone not found."

    ));

    $check->close();
    $conn->close();

    exit();
}

$check->close();

/* ==========================================
   DELETE RECORD
========================================== */

$stmt = $conn->prepare(

"DELETE
 FROM zone
 WHERE Zone_ID = ?"

);

$stmt->bind_param(

"s",

$zoneID

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

            "message" => "Zone Deleted Successfully."

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