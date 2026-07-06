<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Program;
use App\Models\Event;
use App\Models\Product;
use App\Models\Gallery;
use App\Models\News;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user for protected routes
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
        ]);

        // Login to get token
        $response = $this->postJson('/api/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $this->token = $response->json('token');
    }

    public function test_public_endpoints()
    {
        // Create dummy data
        Program::create(['title' => 'Test Program', 'description' => 'Desc', 'icon' => '📚']);
        Event::create(['title' => 'Test Event', 'date' => '2026', 'focus' => ['test']]);
        Product::create(['name' => 'Test Product', 'description' => 'Desc', 'features' => ['f1'], 'icon' => '🍯']);
        Gallery::create(['title' => 'Test Gallery', 'category' => 'Cat', 'color' => '#fff', 'height' => '100px']);
        News::create(['title' => 'Test News', 'category' => 'Cat', 'date' => '2026', 'excerpt' => 'Exc', 'content' => 'Content']);

        // Test GET endpoints
        $endpoints = ['programs', 'events', 'products', 'galleries', 'news'];

        foreach ($endpoints as $endpoint) {
            $response = $this->getJson("/api/{$endpoint}");
            $response->assertStatus(200);
            $this->assertGreaterThan(0, count($response->json()));
        }
    }

    public function test_admin_can_create_record_with_image()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/news', [
            'title' => 'New News',
            'category' => 'News Category',
            'date' => '1 Jan 2026',
            'excerpt' => 'Short desc',
            'content' => 'Full content here',
            'image' => $file,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('news', ['title' => 'New News']);
        
        $news = News::where('title', 'New News')->first();
        $this->assertNotNull($news->image_url);
        
        // Assert file exists in storage
        $path = str_replace('/storage/', '', $news->image_url);
        Storage::disk('public')->assertExists($path);
    }

    public function test_admin_can_update_record_with_image()
    {
        Storage::fake('public');

        $gallery = Gallery::create([
            'title' => 'Old Title',
            'category' => 'Cat',
            'color' => '#000',
            'height' => '100px'
        ]);

        $file = UploadedFile::fake()->image('updated.png');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/galleries/{$gallery->id}", [
            'title' => 'Updated Title',
            'category' => 'Cat',
            'color' => '#fff',
            'height' => '200px',
            'image' => $file,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('galleries', ['title' => 'Updated Title']);
        
        $updatedGallery = Gallery::find($gallery->id);
        $this->assertNotNull($updatedGallery->image_url);
        
        // Assert file exists in storage
        $path = str_replace('/storage/', '', $updatedGallery->image_url);
        Storage::disk('public')->assertExists($path);
    }

    public function test_admin_can_delete_record()
    {
        $program = Program::create(['title' => 'To Delete', 'description' => 'Desc', 'icon' => '🗑️']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/programs/{$program->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('programs', ['id' => $program->id]);
    }
}
