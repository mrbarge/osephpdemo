<html>
<body>
<?php

  if (!isset($_GET['name'])) {
    return;
  }
  $visitor = $_GET['name'];

  $config_file = getenv('APP_CONFIG');
  if ($config_file) {
    $appcfg = parse_ini_file($config_file);
  } else {
    die('Config file not found.');
  }

  try {
    $db = new PDO('mysql:host=' . $appcfg['MYSQL_DATABASE_HOST'] . ';dbname=' . $appcfg['MYSQL_DATABASE_NAME'] . ';charset=utf8',
      $appcfg['MYSQL_DATABASE_USER'], $appcfg['MYSQL_DATABASE_PASSWORD']);
    $stmt = $db->prepare("SELECT * FROM visitors WHERE username=?");
    $stmt->execute(array($visitor));
    $visits = 0;
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $visits = $row['visits'];
      $stmt = $db->prepare("UPDATE visitors SET visits = visits + 1 WHERE username = ?");
      $stmt->execute(array($visitor));
    } else {
      $stmt = $db->prepare("INSERT INTO visitors VALUES (?,?)");
      $stmt->execute(array($visitor,1));
      $visits = 1;
    }
    echo "Hey there $visitor you've visited $visits times.";
  } catch (PDOException $e) {
    die(json_encode(array('outcome' => false, 'message' => 'unable to connect')));
  }
?>
</body>
</html>
