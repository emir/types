<?php

declare(strict_types = 1);

namespace SmartEmailing\Types;

use Consistence\Type\ObjectMixinTrait;
use Nette\Utils\Strings;
use SmartEmailing\Types\ExtractableTraits\ArrayExtractableTrait;

final class Duration
{

	use ObjectMixinTrait;
	use ArrayExtractableTrait;

	public const MAX_VALUE = 1000000;

	/**
	 * @var int
	 */
	private $value;

	/**
	 * @var \SmartEmailing\Types\TimeUnit
	 */
	private $unit;

	/**
	 * @param mixed[] $data
	 */
	private function __construct(
		array $data
	) {
		$value = PrimitiveTypes::extractInt($data, 'value');
		if (\abs($value) > self::MAX_VALUE) {
			throw new InvalidTypeException('Value is out of range: [-' . self::MAX_VALUE . ', ' . self::MAX_VALUE . '].');
		}
		$this->value = $value;
		$this->unit = TimeUnit::extract($data, 'unit');
	}

	public static function fromDateTimeModify(string $dateTimeModify): self
	{
		$matches = Strings::match($dateTimeModify, '/^(-?|\+?)(\d+)\s+(.+)/');

		if (!$matches) {
			throw new InvalidTypeException('Duration: ' . $dateTimeModify . '  is not in valid format.');
		}

		$value = PrimitiveTypes::extractInt($matches, '2');
		$unit = TimeUnit::extract($matches, '3');

		if ($matches[1] === '-') {
			$value *= -1;
		}

		return new self([
			'value' => $value,
			'unit' => $unit->getValue(),
		]);
	}

	public function getValue(): int
	{
		return $this->value;
	}

	public function getUnit(): TimeUnit
	{
		return $this->unit;
	}

	public function getDateTimeModify(): string
	{
		return $this->value . ' ' . $this->unit->getValue();
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return [
			'value' => $this->value,
			'unit' => $this->unit->getValue(),
		];
	}

}
