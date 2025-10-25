<?php

use App\Legacy\Backup;

require "inc/config.inc.php";

$backup = new Backup();

$backup->Do();