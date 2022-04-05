# SamlServiceProvider

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

SAML2.0 Service Provider Laravel Package, use this package to log into a IDP with SAML2.0. Please feel free to contribute to this package. This package has had minimal testing, and provides no security guarantees. Use at your own risk.

## Installation

Via Composer

``` bash
composer require philwilliammee/samlserviceprovider
```

## Usage

Publish Config and Views with

```bash
php artisan vendor:publish --provider="PhilWilliammee\SamlServiceProvider\SamlServiceProviderServiceProvider"
```

Edit the config file in `config/samlserviceprovider.php` to your liking.

This package comes with some blade templates that you can use to get you started. You can review them in `resources/views/vendor/philwilliammee`

example usage:

```html
    <x-samlserviceprovider::login redirect="/user">
        Login
    </x-samlserviceprovider::login>

    <x-samlserviceprovider::logout>
        Logout
    </x-samlserviceprovider::logout>
```

then in the user controller call:

```php
    $session_id = session()->getId();
    $user_attributes = SamlServiceProvider::getAttributes($session_id);
```

You will then probably want to login the user with Laravel, something like this:

```php
    $email = $user_attributes['mail'][0];
    $name = $user_attributes['displayName'][0];
    $user = User::where('email', $email)->first();
    if (!$user) {
        $user = User::create([
            'email' => $email,
            'name' => $name,
        ]);
    }
    Auth::login($user);
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email philwilliammee@gmail.com instead of using the issue tracker.

## Credits

- [Phil Williammee][link-author]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/philwilliammee/samlserviceprovider.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/philwilliammee/samlserviceprovider.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/philwilliammee/samlserviceprovider/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/philwilliammee/samlserviceprovider
[link-downloads]: https://packagist.org/packages/philwilliammee/samlserviceprovider
[link-travis]: https://travis-ci.org/philwilliammee/samlserviceprovider
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/philwilliammee
[link-contributors]: ../../contributors
