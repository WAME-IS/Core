<?php

namespace Wame\Core\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Nette\Neon\Neon;

/**
 * @author Dominik Gmiterko <ienze@ienze.me>
 */
class NeonType extends StringType
{
	const TYPE_NAME = "neon";


	/**
	 * Converts a value from its PHP representation to its database representation
	 * of this type.
	 *
	 * @param mixed $value The value to convert.
	 * @param AbstractPlatform $platform The currently used database platform.
	 *
	 * @return mixed The database representation of the value.
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
		if ($value) {
			return Neon::encode($value);
		}
	}

	/**
	 * Converts a value from its database representation to its PHP representation
	 * of this type.
	 *
	 * @param mixed $value The value to convert.
	 * @param AbstractPlatform $platform The currently used database platform.
	 *
	 * @return mixed The PHP representation of the value.
	 */
	public function convertToPHPValue($value, AbstractPlatform $platform)
    {
		if ($value) {
			return Neon::decode($value);
		}
		return [];
	}

	public function getName()
    {
		return self::TYPE_NAME;
	}

}
