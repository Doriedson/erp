<?php

namespace App\Legacy;

class Session {

	private static function start(){
		//Start session
		session_start();
	}

	private static function close(){
		//Write session data and end session
		session_write_close();
	}

	public static function reset(){
		self::start();
		//Free all sessions variables
		session_unset();
		//Destroy all data registered to a session
		session_destroy();
		self::close();
	}

	public static function get($var){
		self::start();
		if (self::exist($var)) {
			$value = $_SESSION[$var];
		} else {
			$value = null;
		}
		self::close();
		return $value;
	}

	public static function set($var, $value){

		self::start();
		$_SESSION[$var] = $value;
		self::close();
	}

	public static function exist($var){
		return isset($_SESSION[$var]);
	}
}
?>