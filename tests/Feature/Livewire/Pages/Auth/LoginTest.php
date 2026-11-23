<?php

namespace Tests\Feature\Livewire\Pages\Auth;

use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login_page_renders_frontend_only_demo_form(): void
    {
        $response = $this->get(route('login'))->assertOk()
            ->assertSee('Masuk ke DC Monitoring')
            ->assertSee('Email')
            ->assertSee('Kata sandi')
            ->assertSee('Ingat saya')
            ->assertSee('Lupa kata sandi?')
            ->assertSee('Demo')
            ->assertSee('x-data="toastNotifications"', false)
            ->assertDontSee('id="main-content"', false)
            ->assertDontSee('wire:submit', false)
            ->assertDontSee('wire:model', false);

        $document = new \DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new \DOMXPath($document);

        $form = $xpath->query('//form')->item(0);
        $this->assertNotNull($form);
        $this->assertSame('submit', (string) $form->getAttribute('@submit.prevent'));

        $this->assertCount(1, $xpath->query('//*[@id="login-email"][@type="email"]'));
        $this->assertCount(1, $xpath->query('//*[@id="login-password"][@type="password"]'));
        $this->assertCount(1, $xpath->query('//*[@id="login-remember"][@type="checkbox"]'));
        $this->assertCount(1, $xpath->query('//button[@type="submit"]'));
    }
}
