<?php
require "DataBase.php";
$db = new DataBase();

$output = array(
  "auth" => false,
  "id" => 0,
  "noImages" => 0,
  "message" => "none"
);

if (isset($_POST['username']) && isset($_POST['password'])) {
  if ($db->dbConnect()) {
    $output = $db->logIn("user_info", $_POST['username'], $_POST['password']);
    echo $output;
  } else $output['message'] = "Database Connection Failure";
} else $output['message'] = "Missing Username or Password";

echo json_encode($output);
?>
