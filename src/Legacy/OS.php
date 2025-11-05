<?php

namespace App\Legacy;

class OS extends Connection {

	public static function isWindows() {

        $isWindows = stristr( php_uname( 's' ), 'Windows' );

        return $isWindows;
	}
}