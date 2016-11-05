<?php

namespace Core\Model\Enum;

abstract class BaseEnum
{
	/**
	 * @return array for selectboxes - values as keys
	 */
	public static function getList(){

		$constants = self::getConstants();

		foreach($constants AS $key => $val) {
			if (!is_scalar($val) || $val === NULL) {
				// null value is unusable for array_flip and for selectboxes
				$constants[$key] = '';
			}
		}

		return array_flip($constants);
	}

	/**
	 * @return array
	 */
	public static function getConstants(){
		$reflection = new \ReflectionClass(get_called_class());
		return $reflection->getConstants();
	}
}
