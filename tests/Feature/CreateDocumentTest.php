<?php

namespace Tests\Feature;

use App\User;
use App\Document;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateDocumentNoData()
    {
        $data = [
            'title' => 'Test Document Title No Data'
        ];

        // Header for user6
        $headers = [
            'Authorization' => 'Basic dXNlcjY6c2VjcmV0',
        ];

        $response = $this->withHeaders($headers)->json('POST', "api/documents/", $data);
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 200,
            ]);

        $user = User::find(6);
        $content = json_decode($response->getContent());
        $document = $content->document;
        $doc = Document::find($document->id);

        $this->assertNotNull($doc);
        $this->assertEquals($document->id, $doc->id);
        $this->assertEquals($document->title, $doc->title);
        $this->assertEquals($document->creator->id, $doc->creator->id);
        $this->assertEquals($document->creator->username, $doc->creator->username);
        $this->assertEquals($document->data, []);
    }

    public function testCreateDocumentWithData()
    {
        $data = [
            'title' => 'Test Document Title No Data',
            'data' => [
                'key1' => 'Should Be String',
                'key2' => [
                    'type' => 'number',
                    'value' => 42,
                ],
                'key3' => [
                    'type' => 'date',
                    'value' => '1970-01-01 00:00:00',
                ],
            ],
        ];

        // Header for user6
        $headers = [
            'Authorization' => 'Basic dXNlcjY6c2VjcmV0',
        ];

        $response = $this->withHeaders($headers)->json('POST', "api/documents/", $data);
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 200,
            ]);

        $user = User::find(6);
        $content = json_decode($response->getContent());
        $document = $content->document;
        $doc = Document::find($document->id);
        // dd($document);
        $this->assertNotNull($doc);
        $this->assertEquals($document->id, $doc->id);
        $this->assertEquals($document->title, $doc->title);
        $this->assertEquals($document->creator->id, $doc->creator->id);
        $this->assertEquals($document->creator->username, $doc->creator->username);
        $this->assertEquals($doc->data()->where('key','key1')->first()->type, 'string');
        $this->assertEquals($doc->data()->where('key','key1')->first()->value, 'Should Be String');
        $this->assertEquals($doc->data()->where('key','key2')->first()->type, 'number');
        $this->assertEquals($doc->data()->where('key','key2')->first()->value, 42);
        $this->assertEquals($doc->data()->where('key','key3')->first()->type, 'date');
        $this->assertEquals($doc->data()->where('key','key3')->first()->value, 0);
    }

    public function testNoTitleResponse()
    {
        // Header for user6
        $headers = [
            'Authorization' => 'Basic dXNlcjY6c2VjcmV0',
        ];

        $response = $this->withHeaders($headers)->json('POST', "api/documents/", []);
        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'status' => 400,
            ]);
    }

    public function testCreateDocumentWithBadType()
    {
        $data = [
            'title' => 'Test Document Title No Data',
            'data' => [
                'badKey' => [
                    'type' => 'badType',
                    'value' => 42,
                ],
            ],
        ];

        // Header for user6
        $headers = [
            'Authorization' => 'Basic dXNlcjY6c2VjcmV0',
        ];

        $response = $this->withHeaders($headers)->json('POST', "api/documents/", $data);
        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'status' => 400,
            ]);
    }

    public function testCreateDocumentWithNoValue()
    {
        $data = [
            'title' => 'Test Document Title No Data',
            'data' => [
                'badKey' => [
                    'type' => 'string',
                ],
            ],
        ];

        // Header for user6
        $headers = [
            'Authorization' => 'Basic dXNlcjY6c2VjcmV0',
        ];

        $response = $this->withHeaders($headers)->json('POST', "api/documents/", $data);
        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'status' => 400,
            ]);
    }
}
