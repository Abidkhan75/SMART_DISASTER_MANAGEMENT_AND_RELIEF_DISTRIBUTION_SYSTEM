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
   RECEIVE ID
========================================== */

$disasterID = trim($_POST["Disaster_ID"]);

if(empty($disasterID))
{
    die("Disaster ID is required.");
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
   DELETE RECORD
========================================== */

$stmt = $conn->prepare(

"DELETE
 FROM disaster
 WHERE Disaster_ID=?"

);

$stmt->bind_param(

"s",

$disasterID

);

if($stmt->execute())
{
    echo "Disaster Deleted Successfully.";
}
else
{
    echo "Deletion Failed.";
}

$stmt->close();

$conn->close();

?>