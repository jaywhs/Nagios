#!/usr/bin/php
<?php
//Written by Jay Ramirez
//checks kernel type and runs a version check on kernel to see if it's on the patched update for Meltdown/Spectre
//only checks amzn and centos type images
$box_type = shell_exec("uname -r");

if (strpos($box_type, 'amzn') !== false) {
  kcheckamzn($box_type);
}
elseif (strpos($box_type, 'centos') !== false) {
  kcheckcentos($box_type);
}
else {
  echo "Issue pulling image type";
  exit (1);
}

//function for amazon boxes
function kcheckamzn($version) {

  if (version_compare("$version", "4.9.77-31.58.amzn1.x86_64") < 0) { //version stated is min version for aws
    echo "WARN: I am still Kernel Version " . php_uname('r') . ".  I need to be 4.9.77-31.58.amzn1.x86_64 or greater\n";
    exit (1);
  }
  elseif (version_compare("$version", "4.9.77-31.58.amzn1.x86_64") >= 0) {
    echo "OK: I am at least Kernel Version 4.9.77-31.58.amzn1.x86_64, my version: " . php_uname('r') . "\n";
    exit (0);
  }
  else {
    echo "WARN: Issue pulling kernel version\n";
    exit (1);
  }
}

//function for centos boxes
function kcheckcentos($version) {
  if (version_compare("$version", "2.6.32-696.18.7.el6.centos.plus.x86_64") < 0) { //version stated is min version for centos
    echo "WARN: I am still Kernel Version " . php_uname('r') . ".  I need to be 2.6.32-696.18.7.el6.centos.plus.x86_64 or greater\n";
    exit (1);
  }
  elseif (version_compare("$version", "2.6.32-696.18.7.el6.centos.plus.x86_64") >= 0) {
    echo "OK: I am at least Kernel Version 2.6.32-696.18.7.el6.centos.plus.x86_64, my version: " . php_uname('r') . "\n";
    exit (0);
  }
  else {
    echo "WARN: Issue pulling kernel version\n";
    exit (1);
  }
}
?>
