<?php

require_once("../database.php");

/* ==========================================
   ONLY ALLOW GET REQUEST
========================================== */

if ($_SERVER["REQUEST_METHOD"] != "GET")
{
    http_response_code(405);

    echo json_encode(array(

        "success" => false,
        "message" => "Only GET requests are allowed."

    ));

    exit();
}

/* ==========================================
   CHECK DISASTER ID
========================================== */

if (!isset($_GET["id"]))
{
    http_response_code(400);

    echo json_encode(array(

        "success" => false,
        "message" => "Disaster ID is required."

    ));

    exit();
}

$disasterID = trim($_GET["id"]);

if (empty($disasterID))
{
    http_response_code(400);

    echo json_encode(array(

        "success" => false,
        "message" => "Invalid Disaster ID."

    ));

    exit();
}

/* ==========================================
   RETRIEVE DISASTER
========================================== */

$stmt = $conn->prepare(

"SELECT
    Disaster_ID,
    Disaster_Name,
    Severity_Level,
    Start_Time,
    End_Time,
    Status
FROM disaster
WHERE Disaster_ID = ?"

);

$stmt->bind_param("s", $disasterID);

$stmt->execute();

$result = $stmt->get_result();

/* ==========================================
   CHECK RECORD
========================================== */

if ($result->num_rows == 0)
{
    http_response_code(404);

    echo json_encode(array(

        "success" => false,
        "message" => "Disaster not found."

    ));

    $stmt->close();

    $conn->close();

    exit();
}

/* ==========================================
   RETURN DISASTER DETAILS
========================================== */

$disaster = $result->fetch_assoc();

header("Content-Type: application/json");

echo json_encode(array(

    "success" => true,

    "data" => $disaster

), JSON_PRETTY_PRINT);

$stmt->close();

$conn->close();

?>