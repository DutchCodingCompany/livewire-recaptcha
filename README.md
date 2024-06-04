# Livewire ReCAPTCHA v3
[![Latest Version on Packagist](https://img.shields.io/packagist/v/dutchcodingcompany/livewire-recaptcha.svg?style=flat-square)](https://packagist.org/packages/dutchcodingcompany/livewire-recaptcha)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/dutchcodingcompany/livewire-recaptcha/run-tests?label=tests)](https://github.com/dutchcodingcompany/livewire-recaptcha/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/dutchcodingcompany/livewire-recaptcha/Check%20&%20fix%20styling?label=code%20style)](https://github.com/dutchcodingcompany/livewire-recaptcha/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dutchcodingcompany/livewire-recaptcha.svg?style=flat-square)](https://packagist.org/packages/dutchcodingcompany/livewire-recaptcha)

This package provides a custom Livewire directive to protect your Livewire functions with a _Google reCAPTCHA v3_ check.

## Installation
```shell
composer require dutchcodingcompany/livewire-recaptcha
```

## Configuration
Next, read https://developers.google.com/recaptcha/docs/v3 on how to create your own key pair.

The site key and secret key should be defined in your `config/services.php` file:

```php
// ...
'google' => [
    'recaptcha' => [
        'site_key' => env('GOOGLE_RECAPTCHA_SITE_KEY'),
        'secret_key' => env('GOOGLE_RECAPTCHA_SECRET_KEY'),
    ]
],
```
#### Component
In your Livewire component, at your form submission method, add the `#[ValidatesRecaptcha]` attribute:

```php
use Livewire\Component;

class SomeComponent extends Component 
{
    use DutchCodingCompany\LivewireRecaptcha\ValidatesRecaptcha;
    
    // (optional) Set a response property on your component.
    // If not given, the `gRecaptchaResponse` property is dynamically assigned.
    public string $gRecaptchaResponse;
    
    #[ValidatesRecaptcha]
    public function save(): mixed
    {
        // Your logic here will only be called if the captcha passes...
    }
}
```
For fine-grained control, you can pass manual site and secret keys using:
```php
#[ValidatesRecaptcha(siteKey: 'mysitekey', secretKey: 'mysecretkey')]
```
If you do not pass these, by default, the values are read from the `config/services.php` file.

#### View
On the view side, you have to include the Blade directive `@livewireRecaptcha`. This adds two scripts to the page,
one for the reCAPTCHA script and one for the custom Livewire directive to hook into the form submission.

Preferrably these scripts are only added to the page that has the Captcha-protected form (alternatively, you can add
the `@livewireRecaptcha` directive on a higher level, lets say your layout).

Secondly, add the new directive `wire:recaptcha` to the form element that you want to protect.
```html
<!-- some-livewire-component.blade.php -->

<!-- (optional) Add error handling -->
@if($errors->has('gRecaptchaResponse'))
    <div class="alert alert-danger">{{ $errors->first('gRecaptchaResponse') }}</div>
@endif

<!-- Add the `wire:recaptcha` Livewire directive -->
<form wire:submit="save" wire:recaptcha>
    <!-- The rest of your form -->
    <button type="submit">Submit</button>
</form>

<!-- Add the `@livewireRecaptcha` Blade directive -->
@livewireRecaptcha
```

Also here, you are able to set the site-key manually for the directive using:
```html
@livewireRecaptcha('mysitekey')
```

#### Finishing up
The Google reCAPTCHA protection will automatically occur before the actual form is submitted. Before the `save()` method
is executed, a serverside request will be sent to Google to verify the invisible Captcha challenge. Once the reCAPTCHA response
has been successful, the actual `save()` method will be executed.

#### Error handling
When an error occurs with the Captcha validation, a ValidationException is thrown for the key `gRecaptchaResponse`.
There is a translatable error message for `'livewire-recaptcha::recaptcha.invalid_response'`.
