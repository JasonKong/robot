<?php
    include("Robot.php");
    $file = getopt("f:");

    if ($file && isset($file['f'])) {
      $robot = new Robot($file['f']);
    } else {
      $robot = new Robot();
    }
?>