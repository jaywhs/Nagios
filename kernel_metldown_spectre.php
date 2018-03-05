#!/usr/bin/php
<?php
//Owner: SRE
//checks kernel type and runs a version check on kernel to see if it's on the patched update for Meltdown/Spectre
 
//Approved minimum Operating System versions
$OS["approved"] = (object) array(
  "amzn"   => "4.9.81-35.56.amzn1.x86_64",
  "el"     => "2.6.32-696.18.7.el6.x86_64",
);
 
$OS["current"] = shell_exec("uname -r");
if (!isset($OS["current"])){
  echo "WARN: Unable to retrieve kernel!\n";
  exit(1);
}
 
foreach ($OS["approved"] as $name => $version)
{
  $OS["int_current"] = preg_replace("/[^0-9]/", "", $OS["current"]);
  if (strpos($OS["current"], $name) !== false) {
    //found the matching name - check version
    $OS["int_compare"] = preg_replace("/[^0-9]/", "", $OS["approved"]->$name);
    if ($OS["int_current"] >= $OS["int_compare"])
    {
      echo "OK: Kernel version is up to date: Current: " . $OS["current"]  . "Compared:" . $version  . "\n";
      exit(0);
    }
    else {
      echo "WARN: Kernel version is not up to date: Current: " . $OS["current"]  . "Compared:" . $version  . "\n";
      exit(1);
    }
  }
}
 
?>
