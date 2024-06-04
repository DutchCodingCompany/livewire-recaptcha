<?php

namespace DutchCodingCompany\LivewireRecaptcha\Tests;

use DutchCodingCompany\LivewireRecaptcha\LivewireRecaptchaServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Encryption\Encrypter;
use Illuminate\Session\Middleware\StartSession;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app?->make(Kernel::class)->pushMiddleware(StartSession::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            LivewireRecaptchaServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('app.key', 'base64:'.base64_encode(
            Encrypter::generateKey('AES-256-CBC')
        ));

        config()->set('services.google.recaptcha', [
            'site_key' => 'mysitekey',
            'secret_key' => 'mysecretkey',
            'version' => 'v3',
        ]);
    }
}
