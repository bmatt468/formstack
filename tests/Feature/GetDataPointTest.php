<?php

namespace Tests\Feature;

use Artisan;
use App\Document;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class GetDataPointTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function testGetDocumentKey()
    {
        $doc = Document::find(1);
        $dataPoint = $doc->data->first();

        $response = $this->get("api/documents/1/data/{$dataPoint->key}");
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 200,
                'value' => $dataPoint->value,
            ]);
    }

    public function testGetDocumentKey404()
    {
        $response = $this->get('api/documents/1/data/thisKeyDoesNotExistAtAll');
        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'status' => 404,
            ]);
    }
}
