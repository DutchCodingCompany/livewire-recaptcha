<?php

namespace DutchCodingCompany\LivewireRecaptcha;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class LivewireRecaptchaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/lang' => lang_path('vendor/livewire-recaptcha'),
            ], 'aaa');
        }

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'livewire-recaptcha');

        Blade::directive(
            'livewireRecaptcha',
            static fn (string $expression): string => "<?php echo \DutchCodingCompany\LivewireRecaptcha\LivewireRecaptcha::directive($expression) ?>",
        );
    }

    public function register(): void
    {
        //
    }
}
