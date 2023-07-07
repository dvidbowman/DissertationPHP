<?php
require "DataBaseConfig.php";

class DataBase
{
    public $connect;
    public $data;
    private $sql;
    protected $servername;
    protected $username;
    protected $password;
    protected $databasename;

    public function __construct() {
        $this->connect = null;
        $this->data = null;
        $this->sql = null;
        $dbc = new DataBaseConfig();
        $this->servername = $dbc->servername;
        $this->username = $dbc->username;
        $this->password = $dbc->password;
        $this->databasename = $dbc->databasename;
    }

    function dbConnect() {
        $this->connect = mysqli_connect($this->servername, $this->username, $this->password, $this->databasename);
        return $this->connect;
    }

    function prepareData($data) {

        return mysqli_real_escape_string($this->connect, stripslashes(htmlspecialchars($data)));
    }

    function logIn($table, $username, $password) {

      // Output Array
      $output = array(
        "auth" => false,
        "id" => 0,
        "noImages" => 0,
        "message" => "none"
      );

      // Get Login Authorisation
      $username = $this-> prepareData($username);
      $password = $this-> prepareData($password);
      $this->sql = "select * from " . $table . " where username = '" . $username . "'";
      $loginResult = mysqli_query($this->connect, $this->sql);
      $loginRow = mysqli_fetch_assoc($loginResult);

      if (mysqli_num_rows($loginResult) != 0) {
          $storedUsername = $loginRow['username'];
          $storedPassword = $loginRow['password'];

          if ($storedUsername == $username && password_verify($password, $storedPassword)) {
              $output['auth'] = true;
          } else $output['message'] = "Username or Password Incorrect";
      } else $output['message'] = "Username or Password Incorrect";

      // Get User ID and Number of User Images
      if ($output['auth'] == true) {
        $output['id'] = $loginRow['user_id'];

        $this->sql = "select * from `images` where user_id = " .$output['id'];
        $imageResult = mysqli_query($this->connect, $this->sql);
        $output['noImages'] = mysqli_num_rows($imageResult);
      }

      return json_encode($output);
    }

    function getUserImages($table, $userid, $rowNumber) {
      $output = array(
        "id" => 0,
        "date" => 0,
        "value" => 0,
        "avgRed" => 0,
        "pco2" => 0,
        "message" => "none"
      );

      $userid = $this->prepareData($userid);
      $rowNumber = $this->prepareData($rowNumber);
      $this->sql = "select * from " . $table . " where user_id = " .$userid. " ORDER BY image_id LIMIT 1 OFFSET " .$rowNumber;
      $result = mysqli_query($this->connect, $this->sql);
      $row = mysqli_fetch_assoc($result);

      if (mysqli_num_rows($result) != 0) {
        $output['id'] = $row['image_id'];
        $output['date'] = $row['upload_date'];
        $output['value'] = $row['image'];
        $output['avgRed'] = $row['average_red'];
        $output['pco2'] = $row['percentage_co2'];
      } else $output['message'] = "No images found";

      return json_encode($output);
    }

    function signUp($table, $username, $password) {
        $output = array(
          "message" => "none"
        );

        $username = $this->prepareData($username);
        $password = $this->prepareData($password);
        $password = password_hash($password, PASSWORD_DEFAULT);

        $this->sql =
            "INSERT INTO " .$table. " (username, password) VALUES ('" .$username. "','" .$password. "')";

        if (mysqli_query($this->connect, $this->sql)) {
            return json_encode($output);
        } else $output['message'] = "Sign Up Failed";

        return json_encode($output);
    }

    function uploadImage($table, $userid, $devicemanufacturer, $devicemodel, $deviceos, $image, $averagered, $pco2) {
        $output = array(
          "message" => "none"
        );

        $userid = $this->prepareData($userid);
        $devicemanufacturer = $this->prepareData($devicemanufacturer);
        $devicemodel = $this->prepareData($devicemodel);

        $this->sql =
        "insert into " .$table. " (user_id, upload_date, image, percentage_co2, average_red, device_manufacturer, device_model, device_os_version) VALUES ('" .$userid. "','" .date('Y-m-d H:i:s'). "','" .$image. "','" .$pco2. "','" .$averagered. "','" .$devicemanufacturer. "','" .$devicemodel. "','" .$deviceos. "')";

        if (mysqli_query($this->connect, $this->sql)) {
          return json_encode($output);
        } else $output['message'] = "Image Upload Failed";

        return json_encode($output);
    }

}

?>
