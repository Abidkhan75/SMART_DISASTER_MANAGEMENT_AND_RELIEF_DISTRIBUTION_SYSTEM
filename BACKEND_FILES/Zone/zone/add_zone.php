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
   GENERATE NEXT ZONE ID
========================================== */

$query = "SELECT Zone_ID
          FROM zone
          ORDER BY Zone_ID DESC
          LIMIT 1";

$result = $conn->query($query);

if($result && $result->num_rows > 0)
{
    $row = $result->fetch_assoc();

    $lastID = $row["Zone_ID"];

    $number = intval(substr($lastID,1));

    $number++;

    $nextID = "Z".str_pad($number,3,"0",STR_PAD_LEFT);
}
else
{
    $nextID = "Z001";
}

/* ==========================================
   RECEIVE DATA
========================================== */

$city        = trim($_POST["City"] ?? "");
$district    = trim($_POST["District"] ?? "");
$division    = trim($_POST["Division"] ?? "");
$population  = trim($_POST["Population"] ?? "");
$riskLevel   = trim($_POST["Risk_Level"] ?? "");

/* ==========================================
   VALIDATION
========================================== */

if(
    empty($city) ||
    empty($district) ||
    empty($division) ||
    empty($population) ||
    empty($riskLevel)
)
{
    echo json_encode(array(

        "success" => false,
        "message" => "Please fill all required fields."

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
   INSERT RECORD
========================================== */

$stmt = $conn->prepare(

"INSERT INTO zone
(
    Zone_ID,
    City,
    District,
    Division,
    Population,
    Risk_Level
)

VALUES
(
    ?,
    ?,
    ?,
    ?,
    ?,
    ?
)"

);

$stmt->bind_param(

"ssssis",

$nextID,
$city,
$district,
$division,
$population,
$riskLevel

);

if($stmt->execute())
{

    echo json_encode(array(

        "success" => true,

        "message" => "Zone Added Successfully.",

        "Zone_ID" => $nextID

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