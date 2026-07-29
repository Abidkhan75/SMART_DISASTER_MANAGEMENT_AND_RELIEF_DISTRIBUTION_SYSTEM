<?php
require_once("../admin/admin_auth.php");
require_once("../organization/organization_auth.php");
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
   GENERATE NEXT VOLUNTEER ID
========================================== */

$query = "SELECT Volunteer_ID
          FROM volunteer
          ORDER BY Volunteer_ID DESC
          LIMIT 1";

$result = $conn->query($query);

if($result && $result->num_rows > 0)
{

    $row = $result->fetch_assoc();

    $lastID = $row["Volunteer_ID"];

    $number = intval(substr($lastID,1));

    $number++;

    $nextID = "V".str_pad($number,3,"0",STR_PAD_LEFT);

}
else
{

    $nextID = "V001";

}


/* ==========================================
   RECEIVE DATA
========================================== */

$organizationID = trim($_POST["Organization_ID"] ?? "");

$zoneID = trim($_POST["Zone_ID"] ?? "");

$fullName = trim($_POST["Full_Name"] ?? "");

$phone = trim($_POST["Phone"] ?? "");

$gender = trim($_POST["Gender"] ?? "");

$skill = trim($_POST["Skill"] ?? "");

$availability = trim($_POST["Availability"] ?? "");

$password = trim($_POST["Volunteer_Password"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(

empty($organizationID) ||
empty($zoneID) ||
empty($fullName) ||
empty($phone) ||
empty($gender) ||
empty($skill) ||
empty($availability) ||
empty($password)

)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Please fill all required fields."

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

$check->bind_param("s",$organizationID);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Organization ID does not exist."

    ));

    $check->close();

    $conn->close();

    exit();

}

$check->close();


/* ==========================================
   CHECK ZONE EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Zone_ID
 FROM zone
 WHERE Zone_ID=?"

);

$check->bind_param("s",$zoneID);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Zone ID does not exist."

    ));

    $check->close();

    $conn->close();

    exit();

}

$check->close();


/* ==========================================
   PHONE VALIDATION
========================================== */

if(!preg_match('/^[0-9]{11}$/',$phone))
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Phone number must contain exactly 11 digits."

    ));

    exit();

}


/* ==========================================
   GENDER VALIDATION
========================================== */

$validGender = array(

"Male",
"Female",
"Other"

);

if(!in_array($gender,$validGender))
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Invalid Gender."

    ));

    exit();

}


/* ==========================================
   AVAILABILITY VALIDATION
========================================== */

$validAvailability = array(

"Available",
"Busy",
"Unavailable"

);

if(!in_array($availability,$validAvailability))
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Invalid Availability."

    ));

    exit();

}


/* ==========================================
   PASSWORD VALIDATION
========================================== */

if(strlen($password) < 8)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Password must be at least 8 characters."

    ));

    exit();

}


/* ==========================================
   HASH PASSWORD
========================================== */

$hashedPassword = password_hash(

$password,

PASSWORD_DEFAULT

);


/* ==========================================
   INSERT VOLUNTEER
========================================== */

$stmt = $conn->prepare(

"INSERT INTO volunteer

(
Volunteer_ID,
Organization_ID,
Zone_ID,
Full_Name,
Phone,
Gender,
Skill,
Availability,
Volunteer_Password
)

VALUES

(?,?,?,?,?,?,?,?,?)"

);

$stmt->bind_param(

"sssssssss",

$nextID,
$organizationID,
$zoneID,
$fullName,
$phone,
$gender,
$skill,
$availability,
$hashedPassword

);


/* ==========================================
   EXECUTE INSERT
========================================== */

if($stmt->execute())
{

    echo json_encode(array(

        "success"=>true,

        "message"=>"Volunteer Added Successfully.",

        "Volunteer_ID"=>$nextID

    ));

}
else
{

    echo json_encode(array(

        "success"=>false,

        "message"=>$stmt->error

    ));

}

$stmt->close();

$conn->close();

?>