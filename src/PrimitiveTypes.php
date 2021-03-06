<?php

declare(strict_types = 1);

namespace SmartEmailing\Types;

use Consistence\Type\ObjectMixinTrait;
use Nette\Utils\Validators;
use SmartEmailing\Types\Helpers\ExtractableHelpers;

abstract class PrimitiveTypes
{

	use ObjectMixinTrait;

	/**
	 * @param mixed $value
	 * @return int
	 */
	final public static function getInt(
		$value
	): int {
		if (Validators::isNumericInt($value)) {
			return (int) $value;
		}
		throw InvalidTypeException::typeError('int', $value);
	}

	/**
	 * @param mixed[] $data
	 * @param string $key
	 * @return int
	 * @throws \SmartEmailing\Types\InvalidTypeException
	 */
	final public static function extractInt(
		array $data,
		string $key
	): int {
		$value = ExtractableHelpers::extractValue($data, $key);
		try {
			return self::getInt($value);
		} catch (InvalidTypeException $e) {
			throw $e->wrap($key);
		}
	}

	/**
	 * @param mixed $value
	 * @return float
	 */
	final public static function getFloat(
		$value
	): float {
		if (\is_numeric($value)) {
			return (float) $value;
		}
		throw InvalidTypeException::typeError('float', $value);
	}

	/**
	 * @param mixed[] $data
	 * @param string $key
	 * @return float
	 */
	final public static function extractFloat(
		array $data,
		string $key
	): float {
		$value = ExtractableHelpers::extractValue($data, $key);
		try {
			return self::getFloat($value);
		} catch (InvalidTypeException $e) {
			throw $e->wrap($key);
		}
	}

	/**
	 * @param float[] $data
	 * @param string $key
	 * @return float
	 * @throws \SmartEmailing\Types\InvalidTypeException
	 */
	final public static function extractFloatOrNull(
		array $data,
		string $key
	): ?float {
		if (!isset($data[$key])) {
			return null;
		}
		return self::extractFloat($data, $key);
	}

	/**
	 * @param mixed[] $data
	 * @param string $key
	 * @return int
	 * @throws \SmartEmailing\Types\InvalidTypeException
	 */
	final public static function extractIntOrNull(
		array $data,
		string $key
	): ?int {
		if (!isset($data[$key])) {
			return null;
		}
		return self::extractInt($data, $key);
	}

	/**
	 * @param mixed[] $data
	 * @param string $key
	 * @return bool|null
	 * @throws \SmartEmailing\Types\InvalidTypeException
	 */
	final public static function extractBoolOrNull(
		array $data,
		string $key
	): ?bool {
		if (!isset($data[$key])) {
			return null;
		}
		return self::extractBool($data, $key);
	}

	/**
	 * @param mixed[] $data
	 * @param string $key
	 * @return string|null
	 * @throws \SmartEmailing\Types\InvalidTypeException
	 */
	final public static function extractStringOrNull(
		array $data,
		string $key
	): ?string {
		if (
			!isset($data[$key])
			|| $data[$key] === ''
		) {
			return null;
		}
		return self::extractString($data, $key);
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	final public static function getString(
		$value
	): string {
		if (\is_scalar($value)) {
			return (string) $value;
		}
		throw InvalidTypeException::typeError('string', $value);
	}

	/**
	 * @param mixed[] $data
	 * @param string $key
	 * @return string
	 * @throws \SmartEmailing\Types\InvalidTypeException
	 */
	final public static function extractString(
		array $data,
		string $key
	): string {
		$value = ExtractableHelpers::extractValue($data, $key);
		try {
			return self::getString($value);
		} catch (InvalidTypeException $e) {
			throw $e->wrap($key);
		}
	}

	/**
	 * @param mixed $value
	 * @return bool
	 */
	final public static function getBool(
		$value
	): bool {
		if (\is_bool($value)) {
			return $value;
		}

		if (\in_array($value, [false, 0, '0', 'false'], true)) {
			return false;
		}

		if (\in_array($value, [true, 1, '1', 'true'], true)) {
			return true;
		}

		throw InvalidTypeException::typeError('bool', $value);
	}

	/**
	 * @param mixed $value
	 * @return mixed[]
	 */
	final public static function getArray(
		$value
	): array {
		if (\is_array($value)) {
			return $value;
		}
		throw InvalidTypeException::typeError('array', $value);
	}

	/**
	 * @param mixed[] $data
	 * @param string $key
	 * @return bool
	 * @throws \SmartEmailing\Types\InvalidTypeException
	 */
	final public static function extractBool(
		array $data,
		string $key
	): bool {
		$value = ExtractableHelpers::extractValue($data, $key);
		try {
			return self::getBool($value);
		} catch (InvalidTypeException $e) {
			throw $e->wrap($key);
		}
	}

	/**
	 * @param mixed[] $data
	 * @param string $key
	 * @return string[]
	 * @throws \SmartEmailing\Types\InvalidTypeException
	 */
	final public static function extractStringArray(
		array $data,
		string $key
	): array {
		$value = ExtractableHelpers::extractValue($data, $key);
		try {
			$stringArray = self::getArray($value);
			$return = [];
			foreach ($stringArray as $index => $item) {
				$return[$index] = self::getString($item);
			}
			return $return;
		} catch (InvalidTypeException $e) {
			throw $e->wrap($key);
		}
	}

	/**
	 * Preserves keys
	 *
	 * @param mixed[] $data
	 * @param string $key
	 * @return mixed[]
	 * @throws \SmartEmailing\Types\InvalidTypeException
	 */
	final public static function extractArray(array &$data, string $key): array
	{
		$value = ExtractableHelpers::extractValue($data, $key);
		try {
			return self::getArray($value);
		} catch (InvalidTypeException $e) {
			throw $e->wrap($key);
		}
	}

	/**
	 * Preserves keys
	 *
	 * @param mixed[] $data
	 * @param string $key
	 * @return mixed[]|null
	 * @throws \SmartEmailing\Types\InvalidTypeException
	 */
	final public static function extractArrayOrNull(array &$data, string $key): ?array
	{
		if (!isset($data[$key]) || $data[$key] === null) {
			return null;
		}

		return self::extractArray($data, $key);
	}

}
