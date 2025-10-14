<?php

use database\Backup;

require "inc/config.inc.php";

$backup = new Backup();

$backup->Do();