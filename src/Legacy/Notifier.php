<?php

namespace App\Legacy;

class Notifier {

	public const NOTIFIER_INFO = 0;
	public const NOTIFIER_ERROR = 1;
	public const NOTIFIER_DONE = 2;
	public const NOTIFIER_ALERT = 3;

	private static $msg = [];

	public static function getMessages(): array {

		return self::$msg;
	}

	public static function Add($msg, $code) {

		self::$msg[] = [$msg, $code];
	}

	public static function Clear() {

		self::$msg = [];
	}
}