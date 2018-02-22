#!/usr/bin/php
<?php

#written by Jay Ramirez
#Edited on 12/8/17 by Jay Ramirez:
#added the strtotime converion for the maintenance date so that the calculations are done correctly
#configured the warn and crit to 3/5 days instead of 1/3

//pull json and decode
error_reporting( error_reporting() & ~E_NOTICE );
$hostname = shell_exec("hostname");
$statusResults = shell_exec("/usr/local/nagios/libexec/describeInstanceStatus.sh");
$statusResults = json_decode($statusResults, true);

//sets variables of decoded json values to use in if/else statements
$systemStatus = $statusResults["InstanceStatuses"][0]["SystemStatus"]["Status"];
$instanceStatus = $statusResults["InstanceStatuses"][0]["InstanceStatus"]["Details"][0]["Status"];
$instanceMaint = $statusResults["InstanceStatuses"][0]["Events"];

//if status is not empty and equal to value then pass the value if not return "false"
$systemStatus = (!empty($systemStatus)) ? $systemStatus : false;
$instanceStatus = (!empty($instanceStatus)) ? $instanceStatus : false;

//checks system maintenance date if applicable and outputs a warn/crit depending on the amount of time left
if(!empty($instanceMaint)){
  $curr_date = time();
  $maint_date =  strtotime($instanceMaint[0]["NotBefore"]);
  $warn = 24*60*60*5;
  $crit = 24*60*60*3;
 if(($curr_date + $warn) > $maint_date){
  echo "WARNING: System Maintenance is 5 days or less away: $maint_date";
  exit(1);
 }
 elseif(($curr_date + $crit) > $maint_date){
  echo "CRIT: System Maintenance is 3 day or less away: $maint_date";
  exit(2);
 }
}
//if json returns a null issue a warning
if($systemStatus === false or $instanceStatus === false or $systemStatus === 'insufficient-data' or $instanceStatus === 'insufficient-data') {
  echo "WARNING: Issue pulling data from AWS instance\n";
  exit(1);
} //if system status is ok and instance status ok then issue OK
elseif ($systemStatus === "ok" and $instanceStatus === "passed") {
  echo "OK: No issues with AWS instance for $hostname";
  exit(0);
}//if system status and instance status NOT OK print CRIT
elseif($systemStatus !=='ok' or $instanceStatus !== 'passed') {
  echo "CRIT: System Status =  $systemStatus and Instance Status = $instanceStatus";
  exit(2);
}
?>
