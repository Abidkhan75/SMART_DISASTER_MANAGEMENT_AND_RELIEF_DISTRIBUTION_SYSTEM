<?php

session_start();

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
   CHECK LOGIN STATUS
========================================== */

if(!isset($_SESSION["logged_in"]))
{

    echo json_encode(array(

        "success" => false,
        "message" => "No active session found."

    ));

    exit();

}


/* ==========================================
   DESTROY SESSION
========================================== */

$_SESSION = array();

if (ini_get("session.use_cookies"))
{

    $params = session_get_cookie_params();

    setcookie(

        session_name(),

        '',

        time() - 42000,

        $params["path"],

        $params["domain"],

        $params["secure"],

        $params["httponly"]

    );

}

session_destroy();


/* ==========================================
   RETURN RESPONSE
========================================== */

echo json_encode(array(

    "success" => true,

    "message" => "Organization logged out successfully."

));

?>