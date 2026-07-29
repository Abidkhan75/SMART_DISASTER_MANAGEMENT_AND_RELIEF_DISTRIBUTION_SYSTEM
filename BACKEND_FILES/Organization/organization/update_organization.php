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

$organizationID = trim($_POST["Organization_ID"] ?? "");

$organizationName = trim($_POST["Organization_Name"] ?? "");

$address = trim($_POST["Address"] ?? "");

$contactNo = trim($_POST["Contact_No"] ?? "");

$email = trim($_POST["Email"] ?? "");

$password = trim($_POST["Org_Password"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(

empty($organizationID) ||
empty($organizationName) ||
empty($address) ||
empty($contactNo) ||
empty($email)

)
{

    echo json_encode(array(

        "success" => false,
        "message" => "Please fill all required fields."

    ));

    exit();

}


/* ==========================================
   CHECK ORGANIZATION EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Organization_ID
 FROM organization
 WHERE Organization_ID=?"

);

$check->bind_param(

"s",

$organizationID

);

$check->execute();

$result = $check->get_result();

if($result->num_rows == 0)
{

    echo json_encode(array(

        "success" => false,
        "message" => "Organization not found."

    ));

    $check->close();

    $conn->close();

    exit();

}

$check->close();


/* ==========================================
   CONTACT VALIDATION
========================================== */

if(!preg_match('/^[0-9]{11}$/',$contactNo))
{

    echo json_encode(array(

        "success" => false,
        "message" => "Contact Number must contain exactly 11 digits."

    ));

    exit();

}


/* ==========================================
   EMAIL VALIDATION
========================================== */

if(!filter_var($email,FILTER_VALIDATE_EMAIL))
{

    echo json_encode(array(

        "success" => false,
        "message" => "Invalid Email Address."

    ));

    exit();

}


/* ==========================================
   CHECK DUPLICATE EMAIL
========================================== */

$check = $conn->prepare(

"SELECT Organization_ID
 FROM organization
 WHERE Email=? AND Organization_ID<>?"

);

$check->bind_param(

"ss",

$email,
$organizationID

);

$check->execute();

$result = $check->get_result();

if($result->num_rows > 0)
{

    echo json_encode(array(

        "success" => false,
        "message" => "Email already exists."

    ));

    $check->close();

    $conn->close();

    exit();

}

$check->close();


/* ==========================================
   UPDATE WITH PASSWORD
========================================== */

if(!empty($password))
{

    if(strlen($password) < 8)
    {

        echo json_encode(array(

            "success" => false,
            "message" => "Password must be at least 8 characters."

        ));

        exit();

    }

    $hashedPassword = password_hash(

        $password,

        PASSWORD_DEFAULT

    );

    $stmt = $conn->prepare(

    "UPDATE organization

    SET

    Organization_Name=?,
    Address=?,
    Contact_No=?,
    Email=?,
    Org_Password=?

    WHERE Organization_ID=?"

    );

    $stmt->bind_param(

    "ssssss",

    $organizationName,
    $address,
    $contactNo,
    $email,
    $hashedPassword,
    $organizationID

    );

}

/* ==========================================
   UPDATE WITHOUT PASSWORD
========================================== */

else
{

    $stmt = $conn->prepare(

    "UPDATE organization

    SET

    Organization_Name=?,
    Address=?,
    Contact_No=?,
    Email=?

    WHERE Organization_ID=?"

    );

    $stmt->bind_param(

    "sssss",

    $organizationName,
    $address,
    $contactNo,
    $email,
    $organizationID

    );

}


/* ==========================================
   EXECUTE UPDATE
========================================== */

if($stmt->execute())
{

    echo json_encode(array(

        "success" => true,

        "message" => "Organization Updated Successfully."

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