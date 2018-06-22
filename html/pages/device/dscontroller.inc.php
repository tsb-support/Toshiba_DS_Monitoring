<?php
/*
 * LibreNMS device MIB browser
 *
 * Copyright (c) 2015 Gear Consulting Pty Ltd <github@libertysys.com.au>
 *
 * Author: Paul Gear
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.  Please see LICENSE.txt at the top level of
 * the source code distribution for details.
 */

if (!isset($vars['section'])) {
    $vars['section'] = "dscontroller";
}
//if (is_mib_poller_enabled($device)) {


echo('<h4><i class="fa fa-file-text-o"></i> Toshiba DS Controller</h4>');

if (!isset($conf['ds_controller_url'])) {
    echo('<iframe style="width:100%;height:1000px;border: 0px solid white" src="'. $config['ds_controller_url']. '/#!/main?ip='. $device['hostname'] .'"></iframe>');
}
else 
{
    print_ds_controller_disabled();
}
//} else {
//    print_mib_poller_disabled();
//}
