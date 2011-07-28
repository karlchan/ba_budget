<?php
$con = mysql_connect("localhost","budgetuser","password");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db("ba_budget", $con);
?>