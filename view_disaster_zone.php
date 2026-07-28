<?php

require_once("../database.php");

header("Content-Type: application/json");


/* ==========================================
   ONLY ALLOW GET REQUEST
========================================== */


if($_SERVER["REQUEST_METHOD"]!="GET")
{

    http_response_code(405);

    echo json_encode(array(

        "success"=>false,
        "message"=>"Only GET requests are allowed."

    ));

    exit();

}



/* ==========================================
   SORTING
========================================== */


$allowedSort=array(

"DisasterZone_ID",
"Disaster_ID",
"Zone_ID",
"Affected_Population",
"Estimated_Budget",
"Relief_Status",
"Damage_Level"

);


$sort="DisasterZone_ID";


if(isset($_GET["sort"]))
{

    if(in_array($_GET["sort"],$allowedSort))
    {
        $sort=$_GET["sort"];
    }

}



/* ==========================================
   SEARCH
========================================== */


if(isset($_GET["search"]))
{

    $search="%".trim($_GET["search"])."%";


    $stmt=$conn->prepare(

"SELECT *

FROM disaster_zone

WHERE DisasterZone_ID LIKE ?
OR Disaster_ID LIKE ?
OR Zone_ID LIKE ?
OR Relief_Status LIKE ?

ORDER BY $sort"

);


$stmt->bind_param(

"ssss",

$search,
$search,
$search,
$search

);



$stmt->execute();


$result=$stmt->get_result();


}

else
{


$result=$conn->query(

"SELECT *

FROM disaster_zone

ORDER BY $sort"

);


}



/* ==========================================
   RETURN DATA
========================================== */


$data=array();


while($row=$result->fetch_assoc())
{

    $data[]=$row;

}



echo json_encode(array(

"success"=>true,

"total_records"=>count($data),

"data"=>$data


),JSON_PRETTY_PRINT);



if(isset($stmt))
{
    $stmt->close();
}



$conn->close();


?>