<?php

namespace Wame\Core\Doctrine\Types;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class NeonType extends \Doctrine\DBAL\Types\StringType {

	const TYPE_NAME = "neon";

	/**
	 * Converts a value from its PHP representation to its database representation
	 * of this type.
	 *
	 * @param mixed                                     $value    The value to convert.
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
	 *
	 * @return mixed The database representation of the value.
	 */
	public function convertToDatabaseValue($value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform) {
		if ($value) {
			return \Nette\Neon\Neon::encode($value);
		}
	}

	/**
	 * Converts a value from its database representation to its PHP representation
	 * of this type.
	 *
	 * @param mixed                                     $value    The value to convert.
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
	 *
	 * @return mixed The PHP representation of the value.
	 */
	public function convertToPHPValue($value, \Doctrine\DBAL\Platforms\AbstractPlatform $platform) {
		$out = \Nette\Neon\Neon::decode($value);
		if ($out) {
			return $out;
		} else {
			return [];
		}
	}

	public function getName() {
		return self::TYPE_NAME;
	}

}
