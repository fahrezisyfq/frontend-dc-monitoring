<?php

namespace Tests\Feature\Livewire\Pages;

use App\Livewire\Pages\Users\Index as Users;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class UsersTest extends TestCase
{
    public function test_page_has_six_columns_and_no_password_in_table_or_detail(): void
    {
        $response = $this->get(route('users.index'))->assertOk()->assertSee('Administrator Sistem');
        $document = new \DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new \DOMXPath($document);
        $drawer = $xpath->query('//div[@*[name()="wire:key"]="user-detail-drawer"]');
        $this->assertCount(1, $drawer);
        $this->assertStringContainsString('drawer(', $drawer->item(0)->getAttribute('x-data'));
        $this->assertCount(0, $xpath->query('//*[@*[name()="wire:key"]="user-modal"]'));
        $headings = $xpath->query('//table/thead/tr/th');
        $this->assertSame(['No.', 'Nama Pengguna', 'Email', 'Role', 'Last Activity', 'Aksi'], array_map(
            fn ($heading) => trim($heading->textContent), iterator_to_array($headings),
        ));
        // The modal form is pre-rendered for instant opening; detail contains only profile labels.
        $this->assertSame(['Nama Pengguna', 'Email', 'Role'], array_map(
            fn ($label) => trim($label->textContent), iterator_to_array($xpath->query('//dl/dt | //dl/div/dt')),
        ));
        Livewire::test(Users::class)->assertCount('users', 6);
    }

    public function test_search_matches_names_and_emails_and_can_be_reset(): void
    {
        Livewire::test(Users::class)->set('search', ' INFRASTRUKTUR ')
            ->assertSee('Admin Infrastruktur')->assertDontSee('Administrator Sistem')
            ->set('search', 'VIEWER.DATA@')->assertSee('Pengamat Data')->assertDontSee('Admin Monitoring')
            ->set('search', 'missing')->assertSee('Pengguna tidak ditemukan')
            ->set('search', '')->assertSee('Administrator Sistem');
    }

    public function test_reusable_controls_target_their_user_and_modals_keep_the_existing_actions(): void
    {
        $response = $this->get(route('users.index'))->assertOk();
        $document = new \DOMDocument;
        @$document->loadHTML($response->getContent());
        $xpath = new \DOMXPath($document);

        foreach (range(1, 6) as $id) {
            foreach (['Edit', 'Hapus'] as $title) {
                $buttons = $xpath->query('//table/tbody/tr/td//button[@title="'.$title.'" and @data-id="'.$id.'"]');
                $this->assertCount(1, $buttons);
                $this->assertSame('button', $buttons->item(0)->getAttribute('type'));
            }
        }

        $detail = $xpath->query('//table/tbody/tr/td//button[@title="Detail" and @data-id="1"]')->item(0);
        $edit = $xpath->query('//table/tbody/tr/td//button[@title="Edit" and @data-id="1"]')->item(0);
        $delete = $xpath->query('//table/tbody/tr/td//button[@title="Hapus" and @data-id="1"]')->item(0);
        $this->assertStringContainsString('drawer:open', $detail->getAttribute('@click'));
        $this->assertStringContainsString('user-detail', $detail->getAttribute('@click'));
        $this->assertStringContainsString('modal-edit:open', $edit->getAttribute('@click'));
        $this->assertStringContainsString('user-edit', $edit->getAttribute('@click'));
        $this->assertStringContainsString('modal-delete:open', $delete->getAttribute('@click'));
        $this->assertStringContainsString('user-delete', $delete->getAttribute('@click'));

        $editModal = $xpath->query('//div[contains(@x-data, "modalEdit(")]');
        $deleteModal = $xpath->query('//div[contains(@x-data, "modalDelete(")]');
        $this->assertCount(1, $editModal);
        $this->assertCount(1, $deleteModal);
        $this->assertStringContainsString('saveUser', $editModal->item(0)->getAttribute('x-data'));
        $this->assertStringContainsString('deleteUser', $deleteModal->item(0)->getAttribute('x-data'));
        foreach (['name', 'email', 'password', 'confirmation', 'role'] as $field) {
            $this->assertCount(1, $xpath->query('//*[@id="user-'.$field.'"]'));
            $input = $xpath->query('//*[@id="create-user-'.$field.'"]')->item(0);
            $this->assertSame('form.'.$field, $input->getAttribute('wire:model'));
        }
    }

    public function test_create_stores_only_profile_fields_without_database_queries_and_resets_on_reload(): void
    {
        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = $query->sql;
        });

        Livewire::test(Users::class)->set('form', $this->form())->call('createUser')
            ->assertHasNoErrors()->assertCount('users', 7)->assertSet('form.name', '')
            ->assertSet('users.7', ['name' => 'Pengguna Baru', 'email' => 'baru@example.com', 'role' => 'viewer', 'last_activity' => null])
            ->assertDispatched('toast', type: 'success', message: 'Pengguna berhasil ditambahkan ke simulasi.')
            ->call('saveUser', [...$this->form(), 'name' => 'Nama Baru', 'password' => '', 'confirmation' => ''], 7)
            ->assertReturned(['success' => true])->assertSet('users.7.name', 'Nama Baru')
            ->call('deleteUser', 7)->assertReturned(['success' => true])->assertCount('users', 6);
        $this->assertSame([], $queries);
        Livewire::test(Users::class)->assertCount('users', 6)->assertDontSee('Pengguna Baru');
    }

    public function test_invalid_input_never_changes_the_list(): void
    {
        $component = Livewire::test(Users::class)->call('createUser')
            ->assertHasErrors([
                'form.name' => 'required', 'form.email' => 'required', 'form.role' => 'required',
                'form.password' => 'required', 'form.confirmation' => 'required',
            ]);
        foreach ([
            ['name', str_repeat('a', 256), 'max'],
            ['email', 'invalid@', 'email'],
            ['email', ' ADMIN.MONITORING@example.com ', null],
            ['role', 'unknown', 'in'],
            ['confirmation', 'different', 'same'],
        ] as [$field, $value, $rule]) {
            $component->set('form', [...$this->form(), $field => $value])->call('createUser')
                ->assertHasErrors($rule ? ['form.'.$field => $rule] : ['form.'.$field])
                ->assertCount('users', 6)->assertNotDispatched('toast');
        }
    }

    public function test_edit_validates_optional_passwords_and_duplicate_email(): void
    {
        $component = Livewire::test(Users::class);
        $original = $component->get('users')[1];

        $component->call('saveUser', [...$original, 'name' => '', 'password' => '', 'confirmation' => ''], 1)
            ->assertReturned(['success' => false, 'errors' => [
                'form.name' => ['Nama pengguna wajib diisi.'],
            ]])->assertSet('users.1.name', 'Administrator Sistem');

        $component->call('saveUser', [...$original, 'name' => 'Updated', 'password' => '', 'confirmation' => ''], 1)
            ->assertReturned(['success' => true])->assertSet('users.1.name', 'Updated')
            ->assertDispatched('toast', type: 'success', message: 'Pengguna berhasil diperbarui dalam simulasi.');

        $component->call('saveUser', [...$original, 'email' => 'admin.monitoring@example.com', 'password' => '', 'confirmation' => ''], 1)
            ->assertReturned(['success' => false, 'errors' => [
                'form.email' => ['Email sudah digunakan dalam daftar pengguna.'],
            ]])->assertSet('users.1.email', 'superadmin@example.com');

        $component->call('saveUser', [...$original, 'password' => 'new-secret', 'confirmation' => 'different'], 1)
            ->assertReturned(['success' => false, 'errors' => [
                'form.confirmation' => ['Konfirmasi password tidak cocok.'],
            ]])->assertSet('users.1.name', 'Updated');

        $component->call('saveUser', [...$original, 'password' => '', 'confirmation' => 'confirmation-only'], 1)
            ->assertReturned(['success' => false, 'errors' => [
                'form.password' => ['Password wajib diisi.'],
                'form.confirmation' => ['Konfirmasi password tidak cocok.'],
            ]])->assertSet('users.1.name', 'Updated');

        $this->assertSame(['name', 'email', 'role', 'last_activity'], array_keys($component->get('users')[1]));
    }

    public function test_delete_requires_confirmation_and_supports_empty_list(): void
    {
        $component = Livewire::test(Users::class)->call('deleteUser', 999)
            ->assertReturned(['success' => false, 'message' => 'Pengguna tidak ditemukan. Silakan buka kembali modal.'])
            ->assertCount('users', 6)->assertNotDispatched('toast');
        foreach (range(1, 6) as $id) {
            $component->call('deleteUser', $id);
        }
        $component->assertCount('users', 0)->assertSee('Belum ada pengguna')
            ->assertDispatched('toast', type: 'success', message: 'Pengguna berhasil dihapus dari simulasi.')
            ->call('deleteUser', 1)
            ->assertReturned(['success' => false, 'message' => 'Pengguna tidak ditemukan. Silakan buka kembali modal.'])
            ->assertCount('users', 0)
            ->set('form', $this->form())->call('createUser')->assertHasNoErrors()->assertCount('users', 1);
    }

    public function test_profile_text_is_escaped(): void
    {
        Livewire::test(Users::class)
            ->set('form', [...$this->form(), 'name' => '<script>alert(1)</script>'])->call('createUser')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_modal_draft_can_be_saved_and_deleted_without_preparation_requests(): void
    {
        Livewire::test(Users::class)->call('saveUser', $this->form())
            ->assertReturned(['success' => true])->assertCount('users', 7)
            ->assertSet('form.name', '')
            ->assertSet('users.7', ['name' => 'Pengguna Baru', 'email' => 'baru@example.com', 'role' => 'viewer', 'last_activity' => null])
            ->assertDispatched('toast', type: 'success', message: 'Pengguna berhasil ditambahkan ke simulasi.')
            ->call('saveUser', [...$this->form(), 'name' => 'Nama Baru', 'password' => '', 'confirmation' => ''], 7)
            ->assertReturned(['success' => true])->assertSet('users.7.name', 'Nama Baru')
            ->call('deleteUser', 7)->assertReturned(['success' => true])->assertCount('users', 6)
            ->assertDispatched('toast', type: 'success', message: 'Pengguna berhasil dihapus dari simulasi.');
    }

    public function test_modal_validation_returns_field_errors_without_retaining_passwords(): void
    {
        Livewire::test(Users::class)
            ->call('saveUser', [...$this->form(), 'email' => ' ADMIN.MONITORING@example.com '])
            ->assertReturned(['success' => false, 'errors' => ['form.email' => ['Email sudah digunakan dalam daftar pengguna.']]])
            ->assertCount('users', 6)->assertSet('form.name', '')->assertNotDispatched('toast')
            ->call('saveUser', [...$this->form(), 'confirmation' => 'different'])
            ->assertReturned(['success' => false, 'errors' => ['form.confirmation' => ['Konfirmasi password tidak cocok.']]])
            ->assertSet('form.password', '')
            ->call('saveUser', $this->form())->assertReturned(['success' => true])->assertCount('users', 7);
    }

    public function test_stale_modal_targets_do_not_mutate_users(): void
    {
        Livewire::test(Users::class)->call('saveUser', $this->form(), 999)
            ->assertReturned(['success' => false, 'message' => 'Pengguna tidak ditemukan. Silakan buka kembali modal.'])
            ->assertCount('users', 6)->call('deleteUser', 999)
            ->assertReturned(['success' => false, 'message' => 'Pengguna tidak ditemukan. Silakan buka kembali modal.'])
            ->assertCount('users', 6)->assertNotDispatched('toast');
    }

    public function test_last_activity_is_formatted_in_jakarta_and_preserved_when_editing(): void
    {
        config(['app.timezone' => 'UTC']);

        Livewire::test(Users::class)
            ->assertSee('17 Nov 2026, 10:30 WIB')
            ->assertSee('16 Nov 2026, 14:25 WIB')
            ->assertSee('Belum pernah login')
            ->call('saveUser', [...$this->form(), 'last_activity' => '2030-01-01T00:00:00Z'], 1)
            ->assertReturned(['success' => true])
            ->assertSet('users.1.last_activity', '2026-11-17T10:30:00+07:00')
            ->assertSee('17 Nov 2026, 10:30 WIB')
            ->call('saveUser', [...$this->form(), 'email' => 'new@example.com'])
            ->assertReturned(['success' => true])
            ->assertSet('users.7.last_activity', null);
    }

    public function test_reset_form_clears_create_draft_without_dispatching_toast(): void
    {
        Livewire::test(Users::class)
            ->set('form.name', 'Draft sementara')
            ->call('resetForm')
            ->assertSet('form.name', '')
            ->assertNotDispatched('toast');
    }

    private function form(): array
    {
        return ['name' => ' Pengguna Baru ', 'email' => ' baru@example.com ', 'role' => 'viewer', 'password' => 'demo-secret', 'confirmation' => 'demo-secret'];
    }
}
