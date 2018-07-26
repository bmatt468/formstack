<?php

namespace Tests\Feature;

use Artisan;
use App\Document;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class DeleteDataPointTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function testGetDocumentKey()
    {
        $doc = Document::find(1);
        $dataPoint = $doc->data->first();
        $keyName = $dataPoint->key;

        $response = $this->delete("api/documents/1/data/{$keyName}");
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 200,
            ]);

        $response = $this->delete("api/documents/1/data/{$keyName}");
        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'status' => 404,
            ]);
    }
}
