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
   GENERATE NEXT VICTIM ID
========================================== */

$query = "SELECT Victim_ID
          FROM victim
          ORDER BY Victim_ID DESC
          LIMIT 1";

$result = $conn->query($query);

if($result && $result->num_rows > 0)
{

    $row = $result->fetch_assoc();

    $lastID = $row["Victim_ID"];

    $number = intval(substr($lastID,2));

    $number++;

    $nextID = "VI".str_pad($number,3,"0",STR_PAD_LEFT);

}
else
{

    $nextID = "VI001";

}


/* ==========================================
   RECEIVE DATA
========================================== */

$nid = trim($_POST["NID"] ?? "");

$shelterID = trim($_POST["Shelter_ID"] ?? "");

$zoneID = trim($_POST["Zone_ID"] ?? "");

$fullName = trim($_POST["Full_Name"] ?? "");

$age = trim($_POST["Age"] ?? "");

$gender = trim($_POST["Gender"] ?? "");

$familySize = trim($_POST["Family_Size"] ?? "");

$medicalStatus = trim($_POST["Medical_Status"] ?? "");

$contactNo = trim($_POST["Contact_No"] ?? "");

$password = trim($_POST["Victim_Password"] ?? "");


/* ==========================================
   VALIDATION
========================================== */

if(

empty($nid) ||
empty($shelterID) ||
empty($zoneID) ||
empty($fullName) ||
empty($age) ||
empty($gender) ||
empty($familySize) ||
empty($medicalStatus) ||
empty($contactNo) ||
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
   CHECK DUPLICATE NID
========================================== */

$check = $conn->prepare(

"SELECT NID
 FROM victim
 WHERE NID=?"

);

$check->bind_param("s",$nid);

$check->execute();

$result = $check->get_result();

if($result->num_rows > 0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"NID already exists."

    ));

    $check->close();

    $conn->close();

    exit();

}

$check->close();


/* ==========================================
   CHECK SHELTER EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Shelter_ID
 FROM shelter
 WHERE Shelter_ID=?"

);

$check->bind_param("s",$shelterID);

$check->execute();

$result = $check->get_result();

if($result->num_rows == 0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Shelter ID does not exist."

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

if($result->num_rows == 0)
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
   AGE VALIDATION
========================================== */

if(!is_numeric($age) || $age < 0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Age must be a positive number."

    ));

    exit();

}


/* ==========================================
   FAMILY SIZE VALIDATION
========================================== */

if(!is_numeric($familySize) || $familySize < 1)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Family Size must be at least 1."

    ));

    exit();

}


/* ==========================================
   PHONE VALIDATION
========================================== */

if(!preg_match('/^[0-9]{11}$/',$contactNo))
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Contact Number must contain exactly 11 digits."

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
   INSERT VICTIM
========================================== */

$stmt = $conn->prepare(

"INSERT INTO victim

(
Victim_ID,
NID,
Shelter_ID,
Zone_ID,
Full_Name,
Age,
Gender,
Family_Size,
Medical_Status,
Contact_No,
Victim_Password
)

VALUES

(?,?,?,?,?,?,?,?,?,?,?)"

);

$stmt->bind_param(

"sssssisssss",

$nextID,
$nid,
$shelterID,
$zoneID,
$fullName,
$age,
$gender,
$familySize,
$medicalStatus,
$contactNo,
$hashedPassword

);


/* ==========================================
   EXECUTE INSERT
========================================== */

if($stmt->execute())
{

    echo json_encode(array(

        "success"=>true,

        "message"=>"Victim Added Successfully.",

        "Victim_ID"=>$nextID

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