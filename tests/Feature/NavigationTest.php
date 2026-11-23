<?php

namespace Tests\Feature;

use Tests\TestCase;

class NavigationTest extends TestCase
{
    public function test_internal_pages_render_navigable_sidebar_with_the_correct_active_link(): void
    {
        $routes = ['dashboard', 'companies.index', 'area-types.index', 'service-types.index', 'data-centers.create', 'data-centers.index', 'users.index', 'activity-logs.index', 'trash.index'];

        foreach ([...$routes, 'analytics.index', 'providers.index'] as $route) {
            $response = $this->get(route($route))->assertOk();

            $document = new \DOMDocument;
            @$document->loadHTML($response->getContent());
            $xpath = new \DOMXPath($document);
            $links = $xpath->query('//nav[@aria-label="Menu aplikasi"]//a');
            $this->assertCount(9, $links);
            $this->assertSame(0, $xpath->query('//nav[@aria-label="Menu aplikasi"]//p[normalize-space()="Monitoring"]')->length);
            $this->assertSame('Trash', trim($links->item(8)->textContent));

            $submenuToggle = $xpath->query('//button[@aria-controls="data-center-submenu"]')->item(0);
            $this->assertSame('Pusat Data', trim($submenuToggle->textContent));
            $this->assertSame(in_array($route, ['data-centers.create', 'data-centers.index']) ? 'true' : 'false', $submenuToggle->getAttribute('aria-expanded'));
            $submenuLinks = $xpath->query('//div[@id="data-center-submenu"]//a');
            $this->assertCount(2, $submenuLinks);
            $this->assertSame('Tambah Pusat Data', trim($submenuLinks->item(0)->textContent));
            $this->assertSame('Daftar Pusat Data', trim($submenuLinks->item(1)->textContent));
            $this->assertSame('Kawasan', trim($links->item(2)->textContent));
            $this->assertSame('Service', trim($links->item(3)->textContent));
            $this->assertSame($links->item(1)->parentNode, $links->item(2)->parentNode);
            $this->assertSame($links->item(1)->parentNode, $links->item(3)->parentNode);

            if (in_array($route, ['area-types.index', 'service-types.index'])) {
                $this->assertSame($route === 'area-types.index' ? 'Kawasan' : 'Service', trim($xpath->query('//nav[@aria-label="Breadcrumb"]//li[@aria-current="page"]')->item(0)->textContent));
            }

            $expectedBreadcrumb = match ($route) {
                'dashboard' => ['Dashboard'],
                'companies.index' => ['Manajemen Data', 'Perusahaan'],
                'data-centers.create' => ['Manajemen Data', 'Pusat Data', 'Tambah Pusat Data'],
                'data-centers.index' => ['Manajemen Data', 'Pusat Data', 'Daftar Pusat Data'],
                'area-types.index' => ['Manajemen Data', 'Kawasan'],
                'service-types.index' => ['Manajemen Data', 'Service'],
                'providers.index' => ['Manajemen Data', 'Provider'],
                'users.index' => ['Administrasi', 'Pengguna'],
                'activity-logs.index' => ['Administrasi', 'Log Aktivitas'],
                'trash.index' => ['Administrasi', 'Trash'],
                default => null,
            };

            if ($expectedBreadcrumb !== null) {
                $breadcrumbItems = $xpath->query('//nav[@aria-label="Breadcrumb"]//li[not(@aria-hidden)]');
                $this->assertCount(count($expectedBreadcrumb), $breadcrumbItems);

                foreach ($expectedBreadcrumb as $index => $label) {
                    $this->assertSame($label, trim($breadcrumbItems->item($index)->textContent));
                }

                $this->assertSame('page', $breadcrumbItems->item(count($expectedBreadcrumb) - 1)->getAttribute('aria-current'));
                $this->assertStringStartsWith(end($expectedBreadcrumb), $document->getElementsByTagName('title')->item(0)->textContent);
            }

            foreach ($links as $index => $link) {
                $this->assertTrue($link->hasAttribute('wire:navigate'));
                $this->assertSame(route($routes[$index]), $link->getAttribute('href'));
                $this->assertSame($routes[$index] === $route ? 'page' : '', $link->getAttribute('aria-current'));
            }
        }
    }
}
