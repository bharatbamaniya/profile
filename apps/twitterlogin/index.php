<?php

//code breakdown
// - Part 1 - Defining
// - Part 2 - Process
// - Part 3 - Front end

// Part 1 - Defining
require_once('lib/Oauth.php');
require_once('lib/twitteroauth.php');

define('CONSUMER_KEY','WyqSYnMQDsmZCx8yJslhRWpdQ');
define('CONSUMER_SECRET','lteHxwbkMCtDwp3R1piWam3BP7CaDFMZzzCZc4cFIXRGz1d95O');
define('OAUTH_CALLBACK','https://bharatkumar.ml/apps/twitterlogin/');

session_start();

//Part 2 - Process
// 1. check for logout
// 2. check for user session
// 3. check for callback

// 1. to handle logout request

if(isset($_GET['logout'])){
    session_unset();
    $redirect = "https://".$_SERVER['HTTPS_HOST'].$_SERVER['PHP_SELF'];
    header('Location: '.filter_var($redirect, FILTER_SANITIZER_URL));
}

// 2. If user session not enable then get the login url

if(!isset($_SESSION['data']) && !isset($_GET['oauth_token'])) {
    $connection = new TwitterOAuth(CONSUMER_KEY,CONSUMER_SECRET);
    $request_token = $connection->getRequestToken(OAUTH_CALLBACK);
    
    if($request_token)
    {
        $token = $request_token['oauth_token'];
        $_SESSION['request_token'] = $token;
        $_SESSION['request_token_secret'] = $request_token['oauth_token_secret'];

        $login_url = $connection->getAuthorizeURL($token);
    }
}

// 3. if its a call back url

if(isset($GET['oauth_token'])) {
    $connection = new TwitterOAuth(CONSUMER_KEY,CONSUMER_SECRET, $_SESSION['request_token_secret']);
    $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
    if($access_token) {
        $connection = new TwitterOAuth(CONSUMER_KEY,CONSUMER_SECRET, $access_token['oauth_token'],$access_token['oauth_token_secret']);
        $params = array('include_entities'=>'false');
        $data = $connection->get('account/verify_credientials',$params);
        if($data){
            $_SESSION['data'] = $data;
            $redirect = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
            header('Location: '.filer_var($redirect,FILTER_SANITIZER_URL));
        }
    }
}

//PART 3 - front end code
if(isset($login_url) && !isset($_SESSION['data'])){
    echo "<a href='$login_url'><button>Login with twitter </button></a>";
}else {
    $data = $_SESSION['data'];
    echo "Name: ".$data->name."<br>";
    echo "Username: ".$data->screen_name."<br>";
    echo "Photo : <img src ='".$data->profile_image_url."'/><br><br>";

    echo "<a href='?logout=true'><button><Logout</button></a>";
}




?>