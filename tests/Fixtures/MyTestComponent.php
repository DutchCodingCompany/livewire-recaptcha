<?php

namespace DutchCodingCompany\LivewireRecaptcha\Tests\Fixtures;

use DutchCodingCompany\LivewireRecaptcha\ValidatesRecaptcha;
use Exception;
use Livewire\Component;

class MyTestComponent extends Component
{
    public string $gRecaptchaResponse;

    public function mount(): void
    {
        //
    }

    #[ValidatesRecaptcha]
    public function save(): void
    {
        //
    }

    public function render(): string
    {
        return file_get_contents(__DIR__.'/my-test-component.blade.php') ?: throw new Exception('Failed to load my-test-component.blade.php.');
    }
}
