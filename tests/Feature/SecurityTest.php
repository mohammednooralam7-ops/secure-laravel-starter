<?php
namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_present(): void
    {
        $r = $this->get('/');
        $r->assertHeader('X-Frame-Options', 'DENY');
        $r->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertNotNull($r->headers->get('Content-Security-Policy'));
    }

    public function test_account_locks_after_repeated_failures(): void
    {
        $u = User::factory()->create(['password' => Hash::make('Correct-Horse-9!')]);
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/login', ['email' => $u->email, 'password' => 'wrong'])->assertStatus(422);
        }
        $this->postJson('/login', ['email' => $u->email, 'password' => 'Correct-Horse-9!'])->assertStatus(423);
    }

    public function test_login_does_not_reveal_whether_email_exists(): void
    {
        $this->postJson('/login', ['email' => 'nobody@example.com', 'password' => 'x'])
             ->assertStatus(422)->assertJson(['message' => 'Invalid credentials']);
    }

    public function test_user_cannot_view_another_users_document(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $doc = Document::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)->getJson("/documents/{$doc->id}")->assertForbidden();
        $this->actingAs($owner)->getJson("/documents/{$doc->id}")->assertOk();
    }

    public function test_upload_rejects_disallowed_type(): void
    {
        Storage::fake('local');
        $u = User::factory()->create();
        $file = UploadedFile::fake()->create('shell.php', 10, 'application/x-php');
        $this->actingAs($u)->postJson('/documents', ['file' => $file])->assertStatus(422);
    }

    public function test_upload_accepts_pdf_with_random_name(): void
    {
        Storage::fake('local');
        $u = User::factory()->create();
        $file = UploadedFile::fake()->create('report.pdf', 50, 'application/pdf');
        $this->actingAs($u)->postJson('/documents', ['file' => $file])->assertCreated();
        $this->assertDatabaseMissing('documents', ['path' => 'private-uploads/report.pdf']);
    }
}
