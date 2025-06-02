<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user and get token
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'admin123',
        ]);

        $this->token = $response->json('token');
    }

    public function test_can_create_translation()
    {
        $response = $this->withToken($this->token)->postJson('/api/translations', [
            'key' => 'welcome',
            'content' => [
                'en' => 'Welcome',
                'fr' => 'Bienvenue',
                'es' => 'Bienvenido'
            ],
            'tags' => ['web']
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['key' => 'welcome']);
    }

    public function test_can_search_translation_by_key()
    {
        Translation::factory()->create(['key' => 'search_me']);

        $response = $this->withToken($this->token)->get('/api/translations?key=search_me');

        $response->assertStatus(200)
                 ->assertJsonFragment(['key' => 'search_me']);
    }

    public function test_can_update_translation()
    {
        $translation = Translation::factory()->create([
            'key' => 'update_me',
            'content' => ['en' => 'Old', 'fr' => 'Ancien', 'es' => 'Antiguo']
        ]);

        $response = $this->withToken($this->token)->putJson("/api/translations/{$translation->id}", [
            'content' => ['en' => 'Updated']
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['en' => 'Updated']);
    }

    public function test_can_delete_translation()
    {
        $translation = Translation::factory()->create();

        $response = $this->withToken($this->token)->deleteJson("/api/translations/{$translation->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('translations', ['id' => $translation->id]);
    }

    public function test_can_export_translations()
    {
        Translation::factory()->create([
            'key' => 'export_key',
            'content' => ['en' => 'Exported']
        ]);

        $response = $this->withToken($this->token)->get('/api/translations/export?locale=en');

        $response->assertStatus(200)
                 ->assertJsonFragment(['export_key' => 'Exported']);
    }
}
