<?php

namespace database;

class Notifier {

	const NOTIFIER_INFO = 0;
	const NOTIFIER_ERROR = 1;
	const NOTIFIER_DONE = 2;
	const NOTIFIER_ALERT = 3;

	static $msg = [];

	public static function getMessages() {

		return self::$msg;
	}

	public static function Add($msg, $code) {

		self::$msg[] = [$msg, $code];
	}

	public static function Clear() {

		self::$msg = [];
	}
}