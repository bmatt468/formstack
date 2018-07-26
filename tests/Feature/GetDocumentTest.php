<?php

namespace Tests\Feature;

use Artisan;
use App\Document;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class GetDocumentsTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function testGetDocument()
    {
        $doc = Document::find(1);

        $response = $this->get('api/documents/1');
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 200,
            ]);

        $content = json_decode($response->getContent());
        $document = $content->document;
        $this->assertEquals($document->id, 1);
        $this->assertEquals($document->title, $doc->title);
        $this->assertEquals($document->creator->id, $doc->creator->id);
        $this->assertEquals($document->creator->username, $doc->creator->username);
    }

    public function testGetDocument404()
    {
        $response = $this->get('api/documents/0');
        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'status' => 404,
            ]);
    }
}
