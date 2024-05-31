# Installation
```shell
composer require dutchcodingcompany/livewire-recaptcha
```

# Configuration
### View
Include the required Google reCAPTCHA script and the custom Livewire directive by
using the Blade directive `@livewireRecaptcha` in your view. This will add two `<script>` tags.

Preferred method: only include it on the page where you have a reCAPTCHA protected form:
```html
<!-- someform.blade.php -->

<!-- Add the `wire:recaptcha` directive -->
<form wire:submit="save" wire:recaptcha>
    <!-- The rest of your form -->
</form>

@livewireRecaptcha
<!-- or push it to some stack using @push('...')@endpush if you want to control it manually -->
```

Alternatively, you can add the `@livewireRecaptcha` directive on a higher level, lets say your layout.

### Component
At your form submission method in your component, add the following attribute:
```php
    // SomeComponent.php
    use DutchCodingCompany\LivewireRecaptcha\ValidatesRecaptcha;
    
    // (optional) Set a response property on your component.
    // If not given, the property is dynamically assigned.
    public string $gRecaptchaResponse;
    
    /* 
     * (default) take keys from: 
     * config('services.google.recaptcha.site_key')
     * config('services.google.recaptcha.secret_key')
     */
    #[ValidatesRecaptcha]
    /*
     * (alternative) manually provide keys:
     */
    #[ValidatesRecaptcha(siteKey: 'foo', secretKey: 'bar')]
    public function save(): mixed
    {
        // Your logic here will only be called if the captcha passes...
    }
```

The google reCAPTCHA check will automatically occur before the actual form is submitted. Once
the `save()` method is executed, the reCAPTCHA response has been successful.

When an error occurs with validation, a ValidationException is thrown for the key `gRecaptchaResponse`.
There is a translatable error message for `'livewire-recaptcha::recaptcha.invalid_response'`.
