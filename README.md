# :telephone_receiver: PHP library for integrating with [sipgate](https://www.sipgate.com)

[![Latest Stable Version](https://poser.pugx.org/orkhanahmadov/sipgate/v/stable)](https://packagist.org/packages/orkhanahmadov/sipgate)
[![Latest Unstable Version](https://poser.pugx.org/orkhanahmadov/sipgate/v/unstable)](https://packagist.org/packages/orkhanahmadov/sipgate)
[![GitHub license](https://img.shields.io/github/license/orkhanahmadov/sipgate.svg)](https://github.com/orkhanahmadov/sipgate/blob/master/LICENSE.md)

[![Build Status](https://img.shields.io/travis/orkhanahmadov/sipgate.svg)](https://travis-ci.org/orkhanahmadov/sipgate)
[![Test Coverage](https://img.shields.io/codeclimate/coverage/orkhanahmadov/sipgate.svg)](https://codeclimate.com/github/orkhanahmadov/sipgate/test_coverage)
[![Maintainability](https://img.shields.io/codeclimate/maintainability/orkhanahmadov/sipgate.svg)](https://codeclimate.com/github/orkhanahmadov/sipgate/maintainability)
[![Quality Score](https://img.shields.io/scrutinizer/g/orkhanahmadov/sipgate.svg)](https://scrutinizer-ci.com/g/orkhanahmadov/sipgate)
[![StyleCI](https://github.styleci.io/repos/185805106/shield?branch=master)](https://github.styleci.io/repos/185805106)

## Requirements

**PHP 7.2** or higher and ``json`` extension.

## Installation

``` bash
composer require innoscripta/sipgate
```

## Usage

Initialize `Sipgate` class:
``` php
$sipgate = \Orkhanahmadov\Sipgate\Sipgate();
```

**Basic authentication**

You can pass basic authentication username and password when initializing class:
``` php
$sipgate = \Orkhanahmadov\Sipgate\Sipgate('example@example.com', 'secret');
```

Or you can set basic authentication with `setBasicAuthCredentials()` method:
``` php
$sipgate = \Orkhanahmadov\Sipgate\Sipgate();
$sipgate->setBasicAuthCredentials('example@example.com', 'secret');
```

**Account information:**

``` php
$sipgate->account();
```
Returns array of account details.

**Get users:**

``` php
$sipgate->users();
```
Returns array of users registered under account. Each item in array is instance of `Orkhanahmadov\Sipgate\Resources\User` and has following properties:

``` php
$user->id; // string
$user->firstname; // string
$user->lastname; // string
$user->email; // string
$user->defaultDevice; // string
$user->busyOnBusy; // bool
$user->addressId; // string
$user->directDialIds; // array
$user->admin; // bool
```

**Get user devices:**

To get user's devices use `devices()` method and pass a user instance or user ID.

``` php
$sipgate->devices($user);
// or
$sipgate->devices('ABC-123');
```
Returns array of devices registered for given user. Each item in array is instance of `Orkhanahmadov\Sipgate\Resources\Device` and has following properties:

``` php
$device->id; // string
$device->alias; // string
$device->type; // string
$device->online; // bool
$device->dnd; // bool
$device->activePhonelines; // array
$device->activeGroups; // array
$device->credentials; // array
$device->registered; // array
$device->emergencyAddressId; // string
$device->addressUrl; // string
```

**Active calls:**

Use `calls()` method to get list of currently established calls.

``` php
$sipgate->calls();
```
Returns array of currently established calls. Each item in array is instance of `Orkhanahmadov\Sipgate\Resources\Call` and has following properties:

``` php
$call->id; // string
$call->firstname; // string
$call->lastname; // string
$call->email; // string
$call->defaultDevice; // string
$call->busyOnBusy; // bool
$call->addressId; // string
$call->directDialIds; // array
$call->admin; // bool
```

**Initiate new call:**

Use `call()` method to initiate a new call. Method accepts 3 parameters:

* `$device` - Accepts instance of device or device ID. This defines which device you want to use to make a call.
* `$callee` - Phone number you want to call.
* `$callerId` (optional) - Set this parameter if you want to show different number to callee other. When skipped `$device` number will be used.

``` php
$sipgate->initiateCall($device, $callee, $callerId);
```
Returns call's session ID.

**Hangup ongoing call:**

Use `hangup()` method to hangup ongoing call. Method accepts call ID as parameter:

``` php
$sipgate->hangup('ABC-123');
```

**Record ongoing call:**

Use `record()` method to record ongoing call. Method accepts 3 parameters:

* `$callId` - Unique call ID.
* `$value` - `true` or `false`, defines start or stop of recording.
* `$announcement` - `true` or `false`, defines if you want to play announcement about call being recorded.

``` php
$sipgate->record($callId, $value, $announcement);
```

**Call history:**

Use `history()` method to get call history. Method accepts array of options:

* `connectionIds` - `array`, defines list of extensions
* `types` - `array`, defines list of types you want to receive in history, might contain one of many of following values: "CALL", "VOICEMAIL", "SMS", "FAX"
* `directions` - `array`, defines list of call directions you want to receive in history, might contain one of many of following values: "INCOMING", "OUTGOING", "MISSED_INCOMING", "MISSED_OUTGOING"
* `archived` - `bool`, set `true` if you want to receive only archived history items
* `starred` - Defines if you want to receive on starred of unstarred history items, one of these: "STARRED", "UNSTARRED"
* `from` - Defines start date of history. Must be in [ISO8601](https://en.wikipedia.org/wiki/ISO_8601) format
* `to` - Defines end date of history. Must be in [ISO8601](https://en.wikipedia.org/wiki/ISO_8601) format
* `phonenumber` - Defines phone number to search in history
* `limit` - Defines "per page" value for history items
* `offset` - Defines "offset" value for history items

``` php
$sipgate->history(['from' => '2019-07-10T19:32:18Z', 'to' => '2019-07-22T19:32:18Z']);
```

Returns array of history items. Each item in array is instance of `Orkhanahmadov\Sipgate\Resources\History` and has following properties:

``` php
$history->id; // string
$history->source; // string
$history->target; // string
$history->sourceAlias; // string
$history->targetAlias; // string
$history->type; // string
$history->created; // string
$history->lastModified; // string
$history->direction; // string
$history->incoming; // bool
$history->status; // string
$history->connectionIds; // array
$history->read; // bool
$history->archived; // bool
$history->note; // string
$history->endpoints; // array
$history->starred; // bool
$history->labels; // array
$history->callId; // string
$history->recordingUrl; // string
$history->recordings; // array
$history->duration; // int
$history->responder; // string
$history->responderAlias; // string
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email ahmadov90@gmail.com instead of using the issue tracker.

## Credits

- [Orkhan Ahmadov](https://github.com/orkhanahmadov)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
