<?php

namespace DutchCodingCompany\LivewireRecaptcha;

use Attribute;
use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportAttributes\Attribute as LivewireAttribute;
use function Livewire\trigger;
use function Livewire\wrap;

#[Attribute]
class ValidatesRecaptcha extends LivewireAttribute
{
    public function __construct(
        public ?string $secretKey = null,
        public ?float $score = null,
    ) {
        $this->secretKey ??= config('services.google.recaptcha.secret_key');
        $this->score ??= config('services.google.recaptcha.score') ?? 0.5;
    }

    /**
     * @param array<mixed> $params
     * @param \Closure(?\Closure $closure): void $returnEarly
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function call(array $params, Closure $returnEarly): void
    {
        if (isset($this->component->gRecaptchaResponse)) {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $this->secretKey,
                'response' => $this->component->gRecaptchaResponse,
                'remoteip' => request()->ip(),
            ])->json();
        }

        // Check success value and score. The score falls back to 1 since it is not present for v2.
        if (($response['success'] ?? false) && ($response['score'] ?? 1) >= $this->score) {
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
