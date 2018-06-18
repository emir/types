<?php

declare(strict_types = 1);

namespace SmartEmailing\Types;

use Consistence\Type\ObjectMixinTrait;
use SmartEmailing\Types\Emailaddress;
use SmartEmailing\Types\InvalidTypeException;
use SmartEmailing\Types\IpAddress;
use SmartEmailing\Types\Helpers\UniqueToStringArray;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

final class UniqueToStringArrayTest extends TestCase
{

	use ObjectMixinTrait;

	public function test1(): void
	{

		$invalidValues = [
			[
				1,
				2,
				new \stdClass(),
			],
			[
				1,
				2,
				[],
			],
			[
				Emailaddress::from('test@smartemailing.cz'),
				Emailaddress::from('test2@smartemailing.cz'),
				IpAddress::from('8.8.8.8'),
			],
		];

		foreach ($invalidValues as $invalidValue) {
			Assert::throws(
				function () use ($invalidValue): void {
					UniqueToStringArray::from($invalidValue);
				},
				InvalidTypeException::class
			);
		}

		$validValues = [

			[
				Emailaddress::from('test@smartemailing.cz'),
				Emailaddress::from('test2@smartemailing.cz'),
			],
			[
				IpAddress::from('8.8.8.8'),
				IpAddress::from('8.8.4.4'),
			],
		];

		foreach ($validValues as $validValue) {
			$intArray = UniqueToStringArray::from($validValue);
			Assert::type(UniqueToStringArray::class, $intArray);
		}

		$ip1 = IpAddress::from('8.8.4.4');
		$append = UniqueToStringArray::from([$ip1]);
		Assert::equal([$ip1], $append->getValues());

		$ip2 = IpAddress::from('8.8.4.5');
		$append->add($ip2);
		Assert::equal([$ip1, $ip2], $append->getValues());

		$append->remove(IpAddress::from('100.8.4.5'));
		Assert::equal([$ip1, $ip2], $append->getValues());

		$append->remove($ip1);
		Assert::equal([$ip2], $append->getValues());

		$empty = UniqueToStringArray::extractOrEmpty(
			[

			],
			'not_existing'
		);

		Assert::type(UniqueToStringArray::class, $empty);
		Assert::count(0, $empty);

		$data = [
			'data' => $empty,
		];
		$derived = \SmartEmailing\Types\Helpers\UniqueToStringArray::extract(
			$data,
			'data'
		);
		Assert::type(\SmartEmailing\Types\Helpers\UniqueToStringArray::class, $derived);

		$containsTest = \SmartEmailing\Types\Helpers\UniqueToStringArray::from(
			[
				IpAddress::from('8.8.8.9'),
			]
		);
		Assert::true($containsTest->contains(IpAddress::from('8.8.8.9')));
		Assert::false($containsTest->contains(IpAddress::from('8.8.8.10')));

		$containsTest->add(IpAddress::from('8.8.8.10'));
		Assert::true($containsTest->contains(IpAddress::from('8.8.8.10')));

		$containsTest->remove(IpAddress::from('8.8.8.9'));
		Assert::false($containsTest->contains(IpAddress::from('8.8.8.9')));
	}

}

(new UniqueToStringArrayTest())->run();
