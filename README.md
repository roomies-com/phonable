# Roomies Phonable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/roomies/phonable.svg?style=flat-square)](https://packagist.org/packages/roomies/phonable)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/roomies-com/phonable/test.yml?branch=main&label=tests&style=flat-square)](https://github.com/roomies-com/phonable/actions?query=workflow%3Atest+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/roomies/phonable.svg?style=flat-square)](https://packagist.org/packages/roomies/phonable)

Roomies Phonable provides an abstraction layer to identify and verify phone numbers in your Laravel app. Phone verification can be used to help identify legitmate users of your app and also serve as a way to handle 2-factor authentication. Phonable provides implementations for a number of phone services including [Ding](https://ding.live), [Twilio](https://www.twilio.com), and [Vonage](https://vonage.com).

## Installation

You can install the package via Composer:

```bash
composer require roomies/phonable
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="phonable-config"
```

Read through the config file to understand the supported services and provide the correct configuration for your preferred services.

## Identification

Identification is a way to gather more information about a phone number including the country of origin, phone number type and more. This feature is supported by Ding and Vonage.

```php
// Return an instance of \Roomies\Phonable\Identification\IdentificationResult
$result = Identification::get('+12125550000');
```

Alternatively you can pass an object that implements `Roomies\Phonable\Contracts\PhoneIdentifiable` - `getIdentifiablePhoneNumber()` should return the phone number in E.164 format.

```php
use Roomies\Phonable\Facades\Identification;
use Roomies\Phonable\Contracts\PhoneIdentifiable;

class User implements PhoneIdentifiable
{
    /**
     * The identifiable phone number in E.164 format.
     */
    public function getIdentifiablePhoneNumber(): ?string
    {
        return '+12125550000';
    }
}

// Return an instance of \Roomies\Phonable\Identification\IdentificationResult
$result = Identification::get($user);
```

You can swap the driver out on the fly as necessary.

```php
use Roomies\Phonable\Facades\Identification;

Identification::driver('ding')->get($user);
```

## Verification

Verification is a two-step process in sending a generated code to a phone number that then needs to be entered back into your app to complete the process. This ensures your user has timely access to the phone number provided. This feature is supported by Ding, Twilio, and Vonage.



```php
// Return an instance of \Roomies\Phonable\Verification\VerificationRequest
$request = Verification::send('+12125550000');

// Return an instance of \Roomies\Phonable\Verification\VerificationResult
$result = Verification::verify($request->id, 'code');
```

Alternatively you can pass an object that implements `Roomies\Phonable\Contracts\PhoneVerifiable` - `getVerifiablePhoneNumber()` should return the phone number in E.164 format and `getVerifiableSession()` should return the previously stored verification request ID.

```php
use Roomies\Phonable\Facades\Verification;
use Roomies\Phonable\Contracts\PhoneIdentifiable;

class User implements PhoneVerifiable
{
    /**
     * The verifiable phone number in E.164 format.
     */
    public function getVerifiablePhoneNumber(): ?string
    {
        return '+12125550000';
    }

    /**
     * The current verification session identifier.
     */
    public function getVerifiableSession(): ?string
    {
        return $this->phone_verification_session;
    }
}

// Return an instance of \Roomies\Phonable\Verification\VerificationRequest
$request = Verification::send($user);

$user->update([
    'phone_verification_session' => $request->id,
]);
```

You will need to store the verification session ID as it will be used to complete the process.

When you receive the code from the user you can then call the `verify` method with the provided code. `Roomies\Phonable\Verification\VerificationResult` is a simple enum of the result.

```php
use Roomies\Phonable\Verification\VerificationResult;

// Return an instance of \Roomies\Phonable\Verification\VerificationResult
$result = Verification::verify($user, 1234);

if ($result === VerificationResult::Successful) {
    $user->update([
      'phone_verified_at' => now(),
      'phone_verification_session' => null,
    ]);
}
```

You can swap the driver out on the fly as necessary.

```php
use Roomies\Phonable\Facades\Verification;

Verification::driver('ding')->send($user);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
