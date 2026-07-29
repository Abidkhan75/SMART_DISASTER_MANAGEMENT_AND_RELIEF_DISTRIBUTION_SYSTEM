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

$victimID = trim($_POST["Victim_ID"] ?? "");

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

empty($victimID) ||
empty($nid) ||
empty($shelterID) ||
empty($zoneID) ||
empty($fullName) ||
empty($age) ||
empty($gender) ||
empty($familySize) ||
empty($medicalStatus) ||
empty($contactNo)

)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Please fill all required fields."

    ));

    exit();

}


/* ==========================================
   CHECK VICTIM EXISTS
========================================== */

$check = $conn->prepare(

"SELECT Victim_ID

FROM victim

WHERE Victim_ID=?"

);

$check->bind_param("s",$victimID);

$check->execute();

$result = $check->get_result();

if($result->num_rows==0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Victim not found."

    ));

    $check->close();

    $conn->close();

    exit();

}

$check->close();


/* ==========================================
   CHECK DUPLICATE NID
========================================== */

$check = $conn->prepare(

"SELECT Victim_ID

FROM victim

WHERE NID=? AND Victim_ID<>?"

);

$check->bind_param(

"ss",

$nid,
$victimID

);

$check->execute();

$result = $check->get_result();

if($result->num_rows>0)
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

if($result->num_rows==0)
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
   AGE VALIDATION
========================================== */

if(!is_numeric($age) || $age < 0)
{

    echo json_encode(array(

        "success"=>false,
        "message"=>"Invalid Age."

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
        "message"=>"Invalid Family Size."

    ));

    exit();

}


/* ==========================================
   CONTACT NUMBER VALIDATION
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
   UPDATE WITH PASSWORD
========================================== */

if(!empty($password))
{

    if(strlen($password)<8)
    {

        echo json_encode(array(

            "success"=>false,
            "message"=>"Password must be at least 8 characters."

        ));

        exit();

    }

    $hashedPassword = password_hash(

        $password,

        PASSWORD_DEFAULT

    );

    $stmt = $conn->prepare(

    "UPDATE victim

    SET

    NID=?,
    Shelter_ID=?,
    Zone_ID=?,
    Full_Name=?,
    Age=?,
    Gender=?,
    Family_Size=?,
    Medical_Status=?,
    Contact_No=?,
    Victim_Password=?

    WHERE Victim_ID=?"

    );

    $stmt->bind_param(

    "ssssisissss",

    $nid,
    $shelterID,
    $zoneID,
    $fullName,
    $age,
    $gender,
    $familySize,
    $medicalStatus,
    $contactNo,
    $hashedPassword,
    $victimID

    );

}

/* ==========================================
   UPDATE WITHOUT PASSWORD
========================================== */

else
{

    $stmt = $conn->prepare(

    "UPDATE victim

    SET

    NID=?,
    Shelter_ID=?,
    Zone_ID=?,
    Full_Name=?,
    Age=?,
    Gender=?,
    Family_Size=?,
    Medical_Status=?,
    Contact_No=?

    WHERE Victim_ID=?"

    );

    $stmt->bind_param(

    "ssssisisss",

    $nid,
    $shelterID,
    $zoneID,
    $fullName,
    $age,
    $gender,
    $familySize,
    $medicalStatus,
    $contactNo,
    $victimID

    );

}


/* ==========================================
   EXECUTE UPDATE
========================================== */

if($stmt->execute())
{

    echo json_encode(array(

        "success"=>true,

        "message"=>"Victim Updated Successfully."

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