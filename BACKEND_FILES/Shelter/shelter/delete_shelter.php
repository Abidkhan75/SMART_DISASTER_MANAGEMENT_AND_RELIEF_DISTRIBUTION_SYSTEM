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
   RECEIVE SHELTER ID
========================================== */

$shelterID = trim($_POST["Shelter_ID"] ?? "");

/* ==========================================
   VALIDATION
========================================== */

if(empty($shelterID))
{
    echo json_encode(array(

        "success" => false,
        "message" => "Shelter ID is required."

    ));

    exit();
}

/* ==========================================
   CHECK IF SHELTER EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Shelter_ID
 FROM shelter
 WHERE Shelter_ID = ?"

);

$check->bind_param("s", $shelterID);

$check->execute();

$result = $check->get_result();

if($result->num_rows == 0)
{
    echo json_encode(array(

        "success" => false,
        "message" => "Shelter not found."

    ));

    $check->close();
    $conn->close();

    exit();
}

$check->close();

/* ==========================================
   DELETE SHELTER
========================================== */

$stmt = $conn->prepare(

"DELETE
 FROM shelter
 WHERE Shelter_ID = ?"

);

$stmt->bind_param(

"s",

$shelterID

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

            "message" => "Shelter Deleted Successfully."

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