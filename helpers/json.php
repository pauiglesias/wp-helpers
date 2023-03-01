<?php

namespace MicroDeploy\Package\Helpers;

/**
 * JSON class
 *
 * Fail-safe encoding/decoding of json strings.
 *
 * @package		WordPress
 * @subpackage	Helpers
 * @version		1.0.0
 */
class Json {



	/**
	 * Encoding flags
	 */
	private static $encodeFlags = JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_QUOT|JSON_HEX_APOS;



	/**
	 * Decoding replace
	 */
	private static $decodeReplace = [
		['\u0022', 	'\u0027'],
		["\\\"", 	"'"	],
	];



	/**
	 * Encode with default or custom flags
	 */
	public static function encode($value, $flags = null, $depth = 512) {
		return isset($flags) ? @json_encode($value, $flags, $depth) : @json_encode($value, self::$encodeFlags, $depth);
	}



	/**
	 * Decode replacing special chars
	 */
	public static function decode($json, $associative = true, $depth = 512, $flags = 0) {
		$decodeReplace = self::decodeReplace();
		$json = str_replace($decodeReplace[0], $decodeReplace[1], $json);
		return @json_decode($json, $associative, $depth, $flags);
	}



	/**
	 * Retrieves or replaces the default decode replace array
	 */
	public static function decodeReplace($overwriting = null) {

		static $overwrited = null;
		if (isset($overwriting)) {
			$overwrited = false === $overwriting ? null : $overwriting;
		}

		if (isset($overwrited)) {
			return $overwrited;
		}

		return self::$decodeReplace;
	}



}