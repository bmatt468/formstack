<?php

namespace Tests\Feature;

use Artisan;
use App\Document;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class DeleteDocumentTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function testDeleteDocument()
    {
        $doc = Document::withTrashed()->find(1);
        $this->assertEquals($doc->trashed(), false);

        $response = $this->delete('api/documents/1');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 200,
            ]);

        $doc = Document::withTrashed()->find(1);
        $this->assertEquals($doc->trashed(), true);

        $doc = Document::find(1);
        $this->assertNull($doc);
    }

    public function testDeletedDocument404()
    {
        $doc = Document::withTrashed()->find(1);
        $doc->delete();
        $response = $this->get('api/documents/1');
        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'status' => 404,
            ]);
    }
}
