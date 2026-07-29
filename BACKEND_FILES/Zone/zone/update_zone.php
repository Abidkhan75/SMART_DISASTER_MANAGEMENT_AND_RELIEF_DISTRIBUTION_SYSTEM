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
   RECEIVE DATA
========================================== */

$zoneID     = trim($_POST["Zone_ID"] ?? "");
$city       = trim($_POST["City"] ?? "");
$district   = trim($_POST["District"] ?? "");
$division   = trim($_POST["Division"] ?? "");
$population = trim($_POST["Population"] ?? "");
$riskLevel  = trim($_POST["Risk_Level"] ?? "");

/* ==========================================
   VALIDATION
========================================== */

if(
    empty($zoneID) ||
    empty($city) ||
    empty($district) ||
    empty($division) ||
    empty($population) ||
    empty($riskLevel)
)
{
    echo json_encode(array(

        "success" => false,
        "message" => "All fields are required."

    ));

    exit();
}

/* ==========================================
   POPULATION VALIDATION
========================================== */

if(!is_numeric($population) || $population < 0)
{
    echo json_encode(array(

        "success" => false,
        "message" => "Population must be a positive number."

    ));

    exit();
}

/* ==========================================
   RISK LEVEL VALIDATION
========================================== */

$validRisk = array(

    "Low",
    "Medium",
    "High",
    "Critical"

);

if(!in_array($riskLevel,$validRisk))
{
    echo json_encode(array(

        "success" => false,
        "message" => "Invalid Risk Level."

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

$check->bind_param("s",$zoneID);

$check->execute();

$result = $check->get_result();

if($result->num_rows == 0)
{
    echo json_encode(array(

        "success" => false,
        "message" => "Zone ID does not exist."

    ));

    $check->close();
    $conn->close();

    exit();
}

$check->close();

/* ==========================================
   UPDATE ZONE
========================================== */

$stmt = $conn->prepare(

"UPDATE zone

SET

City = ?,
District = ?,
Division = ?,
Population = ?,
Risk_Level = ?

WHERE Zone_ID = ?"

);

$stmt->bind_param(

"sssiss",

$city,
$district,
$division,
$population,
$riskLevel,
$zoneID

);

/* ==========================================
   EXECUTE UPDATE
========================================== */

if($stmt->execute())
{

    echo json_encode(array(

        "success" => true,

        "message" => "Zone Updated Successfully."

    ));

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