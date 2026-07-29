<?php

session_start();

header("Content-Type: application/json");

/* ==========================================
   CHECK LOGIN STATUS
========================================== */

if(

!isset($_SESSION["logged_in"]) ||
$_SESSION["logged_in"] !== true ||
!isset($_SESSION["organization_id"])

)
{

    http_response_code(401);

    echo json_encode(array(

        "success" => false,

        "message" => "Access Denied. Please login as an Organization."

    ));

    exit();

}


/* ==========================================
   OPTIONAL: STORE SESSION VALUES
========================================== */

$organizationID = $_SESSION["organization_id"];

$organizationName = $_SESSION["organization_name"] ?? "";

?>