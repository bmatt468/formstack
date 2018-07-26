<?php

namespace Tests\Feature;

use Artisan;
use App\Document;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class AllDocumentsTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function testAllDocuments()
    {
        $docs = Document::all();

        $response = $this->get('api/documents');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 200,
                'total' => count($docs),
                'per_page' => 25,
                'current_page' => 1,
            ]);

        $content = json_decode($response->getContent());
        $this->assertEquals(count($content->documents), 25);
    }

    public function testCountDocuments()
    {
        $docs = Document::all();
        $response = $this->get('api/documents?count=10');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 200,
                'total' => count($docs),
                'per_page' => 10,
                'current_page' => 1,
            ]);

        $content = json_decode($response->getContent());
        $this->assertEquals(count($content->documents), 10);
    }

    public function testDocumentPagination()
    {
        $docs = Document::all();
        $response = $this->get('api/documents?count=10&page=2');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 200,
                'total' => count($docs),
                'per_page' => 10,
                'current_page' => 2,
            ]);

        $content = json_decode($response->getContent());
        $this->assertEquals(count($content->documents), 10);
    }
}
