<?php

session_start();

header("Content-Type: application/json");

/* ==========================================
   ONLY ALLOW POST
========================================== */

if($_SERVER["REQUEST_METHOD"]!="POST")
{

    http_response_code(405);

    echo json_encode(array(

        "success"=>false,

        "message"=>"Only POST requests are allowed."

    ));

    exit();

}


/* ==========================================
   DESTROY SESSION
========================================== */

$_SESSION=array();

if(ini_get("session.use_cookies"))
{

    $params=session_get_cookie_params();

    setcookie(

        session_name(),

        '',

        time()-42000,

        $params["path"],

        $params["domain"],

        $params["secure"],

        $params["httponly"]

    );

}

session_destroy();


/* ==========================================
   SUCCESS
========================================== */

echo json_encode(array(

    "success"=>true,

    "message"=>"Admin Logged Out Successfully."

));

?>