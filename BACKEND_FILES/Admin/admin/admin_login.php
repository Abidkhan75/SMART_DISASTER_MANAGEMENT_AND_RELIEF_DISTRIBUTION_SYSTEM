<?php

require_once("../database.php");

session_start();

header("Content-Type: application/json");

/* ==========================================
   ONLY ALLOW POST REQUEST
========================================== */

if($_SERVER["REQUEST_METHOD"] != "POST")
{

    http_response_code(405);

    echo json_encode(array(

        "success"=>false,
        "message"=>"Only POST requests are allowed."

    ));

    exit();

}


/* ==========================================
   RECEIVE DATA
========================================== */

$adminID = trim($_POST["Admin_ID"] ?? "");

$password = trim($_POST["Admin_Password"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(empty($adminID) || empty($password))
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Please enter Admin ID and Password."

    ));

    exit();

}


/* ==========================================
   CHECK LOGIN
========================================== */

$stmt = $conn->prepare(

"SELECT

Admin_ID,
Admin_Name,
Admin_Number,
Admin_Password

FROM admin

WHERE Admin_ID=?"

);

$stmt->bind_param(

"s",

$adminID

);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Invalid Admin ID."

    ));

    exit();

}

$row = $result->fetch_assoc();


if($password != $row["Admin_Password"])
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Incorrect Password."

    ));

    exit();

}


/* ==========================================
   CREATE SESSION
========================================== */

$_SESSION["logged_in"]=true;

$_SESSION["role"]="Admin";

$_SESSION["admin_id"]=$row["Admin_ID"];

$_SESSION["admin_name"]=$row["Admin_Name"];


/* ==========================================
   SUCCESS
========================================== */

echo json_encode(array(

    "success"=>true,

    "message"=>"Admin Login Successful.",

    "Admin_ID"=>$row["Admin_ID"],

    "Admin_Name"=>$row["Admin_Name"]

));

$stmt->close();

$conn->close();

?>