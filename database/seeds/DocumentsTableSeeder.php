<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DocumentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Document::class, 100)->create()->each(function ($document) {
            $dataPoints = random_int(1, 10);
            for ($i = 0; $i < $dataPoints; $i++) {
                $document->data()->save(factory(App\Data::class)->make());
            }
        });
    }
}
