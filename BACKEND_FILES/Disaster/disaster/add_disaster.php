<?php
require_once("../admin/admin_auth.php");
require_once("../database.php");

/* ===========================================
   CHECK REQUEST METHOD
=========================================== */

if($_SERVER["REQUEST_METHOD"] != "POST")
{
    die("Only POST requests are allowed.");
}

/* ===========================================
   GENERATE NEXT DISASTER ID
=========================================== */

$query = "SELECT Disaster_ID
          FROM disaster
          ORDER BY Disaster_ID DESC
          LIMIT 1";

$result = $conn->query($query);

if($result && $result->num_rows > 0)
{
    $row = $result->fetch_assoc();

    $lastID = $row["Disaster_ID"];

    $number = intval(substr($lastID,1));

    $number++;

    $nextID = "D".str_pad($number,3,"0",STR_PAD_LEFT);
}
else
{
    $nextID = "D001";
}

/* ===========================================
   RECEIVE DATA
=========================================== */

$disasterName = isset($_POST["Disaster_Name"]) ? trim($_POST["Disaster_Name"]) : "";
$severity     = isset($_POST["Severity_Level"]) ? trim($_POST["Severity_Level"]) : "";
$startTime    = isset($_POST["Start_Time"]) ? trim($_POST["Start_Time"]) : "";
$endTime      = isset($_POST["End_Time"]) ? trim($_POST["End_Time"]) : "";
$status       = isset($_POST["Status"]) ? trim($_POST["Status"]) : "";

/* ===========================================
   VALIDATION
=========================================== */

if(
    empty($disasterName) ||
    empty($severity) ||
    empty($startTime) ||
    empty($status)
)
{
    die("Error : All required fields must be filled.");
}

/* ===========================================
   DATE VALIDATION
=========================================== */

if(!empty($endTime))
{
    if(strtotime($endTime) < strtotime($startTime))
    {
        die("Error : End Time cannot be earlier than Start Time.");
    }
}

/* ===========================================
   VALID SEVERITY
=========================================== */

$validSeverity = array("Low","Medium","High","Critical");

if(!in_array($severity,$validSeverity))
{
    die("Error : Invalid Severity Level.");
}

/* ===========================================
   VALID STATUS
=========================================== */

$validStatus = array("Upcoming","Active","Resolved");

if(!in_array($status,$validStatus))
{
    die("Error : Invalid Status.");
}

/* ===========================================
   INSERT DATA
=========================================== */

$stmt = $conn->prepare(

"INSERT INTO disaster
(
    Disaster_ID,
    Disaster_Name,
    Severity_Level,
    Start_Time,
    End_Time,
    Status
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

"ssssss",

$nextID,
$disasterName,
$severity,
$startTime,
$endTime,
$status

);

if($stmt->execute())
{

    echo "====================================<br>";
    echo "DISASTER ADDED SUCCESSFULLY<br>";
    echo "====================================<br>";
    echo "Generated Disaster ID : ".$nextID."<br>";

}
else
{

    echo "Insertion Failed.<br>";

    echo $stmt->error;

}

$stmt->close();

$conn->close();

?>