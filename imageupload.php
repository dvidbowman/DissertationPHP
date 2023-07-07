<?php
require "DataBase.php";
$db = new DataBase();

$output = array(
  "message" => "none"
);

if (isset($_POST['user_id']) && isset($_POST['image']) && isset($_POST['device_manufacturer']) && isset($_POST['device_model']) && isset($_POST['device_os']) && isset($_POST['average_red']) && isset($_POST['pco2'])) {
  if ($db->dbConnect()) {
    $output = $db->uploadImage("images", $_POST['user_id'], $_POST["device_manufacturer"], $_POST["device_model"], $_POST["device_os"], $_POST["image"], $_POST["average_red"], $_POST["pco2"]);
    echo $output;
  } else $output['message'] = "Database Connection Error";
} else $output['message'] = "Field(s) missing";

echo json_encode($output);
?>
