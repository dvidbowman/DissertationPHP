<?php
require "DataBase.php";
$db = new DataBase();

$output = array(
  "message" => "none"
);

if (isset($_POST['username']) && isset($_POST['password'])) {
  if ($db->dbConnect()) {
    $output = $db->signUp("user_info", $_POST['username'], $_POST['password']);
    echo $output;
  } else $output['message'] = "Database Connection Error";
} else $output['message'] = "Field(s) missing";

echo json_encode($output);
?>
