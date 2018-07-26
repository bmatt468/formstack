<?php

namespace Tests\Feature;

use Artisan;
use App\Document;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class PatchDataPointTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    public function testPatchDocumentKey()
    {
        $doc = Document::find(1);
        $dataPoint = $doc->data->first();

        $response = $this->json('PATCH', "api/documents/1/data/{$dataPoint->key}", ['type' => 'string', 'value' => 'test']);
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 200,
            ]);

        $dataPoint = $dataPoint->fresh();
        $this->assertEquals($dataPoint->type, 'string');
        $this->assertEquals($dataPoint->value, 'test');
    }
}
