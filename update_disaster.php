<?php

require_once("../database.php");

/* ==========================================
   ONLY ALLOW POST REQUEST
========================================== */

if($_SERVER["REQUEST_METHOD"] != "POST")
{
    die("Only POST requests are allowed.");
}

/* ==========================================
   RECEIVE DATA
========================================== */

$disasterID   = trim($_POST["Disaster_ID"]);
$disasterName = trim($_POST["Disaster_Name"]);
$severity     = trim($_POST["Severity_Level"]);
$startTime    = trim($_POST["Start_Time"]);
$endTime      = trim($_POST["End_Time"]);
$status       = trim($_POST["Status"]);

/* ==========================================
   VALIDATION
========================================== */

if(
    empty($disasterID) ||
    empty($disasterName) ||
    empty($severity) ||
    empty($startTime) ||
    empty($status)
)
{
    die("Please fill all required fields.");
}

/* ==========================================
   DATE VALIDATION
========================================== */

if(!empty($endTime))
{
    if(strtotime($endTime) < strtotime($startTime))
    {
        die("End Date cannot be earlier than Start Date.");
    }
}

/* ==========================================
   CHECK EXISTENCE
========================================== */

$check = $conn->prepare(

"SELECT Disaster_ID
 FROM disaster
 WHERE Disaster_ID=?"

);

$check->bind_param("s",$disasterID);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0)
{
    die("Disaster ID does not exist.");
}

$check->close();

/* ==========================================
   UPDATE RECORD
========================================== */

$stmt = $conn->prepare(

"UPDATE disaster

SET

Disaster_Name=?,
Severity_Level=?,
Start_Time=?,
End_Time=?,
Status=?

WHERE Disaster_ID=?"

);

$stmt->bind_param(

"ssssss",

$disasterName,
$severity,
$startTime,
$endTime,
$status,
$disasterID

);

if($stmt->execute())
{
    echo "Disaster Updated Successfully.";
}
else
{
    echo "Update Failed.";
}

$stmt->close();

$conn->close();

?>