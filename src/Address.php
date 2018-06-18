<?php

declare(strict_types = 1);

namespace SmartEmailing\Types;

use Consistence\Type\ObjectMixinTrait;

final class Address
{

	use ObjectMixinTrait;
	use ArrayExtractableTrait;

	/**
	 * @var string
	 */
	private $streetAndNumber;

	/**
	 * @var string
	 */
	private $town;

	/**
	 * @var \SmartEmailing\Types\ZipCode
	 */
	private $zipCode;

	/**
	 * @var \SmartEmailing\Types\Country
	 */
	private $country;

	/**
	 * @param mixed[] $data
	 */
	private function __construct(
		array $data
	) {
		$this->streetAndNumber = PrimitiveTypes::extractString($data, 'street_and_number');
		$this->town = PrimitiveTypes::extractString($data, 'town');
		$this->zipCode = ZipCode::extract($data, 'zip_code');
		$this->country = Country::extract($data, 'country');
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return [
			'street_and_number' => $this->streetAndNumber,
			'town' => $this->town,
			'zip_code' => $this->zipCode->getValue(),
			'country' => $this->country->getValue(),
		];
	}

	public function getStreetAndNumber(): string
	{
		return $this->streetAndNumber;
	}

	public function getTown(): string
	{
		return $this->town;
	}

	public function getZipCode(): ZipCode
	{
		return $this->zipCode;
	}

	public function getCountry(): Country
	{
		return $this->country;
	}

}
