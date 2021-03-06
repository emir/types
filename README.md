# SmartEmailing \ Types 

### Missing data types for PHP 7.1. Highly extendable.

[![Monthly Downloads](https://poser.pugx.org/smartemailing/types/d/monthly)](https://packagist.org/packages/smartemailing/types)
[![License](https://poser.pugx.org/smartemailing/types/license)](https://packagist.org/packages/smartemailing/types)
[![Latest Stable Version](https://poser.pugx.org/smartemailing/types/v/stable)](https://packagist.org/packages/smartemailing/types)

[![codecov](https://codecov.io/gh/smartemailing/types/branch/master/graph/badge.svg)](https://codecov.io/gh/smartemailing/types)
[![CircleCI](https://circleci.com/gh/smartemailing/types.svg?style=shield)](https://circleci.com/gh/smartemailing/types)
[![CodeClimate](https://img.shields.io/codeclimate/maintainability/smartemailing/types.svg)](https://codeclimate.com/github/smartemailing/types/maintainability)


Neverending data validation can be tiresome. Either you have to validate your data 
over and over again in every function you use it, or you have to rely 
it has already been validated somewhere else and risk potential errors. Smelly, right?

Replacing validation hell with **Types** will make 
your code much more readable and less vulnerable to bugs.

**Types** wrap your data in value objects that are guaranteed to be 
**valid and normalized; or not to exist at all**. 
It allows you to use specific type hints instead of primitive types or arrays.
Your code will be unbreakable and your IDE will love it.

![](./docs/img/ide-love-2.png)


## Table of Contents

- [SmartEmailing \ Types](#smartemailing---types)
  * [Installation](#installation)
  * [How does it work](#how-does-it-work)
    + [Wrapping raw value](#wrapping-raw-value)
    + [Extraction from array](#extraction-from-array)
  * [String-extractable types](#string-extractable-types)
    + [E-mail address](#e-mail-address)
    + [Domain](#domain)
    + [Hex 32](#hex-32)
    + [GUID](#guid)
    + [IP address](#ip-address)
    + [URL](#url)
    + [Company registration number](#company-registration-number)
    + [Phone number](#phone-number)
    + [ZIP code](#zip-code)
    + [JSON](#json)
    + [Base 64](#base-64)
    + [Iban](#iban)
    + [SwiftBic](#swiftbic)
    + [VatId](#vatid)
    + [CurrencyCode](#currencycode)
    + [CountryCode](#countrycode)
  * [Int-extractable types](#int-extractable-types)
    + [Port](#port)
  * [Float-extractable types](#float-extractable-types)
    + [Part](#part)
    + [Sigmoid function value](#sigmoid-function-value)
    + [Rectified Linear Unit function value](#rectified-linear-unit-function-value)
  * [Array-extractable types](#array-extractable-types)
    + [DateTimeRange](#datetimerange)
    + [Duration](#duration)
    + [Address](#address)
    + [Price](#price)
  * [Array-types](#array-types)
    + [UniqueIntArray](#uniqueintarray)
    + [UniqueStringArray](#uniquestringarray)
  * [Enum-extractable types](#enum-extractable-types)
    + [Lawful Basis For Processing](#lawful-basis-for-processing)
    + [Country code](#country-code)
    + [Currency code](#currency-code)
    + [Field of Application](#field-of-application)
    + [Time unit](#time-unit)
    + [Relation](#relation)

## Installation

The recommended way to install is via Composer:

```
composer require smartemailing/types
```

## How does it work

It is easy. You just initialize desired value object by simple one-liner. 
From this point, you have sanitized, normalized and valid data; or `SmartEmailing\Types\InvalidTypeException` to handle.

**Types** consist from:

- String-extractable types - validated strings (E-mail address, Domains, Hexadecimal strings, ...)
- Int-extractable types - validated integers (Port) 
- Float-extractable types - validated floats (SigmoidValue, Part, ...) 
- Enum-extractable types - enumerables (CountryCode, CurrencyCode, GDPR's Lawful purpose, ...)
- Composite (Array-extractable) types - structures containing multiple another types (Address, ...)
- DateTimes - extraction of DateTime and DateTimeImmutable
- Primitive types extractors and unique arrays

Different types provide different methods related to them, but all types share this extraction API:

### Wrapping raw value

```php
<?php

declare(strict_types = 1);

use SmartEmailing\Types\Emailaddress;
use SmartEmailing\Types\InvalidTypeException;

// Valid input

$emailaddress = Emailaddress::from('hello@gmail.com'); // returns Emailaddress object
$emailaddress = Emailaddress::from($emailaddress); // returns original $emailaddress

// Invalid input

$emailaddress = Emailaddress::from('bla bla'); // throws InvalidTypeException
$emailaddress = Emailaddress::from(1); // throws InvalidTypeException
$emailaddress = Emailaddress::from(false); // throws InvalidTypeException
$emailaddress = Emailaddress::from(null); // throws InvalidTypeException
$emailaddress = Emailaddress::from([]); // throws InvalidTypeException
$emailaddress = Emailaddress::from(new \StdClass()); // throws InvalidTypeException

// Nullables

$emailaddress = Emailaddress::fromOrNull(null); // returns NULL
$emailaddress = Emailaddress::fromOrNull('bla bla'); // throws InvalidTypeException
$emailaddress = Emailaddress::fromOrNull('bla bla', true); // returns null instead of throwing

```

### Extraction from array

This is really useful for strict-typing (validation) multidimensional arrays like API requests, forms or database data.

```php
<?php

use SmartEmailing\Types\Emailaddress;
use SmartEmailing\Types\InvalidTypeException;

$input = [
	'emailaddress' => 'hello@gmail.com',
	'already_types_emailaddress' => Emailaddress::from('hello2@gmail.com'),
	'invalid_data' => 'bla bla',
];

// Valid input

$emailaddress = Emailaddress::extract($input, 'emailaddress'); // returns Emailaddress object
$emailaddress = Emailaddress::extract($input, 'already_types_emailaddress'); // returns original Emailaddress object

// Invalid input

$emailaddress = Emailaddress::extract($input, 'invalid_data'); // throws InvalidTypeException
$emailaddress = Emailaddress::extract($input, 'not_existing_key'); // throws InvalidTypeException

// Nullables 

$emailaddress = Emailaddress::extractOrNull($input, 'not_existing_key'); // returns null
$emailaddress = Emailaddress::extractOrNull($input, 'invalid_data'); //  throws InvalidTypeException
$emailaddress = Emailaddress::extractOrNull($input, 'invalid_data', true); // returns null instead of throwing

// Default values
$emailaddress 
	= Emailaddress::extractOrNull($input, 'not_existing_key') 
	?? Emailaddress::from('default@domain.com'); 
	// uses null coalescing operator to assign default value if key not present or null

$emailaddress 
	= Emailaddress::extractOrNull($input, 'not_existing_key', true) 
	?? Emailaddress::from('default@domain.com'); 
	// uses null coalescing operator to assign default value if key not present or null or invalid


```

## String-extractable types

String-extractable types are based on validated strings. All values are trimmed before validation.

They can be easily converted back to string by string-type casting or calling `$type->getValue()`.

### E-mail address

`SmartEmailing\Types\Emailaddress`

Lowercased and ASCII-transformed e-mail address (`hello@gmail.com`)

Type-specific methods:
- `getLocalPart() : string` returns local part of e-mail address (`hello`)
- `getDomain() : \SmartEmailing\Types\Domain` returns domain part (`gmail.com`, represented as `Types\Domain`)

### Domain

`SmartEmailing\Types\Domain`

Lowercased domain name (`mx1.googlemx.google.com`)

Type-specific methods:
- `getSecondLevelDomain() : \SmartEmailing\Types\Domain` returns second-level domain. (`google.com`)


### Hex 32

`SmartEmailing\Types\Hex32`

Lowercased 32-characters long hexadecimal string useful as container for MD5 or UUID without dashes. (`741ecf779c9244358e6b85975bd13452`)


### GUID

`SmartEmailing\Types\Guid`

Lowercased Guid with dashes (`741ecf77-9c92-4435-8e6b-85975bd13452`)

### IP address

`SmartEmailing\Types\IpAddress`

IP address v4 or v6. (`127.0.0.1`, `[2001:0db8:0a0b:12f0:0000:0000:0000:0001]`, `2001:db8:a0b:12f0::1`)

Type-specific methods:
- `getVersion() : int` returns IP address version, `4` or `6`

### URL

`SmartEmailing\Types\UrlType`

URL based on `Nette\Http\Url` (`https://www.google.com/search?q=all+work+and+no+play+makes+jack+a+dull+boy`)

Type-specific methods:
- `getAuthority() : string` returns authority (`www.google.com`)
- `getHost() : string` returns Host (`www.google.com`)
- `getQueryString() : string` returns Query string (`q=all%20work%20and%20no%20play%20makes%20jack%20a%20dull%20boy`)
- `getPath() : string` returns URl Path (`/search`)
- `getAbsoluteUrl() : string` Complete URL as `string`, alias for `getValue()` 
- `getQueryParameter(string $name, mixed $default = null): mixed` Return value of parameter `$name`
- `getBaseUrl(): string` Return URL without path, query string and hash part (`https://www.google.cz/`)
- `getScheme(): string` Return URL scheme (`https`)
- `hasParameters(string[] $names): bool` Returns `true` if URL parameters contain all parameters defined in `$names` array
- `getParameters(): array` Returns all URL parameters as string-indexed array
- `withQueryParameter(string $name, mixed $value): UrlType` Returns new instance with added query parameter.

### Company registration number

`SmartEmailing\Types\CompanyRegistrationNumber`

Whitespace-free company registration number for following countries: 
`CZ`, `SK`, `CY`

### Phone number

`SmartEmailing\Types\PhoneNumber`

Whitespace-free phone number in international format for following countries: 
`CZ`, `SK`, `AT`, `BE`, `FR`, `HU`, `GB`, `DE`, `US`, `PL`, `IT`, `SE`, `SI`, `MH`, `NL`, `CY`, `IE`, `DK`, `FI`, `LU`, `TR`

Type-specific methods:
- `getCountry() : SmartEmailing\Types\Country` Originating country (`CZ`)


### ZIP code

`SmartEmailing\Types\ZipCode`

Whitespace-free ZIP code valid in following countries: 
`CZ`, `SK`, `UK`, `US`


### JSON

`SmartEmailing\Types\JsonString`

Valid JSON-encoded data as string

Type-specific methods:
- `static encode(mixed $data) : SmartEmailing\Types\JsonString` create JsonString from raw data
- `getDecodedValue() : mixed` decode JsonString back to raw data

### Base 64

`SmartEmailing\Types\Base64String`

Valid Base 64-encoded data as string

Type-specific methods:
- `static encode(string $value) : SmartEmailing\Types\Base64String` create Base64String from string
- `getDecodedValue() : string` decode Base64String back to original string


### Iban

`SmartEmailing\Types\Iban`

Type-specific methods:
- `getFormatted(string $type = SmartEmailing\Types\Iban::FORMAT_ELECTRONIC): string` returns formatted Iban string. Format types: `FORMAT_ELECTRONIC`, `FORMAT_PRINT`.
- `getCountry(): SmartEmailing\Types\Country`
- `getChecksum(): int` 

### SwiftBic

`SmartEmailing\Types\SwiftBic`

Valid Swift/Bic codes.

### VatId
`SmartEmailing\Types\VatId`

Type-specific methods:
- `static isValid(string $vatId): bool` returns true if the vat id is valid otherwise returns false
- `getCountry(): ?Country` returns `Country` under which the subject should falls or null.
- `getPrefix(): ?string` returns string that prefixing vat id like `EL` from `EL123456789` or null.
- `getVatNumber(): string` returns vat number without prefix like `123456789`
- `getValue(): string` return whole vat id `EL123456789`

### CurrencyCode
`SmartEmailing\Types\CurrencyCode` 

Valid currency codes by ISO 4217

### CountryCode
`SmartEmailing\Types\CountryCode`

Valid country codes by ISO 3166-1 alpha-2


## Int-extractable types

Int-extractable types are based on validated integers.

They can be easily converted back to int by int-type casting or calling `$type->getValue()`.

### Port

`SmartEmailing\Types\Port`

Port number

Integer interval, `<0, 65535>`

## Float-extractable types

Float-extractable types are based on validated floats.

They can be easily converted back to float by float-type casting or calling `$type->getValue()`.

### Part

`SmartEmailing\Types\Part`

Portion of the whole

Float interval `<0.0, 1.0>`

Type-specific methods:
- `static fromRatio(float $value, float $whole): Part` creates new instance by division `$value` and `$whole`.
-  `getPercent(): float` returns `(Ratio's value) * 100` to get percent representation

### Sigmoid function value

`SmartEmailing\Types\SigmoidValue`

Result of Sigmoid function, useful when building neural networks.

Float interval `<-1.0, 1.0>`. 

### Rectified Linear Unit function value

`SmartEmailing\Types\ReLUValue`

Result of Rectified Linear Unit function, useful when building neural networks.

Float interval `<0.0, Infinity)`. 


## Array-extractable types

Array-extractable types are composite types encapsulating one or more another types.
They are created from associative array. All Array-extractable types implement method 
`toArray() : array` which returns normalized array or type's data.

### DateTimeRange

`SmartEmailing\Types\DateTimeRange`

Range between two `\DateTimeInterface`s

Can be created from:

```php
DateTimeRange::from(
	[
		'from' => 'YYYY-MM-DD HH:MM:SS',
		'to' => 'YYYY-MM-DD HH:MM:SS',
	]
)
```
Type-specific methods:
- `getFrom(): \DateTimeImmutable` returns `From` date and time as `\DateTimeImmutable` instance
- `getTo(): \DateTimeImmutable` returns `To` date and time as `\DateTimeImmutable` instance
- `getDurationInSeconds(): int` returns number of seconds between `From` and `To` dates
- `contains(\DateTimeInterface $dateTime): bool` returns `true` if provided `\DateTimeInterface` lies between `From` and `To` dates.

### Duration

`SmartEmailing\Types\Duration`

Human-readable time interval.

Can be created from:

```php
Duration::from(
	[
		'value' => 1,
		'unit' => TimeUnit::HOURS,
	]
);
```
Type-specific methods:
- `getDateTimeModify(): string` returns string that is compatible with `\DateTime::modify()` and `\DateTimeImmutable::modify()`
- `getUnit(): TimeUnit` returns `TimeUnit` enum type
- `getValue() int` returns number of units
- `static fromDateTimeModify(string $dateTimeModify): self` creates new instance from string compatible with `\DateTime::modify()` and `\DateTimeImmutable::modify()`


### Address

`SmartEmailing\Types\Address`

Location address cotaining street and number, town, zip code and country.

Can be created from:

```php
Address::from(
	[
		'street_and_number' => '29 Neibolt Street',
		'town' => 'Derry',
		'zip_code' => '03038',
		'country' => 'US',
	]
);
```
Type-specific methods:
- `getStreetAndNumber(): string` returns street and number
- `getTown(): string` returns Town
- `getZipCode(): ZipCode` returns ZipCode instance
- `getCountry(): CountryCode` returns CountryCode instance

### Price

`SmartEmailing\Types\Price`

Price object containing number of currency units with VAT, number of currency units without VAT and currency.

Can be created from:

```php
Price::from(
	[
		'with_vat' => 432.1,
		'without_vat' => 123.45,
		'currency' => CurrencyCode::EUR,
	]
);
```
Type-specific methods:
- `getWithoutVat(): float` returns price without VAT
- `getWithVat(): float` returns price with VAT
- `getCurrency(): CurrencyCode` returns CurrencyCode instance

## Array-types

`Types` provide another kind of Array-extractable types: Unique primitive-type arrays.
Their purpose is to hold unique set of primitives. 
They implement `\Countable` and `\IteratorAggregate` and natively support
set operations.

All Array-types share following features:
- `static empty() : self` Creates new empty instance of desired array-type.
- `split(int $chunkSize): self[]` Splits current instance into array of several instances, each with maximum data-set size of `$chunkSize`.
- `merge(self $toBeMerged): self` Returns new instance with data-set combined from parent and `$toBeMerged` instances. Both source instances stay unchanged. 
- `deduct(self $toBeDeducted): self` Returns new instance with data-set containing all items from parent that are not contained in `$toBeDeducted`. Both source instances stay unchanged. 
- `count(): int` Returns data-set size.
- `isEmpty(): bool` Returns `true` if data-set is empty, `false` otherwise.

Array-types-specific extractors:
- `static extractOrEmpty(array $data, string $key): self` Behaves like standard `::extract()` method, but returns empty set when `$data[$key]` is `null` or not set.
- `static extractNotEmpty(array $data, string $key): self` Behaves like standard `::extract()` method, but throws `InvalidTypeException` when `$data[$key]` is not set, `null` or empty array.

### UniqueIntArray

`SmartEmailing\Types\UniqueIntArray`

UniqueIntArray is able to hold unique set of integers. 

Can be created from:

```php
// duplicate values will be discarted
// keys are ignored

UniqueIntArray::from(
	[
		1, 2, 2, 3, 3, 3, 4 
	]
);
```

Type-specific methods:
- `getValues(): int[]` Returns data-set of unique integers as array.
- `toArray(): int[]` Is just alias for `getValues()`.
- `add(int $id): bool` Adds another integer to the data-set. Returns `false` if integer has already been there.
- `remove(int $id): void` Removes integer from the data-set, if present.
- `contains(int $id): bool` Returns `true` if `$id` is contained in the data-set, `false` otherwise.

### UniqueStringArray

`SmartEmailing\Types\UniqueIntArray`

UniqueStringArray is able to hold unique set of strings. 

Can be created from:

```php
// duplicate values will be discarted
// keys are ignored

UniqueStringArray::from(
	[
		'a', 
		'b', 
		'c', 
		'all work and no play makes jack a dull boy',
		'all work and no play makes jack a dull boy',
		'all work and no play makes jack a dull boy',
	]
);
```

Type-specific methods:
- `getValues(): string[]` Returns data-set of unique strings as array.
- `toArray(): string[]` Is just alias for `getValues()`.
- `add(string $id): bool` Adds another string to the data-set. Returns `false` if string has already been there.
- `remove(string $id): void` Removes string from the data-set, if present.
- `contains(string $id): bool` Returns `true` if `$id` is contained in the set, `false` otherwise.


## Enum-extractable types

Enum-extractable types are types that can contain single value from defined set. They are based on kkk

All Enum-extractable types share following features:
- `getValue() : string` Returns enum-value
- `equals(self $enum): bool` Returns `true` if `$enum` contains same value as parent. 
- `equalsValue(string $value): self` Returns `true` if parent contains the same value as `$value`.

Enums can be created using standard extractors or using their constants:
```php
CurrencyCode::from(
	CurrencyCode::EUR
);
CurrencyCode::from(
	'EUR'
);
```


### Lawful Basis For Processing

`SmartEmailing\Types\LawfulBasisForProcessing`

GDPR's lawful basis for processing

[Available values](./src/LawfulBasisForProcessing.php)

### Country code

`SmartEmailing\Types\CountryCode`

ISO-3166-1 Alpha 2 country code

[Available values](./src/CountryCode.php)

### Currency code

`SmartEmailing\Types\CurrencyCode`

ISO-4217 three-letter currency code

[Available values](./src/CurrencyCode.php)

### Field of Application

`SmartEmailing\Types\FieldOfApplication`

Most common fields of human applications.

[Available values](./src/FieldOfApplication.php)

### Time unit

`SmartEmailing\Types\TimeUnit`

Time unit compatible with `\DateTime::modify()` argument format

[Available values](./src/TimeUnit.php)

### Relation

`SmartEmailing\Types\Relation`

Represents Relation or Gate - AND / OR

[Available values](./src/Relation.php)
