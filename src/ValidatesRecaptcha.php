<?php

namespace DutchCodingCompany\LivewireRecaptcha;

use Attribute;
use DutchCodingCompany\LivewireRecaptcha\Exceptions\LivewireRecaptchaException;
use Closure;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportAttributes\Attribute as LivewireAttribute;
use function Livewire\trigger;
use function Livewire\wrap;

#[Attribute]
class ValidatesRecaptcha extends LivewireAttribute
{
    public function __construct(
        public ?string $siteKey = null,
        public ?string $secretKey = null,
    ) {
        $this->siteKey ??= config('services.google.recaptcha.site_key');
        $this->secretKey ??= config('services.google.recaptcha.secret_key');
    }

    public function boot(): void
    {
        Blade::directive(
            'livewireRecaptcha',
            function (string $expression) {
                $siteKey = ! empty($expression) ? str_replace(['"', '\''], '', $expression) : $this->siteKey;

                return str_replace(
                    '__SITEKEY__',
                    e($siteKey),
                    file_get_contents($path = __DIR__.'/directive.recaptcha.php') ?: throw new LivewireRecaptchaException("Failed to load '$path'."),
                );
            },
        );
    }

    /**
     * @param array<mixed> $params
     * @param \Closure(?\Closure $closure): void $returnEarly
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function call(array $params, Closure $returnEarly): void
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $this->secretKey,
            'response' => $this->component->gRecaptchaResponse,
            'remoteip' => request()->ip(),
        ])->json();

        if ($response['success'] ?? false) {
            $returnEarly(
                wrap($this->component)->{$this->subName}(...$params)
            );

            return;
        }

        $returnEarly(
            trigger('exception', $this->component, ValidationException::withMessages([
                'gRecaptchaResponse' => __('livewire-recaptcha::recaptcha.invalid_response'),
            ]), fn () => true)
        );
    }
}
