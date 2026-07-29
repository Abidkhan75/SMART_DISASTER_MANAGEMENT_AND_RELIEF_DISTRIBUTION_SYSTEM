<?php

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
   RECEIVE DISTRIBUTION ID
========================================== */

$disID = trim($_POST["Dis_ID"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(empty($disID))
{

    echo json_encode(array(

        "success" => false,
        "message" => "Distribution ID is required."

    ));

    exit();

}


/* ==========================================
   CHECK IF RECORD EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Dis_ID

FROM relief_distribution

WHERE Dis_ID=?"

);

$check->bind_param(

"s",

$disID

);

$check->execute();

$result = $check->get_result();

if($result->num_rows == 0)
{

    echo json_encode(array(

        "success" => false,
        "message" => "Relief Distribution record not found."

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

FROM relief_distribution

WHERE Dis_ID=?"

);

$stmt->bind_param(

"s",

$disID

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

            "message" => "Relief Distribution Deleted Successfully."

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