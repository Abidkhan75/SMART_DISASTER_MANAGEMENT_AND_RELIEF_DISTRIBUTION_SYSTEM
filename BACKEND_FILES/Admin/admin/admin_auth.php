<?php

session_start();

header("Content-Type: application/json");

/* ==========================================
   AUTHENTICATION
========================================== */

if(

!isset($_SESSION["logged_in"]) ||

$_SESSION["logged_in"]!==true ||

!isset($_SESSION["role"]) ||

$_SESSION["role"]!="Admin"

)
{

    http_response_code(401);

    echo json_encode(array(

        "success"=>false,

        "message"=>"Access Denied. Admin Login Required."

    ));

    exit();

}


/* ==========================================
   SESSION VALUES
========================================== */

$adminID=$_SESSION["admin_id"];

$adminName=$_SESSION["admin_name"];

?>