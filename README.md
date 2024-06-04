# Livewire ReCAPTCHA v3/v2/v2-invisible

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dutchcodingcompany/livewire-recaptcha.svg?style=flat-square)](https://packagist.org/packages/dutchcodingcompany/livewire-recaptcha)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/dutchcodingcompany/livewire-recaptcha/run-tests?label=tests)](https://github.com/dutchcodingcompany/livewire-recaptcha/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/dutchcodingcompany/livewire-recaptcha/Check%20&%20fix%20styling?label=code%20style)](https://github.com/dutchcodingcompany/livewire-recaptcha/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dutchcodingcompany/livewire-recaptcha.svg?style=flat-square)](https://packagist.org/packages/dutchcodingcompany/livewire-recaptcha)

This package provides a custom Livewire directive to protect your Livewire functions with a _Google reCAPTCHA (v2 + v2
invisible + v3)_ check.

## Installation

```shell
composer require dutchcodingcompany/livewire-recaptcha
```

## Configuration

Read https://developers.google.com/recaptcha/intro on how to create your own key pair for the specific ReCaptcha
version you are going to implement.

This package supports the following versions. Note that each version requires a different sitekey/secretkey pair:

| **Version**          | **Docs**                                                          | **Notes**                   |
|----------------------|-------------------------------------------------------------------|-----------------------------|
| **v3** (recommended) | [V3 Docs](https://developers.google.com/recaptcha/docs/v3)        |                             |
| **v2**               | [V2 Docs](https://developers.google.com/recaptcha/docs/display)   |                             |
| **v2 invisible**     | [V2 Docs](https://developers.google.com/recaptcha/docs/invisible) | Use `'size' => 'invisible'` |

Your options should reside in the `config/services.php` file:

```php
    // V3 config:
    'google' => [
        'recaptcha' => [
            'site_key' => env('GOOGLE_RECAPTCHA_SITE_KEY'),
            'secret_key' => env('GOOGLE_RECAPTCHA_SECRET_KEY'),
            'version' => 'v3',
            'score' => 0.5, // An integer between 0 and 1, that indicates the minimum score to pass the Captcha challenge.
        ],
    ],

    // V2 config:
    'google' => [
        'recaptcha' => [
            'site_key' => env('GOOGLE_RECAPTCHA_SITE_KEY'),
            'secret_key' => env('GOOGLE_RECAPTCHA_SECRET_KEY'),
            'version' => 'v2',
            'size' => 'normal', // 'normal', 'compact' or 'invisible'.
            'theme' => 'light', // 'light' or 'dark'.
        ],
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

For fine-grained control, you can pass a custom secret key and minimum score (applies only to V3) using:

```php
#[ValidatesRecaptcha(secretKey: 'mysecretkey', score: 0.9)]
```

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

You can override any of the configuration values using:

```html
@livewireRecaptcha(
    version: 'v2',
    siteKey: 'abcd_efgh-hijk_LMNOP',
    theme: 'dark',
    size: 'compact',
)
```

#### Finishing up

The Google ReCAPTCHA validation will automatically occur before the actual form is submitted. Before the `save()` method
is executed, a serverside request will be sent to Google to verify the Captcha challenge. Once the reCAPTCHA
response has been successful, your actual Livewire component method will be executed.

#### Error handling

When an error occurs with the Captcha validation, a ValidationException is thrown for the key `gRecaptchaResponse`.
There is a translatable error message available under `'livewire-recaptcha::recaptcha.invalid_response'`.
