<?php namespace presentation;
setcookie("PHPSESSID", "", -1000);
setcookie("tscskunks-pwa", "", -1000);
$lnk="index.php";
sleep(2);
header("Location: $lnk",301);