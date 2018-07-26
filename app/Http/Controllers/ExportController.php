<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Http\Request;
use Illuminate\Filesystem\FilesystemManager;
use Storage;

class ExportController extends Controller
{
    /**
     * This function is responsible for downloading a CSV of the document's data. It will return either a JSON payload
     * if there is an error, or it will download the generated CSV.
     *
     * @param Request $request - export options
     * @param int $id - the ID of the document we are exporting
     *
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function get(Request $request, $id)
    {
        // Attempt to get the document from the database.
        $document = Document::find($id);

        // If we were not able to find the document, we return a 404 back to the user.
        if (!$document) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'error' => [
                    'title' => 'Not Found',
                    'message' => 'The requested document was not found.',
                ],
            ], 404);
        }

        // Now that we have the document, we can go ahead an get the variables from the request.
        // If the optional variables were not set, we set them to be their default values.
        $now = now()->timestamp;
        $name = $request->get('name', "document-{$document->id}-export-{$now}");
        $format = $request->get('format', 'csv');
        $dateFormat = $request->get('date_format', 'Y-m-d H:i:s');

        // Make a check to see if the request format was valid. If so, generate that document,
        // otherwise, return a failure response back to the user.
        switch ($format) {
            case 'csv':
                $rows = $document->generateCSVContents([
                    'name' => $name,
                    'dateFormat' => $dateFormat,
                ]);

                // Tap our document so that the export is reflected, and return the file
                $document->wasExported();
                return response()->streamDownload(function () use ($rows) {
                    $fp = fopen("php://output", 'w');
                    foreach ($rows as $row) {
                        fputcsv($fp, $row);
                    }
                    fclose($fp);
                }, "{$name}.csv");
                break;

            default:
                return response()->json([
                    'success' => false,
                    'status' => 400,
                    'error' => [
                        'title' => 'Bad Request',
                        'message' => "The requested format '{$format}' is not valid.",
                    ],
                ], 404);
                break;
        }
    }

    /**
     * This function is responsible for exporting a CSV of the document's data to a third party service.
     *
     * @param Request $request - export options
     * @param int $id - the ID of the document we are exporting
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function post(Request $request, $id)
    {
        // Attempt to get the document from the database.
        $document = Document::find($id);

        // If we were not able to find the document, we return a 404 back to the user.
        if (!$document) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'error' => [
                    'title' => 'Not Found',
                    'message' => 'The requested document was not found.',
                ],
            ], 404);
        }

        // Now that we have the document, we can go ahead an get the variables from the request.
        // If the optional variables were not set, we set them to be their default values.
        $now = now()->timestamp;
        $name = $request->get('name', "document-{$document->id}-export-{$now}");
        $format = $request->get('format', 'csv');
        $service = $request->get('service', 's3');
        $dateFormat = $request->get('date_format', 'Y-m-d H:i:s');

        // Make a check to see if the request format was valid. If so, generate that document,
        // otherwise, return a failure response back to the user.
        $fp = null;
        switch ($format) {
            case 'csv':
                $rows = $document->generateCSVContents([
                    'name' => $name,
                    'dateFormat' => $dateFormat,
                ]);

                // Open file write contents
                $fp = tmpfile();
                foreach ($rows as $row) {
                    fputcsv($fp, $row);
                }
                break;

            default:
                return response()->json([
                    'success' => false,
                    'status' => 400,
                    'error' => [
                        'title' => 'Bad Request',
                        'message' => "The requested format '{$format}' is not valid.",
                    ],
                ], 404);
                break;
        }

        $url = null;
        switch ($service) {
            case 's3':
                $config = [
                    'driver' => 's3',
                    'key' => decrypt(env('AAK_HASH')),
                    'secret' => decrypt(env('ASAK_HASH')),
                    'region' => 'us-east-1',
                    'bucket' => 'formstack-challenge',
                ];

                $fm = new FilesystemManager(app());
                $s3 = $fm->createS3Driver($config);
                $stored = $s3->put("{$name}.csv", fopen(stream_get_meta_data($fp)['uri'], 'r'), 'public');

                if (!$stored) {
                    return response()->json([
                        'success' => false,
                        'status' => 500,
                        'error' => [
                            'title' => 'Internal Server Error',
                            'message' => "An unexpected error occured uploading the file to S3.",
                        ],
                    ], 500);
                }

                $url = $s3->url("{$name}.csv");
                break;

            default:
                return response()->json([
                    'success' => false,
                    'status' => 400,
                    'error' => [
                        'title' => 'Bad Request',
                        'message' => "The requested service '{$service}' is not valid.",
                    ],
                ], 404);
                break;
        }



        // At the end of it all, tap our document so that the export is reflected, and return the url to the user
        $document->wasExported();
        return response()->json([
            'success' => true,
            'status' => 200,
            'url' => $url,
        ]);
    }
}
