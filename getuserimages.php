<?php
require "DataBase.php";
$db = new DataBase();

$output = array(
  "id" => 0,
  "date" => 0,
  "value" => 0,
  "message" => "none"
);

if (isset($_POST['user_id']) && isset($_POST['currentRowNumber'])) {
  if ($db->dbConnect()) {
    $imageDetails = $db->getUserImages("images", $_POST['user_id'], $_POST['currentRowNumber']);
    echo $imageDetails;
  } else $output['message'] = "Database Connection Failure";
} else $output['message'] = "Missing UserID or RowNumber";

echo json_encode($output);
?>
