<?php

namespace database;

class OS extends Connection {

	public static function isWindows() {

        $isWindows = stristr( php_uname( 's' ), 'Windows' );

        return $isWindows;
	}
}