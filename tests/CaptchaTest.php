<?php

namespace DutchCodingCompany\LivewireRecaptcha\Tests;

use DutchCodingCompany\LivewireRecaptcha\Tests\Fixtures\MyTestComponent;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;

class CaptchaTest extends TestCase
{
    /**
     * @param array{0: bool, 1: array{'success': bool}} $captchaResponse
     */
    #[DataProvider('provideCaptchaData')]
    public function testInvalidCaptchaResponse(bool $isValid, array $captchaResponse): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response($captchaResponse),
        ]);

        $testable = Livewire::test(MyTestComponent::class);

        $testable->assertSee('<script src="https://www.google.com/recaptcha/api.js?render=mysitekey"></script>', false)
            ->assertSee("Livewire.directive('recaptcha'", false);

        $testable->set('gRecaptchaResponse', $captcha = 'mygrecaptcharesponse');

        $testable = $testable->call('save');

        if ($isValid) {
            $testable->assertHasNoErrors();
        } else {
            $testable->assertHasErrors([
                'gRecaptchaResponse',
            ]);
        }

        Http::assertSent(fn (Request $request,
        ) => $request->url() === 'https://www.google.com/recaptcha/api/siteverify' &&
            $request['secret'] === 'mysecretkey' &&
            $request['response'] === $captcha &&
            array_key_exists('remoteip', $request->data())
        );
    }

    /**
     * @return array<string, array{0: bool, 1: array{'success': bool}}>
     */
    public static function provideCaptchaData(): array
    {
        return [
            'valid response' => [true, ['success' => true, 'score' => 0.9]],
            'valid response, low score' => [false, ['success' => true, 'score' => 0.1]],
            'invalid response' => [false, ['success' => false]],
        ];
    }
}
