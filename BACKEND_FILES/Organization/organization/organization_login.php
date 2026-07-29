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
   RECEIVE LOGIN DATA
========================================== */

$organizationID = trim($_POST["Organization_ID"] ?? "");

$password = trim($_POST["Org_Password"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(

empty($organizationID) ||
empty($password)

)
{

    echo json_encode(array(

        "success" => false,
        "message" => "Organization ID and Password are required."

    ));

    exit();

}


/* ==========================================
   FIND ORGANIZATION
========================================== */

$stmt = $conn->prepare(

"SELECT

Organization_ID,
Organization_Name,
Org_Password

FROM organization

WHERE Organization_ID = ?"

);

$stmt->bind_param(

"s",

$organizationID

);

$stmt->execute();

$result = $stmt->get_result();


/* ==========================================
   ORGANIZATION NOT FOUND
========================================== */

if($result->num_rows == 0)
{

    echo json_encode(array(

        "success" => false,
        "message" => "Invalid Organization ID or Password."

    ));

    $stmt->close();

    $conn->close();

    exit();

}


$row = $result->fetch_assoc();


/* ==========================================
   VERIFY PASSWORD
========================================== */

if(password_verify($password,$row["Org_Password"]))
{

    echo json_encode(array(

        "success" => true,

        "message" => "Login Successful.",

        "Organization_ID" => $row["Organization_ID"],

        "Organization_Name" => $row["Organization_Name"]

    ));

}
else
{

    echo json_encode(array(

        "success" => false,

        "message" => "Invalid Organization ID or Password."

    ));

}


$stmt->close();

$conn->close();

?>