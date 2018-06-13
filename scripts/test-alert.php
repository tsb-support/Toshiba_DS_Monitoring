#!/usr/bin/env php
<?php

$init_modules = array('alerts');
require __DIR__ . '/../includes/init.php';

$options = getopt('t:h:r:p:s:d::');

if ($options['r'] && $options['h']) {
    if (isset($options['d'])) {
        $debug = true;
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        ini_set('log_errors', 1);
        ini_set('error_reporting', 1);
    }
    $rule_id = (int)$options['r'];
    $device_id = ctype_digit($options['h']) ? $options['h'] : getidbyname($options['h']);
    $where = "alerts.device_id = $device_id && alerts.rule_id = $rule_id";
    $alerts = loadAlerts($where);
    if (empty($alerts)) {
        echo "No active alert found, please check that you have the correct ids";
        exit(2);
    }
    $alert = $alerts[0];

    $alert['details']['delay'] = 0;
    IssueAlert($alert);
} else {
    c_echo("
Info:
    Use this to send an actual alert via transports that is currently active.
Usage:
    -r Is the Rule ID.
    -h Is the device ID or hostname
    -d Debug
    
Example:
./scripts/test-alert.php -r 4 -d -h localhost

");
    exit(1);
}
