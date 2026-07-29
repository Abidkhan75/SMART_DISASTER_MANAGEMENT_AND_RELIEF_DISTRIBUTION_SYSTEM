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
   RECEIVE VICTIM ID
========================================== */

$victimID = trim($_POST["Victim_ID"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(empty($victimID))
{

    echo json_encode(array(

        "success" => false,
        "message" => "Victim ID is required."

    ));

    exit();

}


/* ==========================================
   CHECK IF VICTIM EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Victim_ID

FROM victim

WHERE Victim_ID=?"

);

$check->bind_param(

"s",

$victimID

);

$check->execute();

$result = $check->get_result();

if($result->num_rows == 0)
{

    echo json_encode(array(

        "success" => false,
        "message" => "Victim not found."

    ));

    $check->close();

    $conn->close();

    exit();

}

$check->close();


/* ==========================================
   DELETE VICTIM
========================================== */

$stmt = $conn->prepare(

"DELETE

FROM victim

WHERE Victim_ID=?"

);

$stmt->bind_param(

"s",

$victimID

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

            "message" => "Victim Deleted Successfully."

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