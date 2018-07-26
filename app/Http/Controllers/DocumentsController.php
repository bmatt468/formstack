<?php

namespace App\Http\Controllers;

use Auth;
use App\Document;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DocumentsController extends Controller
{
    /**
     * This function returns all of the available documents back to the API.
     *
     * @param Request $request - the optional parameters for the request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(Request $request)
    {
        // Get our count from the request. If it was not defined, set it to the default value.
        $count = $request->get('count') ?? 25;

        // Retrieve all of the documents from the database, and format the records to have
        // the layout that we want (we are using the `map` function for this). We also return
        // pagination information, and information about the entire dataset.
        $docs = Document::with(['creator','lastModifier','lastExporter'])->paginate($count);
        return response()->json([
            'success' => true,
            'status' => 200,
            'total' => $docs->total(),
            'per_page' => $docs->perPage(),
            'current_page' => $docs->currentPage(),
            'last_page' => $docs->lastPage(),
            'documents' => $docs->map(function ($item, $key) {
                // To see if the document was ever modified, we check the record for the last_modifier_id.
                // If we find a value there, we will return the timestamp based off updated_at, otherwise
                // we will return null.
                $last_update = null;
                if ($item->last_modifier_id) {
                    $last_update = [
                        'value' => $item->updated_at->toDateTimeString(),
                        'epoch' => $item->updated_at->timestamp,
                        'user' => $item->lastModifier->only('id', 'username'),
                    ];
                }

                // Check to see if this document was ever exported. If it was, we can go ahead and return
                // that timestamp, otherwise we return null.
                $last_export = null;
                if ($item->last_export) {
                    $last_export = [
                        'value' => Carbon::parse($item->last_export)->toDateTimeString(),
                        'epoch' => Carbon::parse($item->last_export)->timestamp,
                        'user' => $item->lastExporter->only('id', 'username'),
                    ];
                }

                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'creator' => $item->creator->only('id', 'username'),
                    'meta' => [
                        'created_at' => [
                            'value' => $item->created_at->toDateTimeString(),
                            'epoch' => $item->created_at->timestamp,
                            'user' => $item->creator->only('id', 'username'),
                        ],
                        'last_update' => $last_update,
                        'last_export' => $last_export,
                    ],
                ];
            }),
        ]);
    }

    /**
     * This function is responsible for creating a document.
     *
     * @param Request $request - information about the document being created
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function post(Request $request)
    {
        // Get the title from the request
        $title = $request->get('title');

        // If a title was not specified, send a bad request response back to the user
        if (!$title) {
            return response()->json([
                'success' => false,
                'status' => 400,
                'error' => [
                    'title' => 'Bad Request',
                    'message' => 'A title must be specified to create a document.',
                ],
            ], 400);
        }

        $document = Document::create([
            'creator_id' => Auth::id(),
            'title' => $title,
        ]);

        // Start looping through the data and attaching it to the document
        foreach ($request->get('data', []) as $key => $value) {
            $type = null;
            $val = null;

            // Check to see if an array was passed in. If it was, we need to check to make sure
            // that a valid type was given, and that a value was specified.
            if (is_array($value)) {
                // If a type was passed in, but is not a valid type, we need to return a bad request
                if (isset($value['type']) && !in_array($value['type'], ['date','number','string'])) {
                    return response()->json([
                        'success' => false,
                        'status' => 400,
                        'error' => [
                            'title' => 'Bad Request',
                            'message' => "'{$value['type']}' is not a valid type of data point.",
                        ],
                    ], 400);
                }

                // If a value was not passed in, we need to return an error
                if (!array_key_exists('value', $value)) {
                    return response()->json([
                        'success' => false,
                        'status' => 400,
                        'error' => [
                            'title' => 'Bad Request',
                            'message' => "A value for '{$key}' was not set.",
                        ],
                    ], 400);
                }

                // If both items are fine, we can go ahead and create our value. We loop through the potential
                // types so that we can modify the data as necessary. I'm not the most pleased with how I left
                // the type inferring in this application. If possible, I would have refactored this part some
                // to properly return ints/floats/strings etc, instead of only strings.
                switch ($value['type']) {
                    case 'string':
                        $val = $value['value'];
                        break;

                    case 'number':
                        $type = 'number';
                        $val = $value['value'];
                        break;

                    case 'date':
                        $type = 'date';
                        $date = $value['value'];
                        $val = Carbon::parse($date)->timestamp;
                        break;

                    // For the default, we just store the data (just like string)
                    default:
                        $val = $value['value'];
                        break;
                }
            } else {
                // If we aren't dealing with an array, we can just store the value directly
                $val = $value;
            }

            $document->data()->create([
                'key' => $key,
                'type' => $type ?? 'string',
                'value' => $val,
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => 200,
            'document' => [
                'id' => $document->id,
                'title' => $document->title,
                'creator' => $document->creator->only('id', 'username'),
                'meta' => [
                    'created_at' => [
                        'value' => $document->created_at->toDateTimeString(),
                        'epoch' => $document->created_at->timestamp,
                        'user' => $document->creator->only('id', 'username'),
                    ],
                    'last_update' => null,
                    'last_export' => null,
                ],
                'data' => $document->data->keyBy('key')->map(function ($item, $key) {
                    return $item->only(['type','value']);
                }),
            ],
        ]);
    }

    /**
     * Return information about a specific document back to the caller
     *
     * @param Request $request - information about the request
     * @param int $id - the ID of the document we are searching for
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request, $id)
    {
        // Attempt to get the document from the database. To make operations quicker, we eager load
        // the relationship data that we will need
        $document = Document::with(['creator','lastModifier','lastExporter','data'])->find($id);

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

        // To see if the document was ever modified, we check the record for the last_modifier_id.
        // If we find a value there, we will return the timestamp based off updated_at, otherwise
        // we will return null.
        $last_update = null;
        if ($document->last_modifier_id) {
            $last_update = [
                'value' => $document->updated_at->toDateTimeString(),
                'epoch' => $document->updated_at->timestamp,
                'user' => $document->lastModifier->only('id', 'username'),
            ];
        }

        // Check to see if this document was ever exported. If it was, we can go ahead and return
        // that timestamp, otherwise we return null.
        $last_export = null;
        if ($document->last_export) {
            $last_export = [
                'value' => Carbon::parse($document->last_export)->toDateTimeString(),
                'epoch' => Carbon::parse($document->last_export)->timestamp,
                'user' => $document->lastExporter->only('id', 'username'),
            ];
        }

        return response()->json([
            'success' => true,
            'status' => 200,
            'document' => [
                'id' => $document->id,
                'title' => $document->title,
                'creator' => $document->creator->only('id', 'username'),
                'meta' => [
                    'created_at' => [
                        'value' => $document->created_at->toDateTimeString(),
                        'epoch' => $document->created_at->timestamp,
                        'user' => $document->creator->only('id', 'username'),
                    ],
                    'last_update' => $last_update,
                    'last_export' => $last_export,
                ],
                'data' => $document->data->keyBy('key')->map(function ($item, $key) {
                    return $item->only(['type','value']);
                }),
            ],
        ]);
    }

    /**
     * This function is used to update a documents data points.
     *
     * @param Request $request - the keys and values of the data we are updating
     * @param int $id - the ID of the document we are updating
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function put(Request $request, $id)
    {
        // Attempt to get the document from the database. To make operations quicker, we eager load
        // the data alongside the document
        $document = Document::with(['creator','lastModifier','lastExporter','data'])->find($id);

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

        // Get the data keys that we will be updating from the request. Once we get the keys, start
        // looping through them and updating them.
        $keys = $request->all();
        $changed = [];
        foreach ($keys as $key => $value) {
            // Make a check to see if we are updating the title of the file. Since the title is stored
            // inside of the document record itself, we don't need to go looping through its data to find
            // that key. If we were to add more metadata in the future, more checks would go here
            if ($key == 'title') {
                $document->title = $value;

                if ($document->isDirty()) {
                    $document->save();
                    $document->wasUpdated();
                    $changed['title'] = $value;
                }
            } else {
                // Since we are not updating metadata, we can search this documents data for the
                // data we are looking for. If we don't find the data right off, we go ahead and
                // create a record for it.
                $data = $document->data()->firstOrCreate(['key' => $key]);
                $data->value = $value;

                // Check to see if our data is dirty. If so, we update the data, and "touch" the parent
                // so that it updated_at column is properly changed.
                if ($data->isDirty()) {
                    $data->save();
                    $document->wasUpdated();
                    $changed[$key] = $value;
                }
            }
        }

        return response()->json([
            'success' => true,
            'status' => 200,
            'changed' => $changed,
        ]);
    }

    /**
     * This function deletes a specific document
     *
     * @param Request $request - information about the request
     * @param int $id - the ID of the document we are deleting
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $id)
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

        // Since we were able to find the document, delete it and return the result back to the user.
        $result = $document->delete();
        if (!$result) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'error' => [
                    'title' => 'Unexpected Error',
                    'message' => 'An unexpected error occured while performing the operation.',
                ],
            ], 500);
        }

        return response()->json([
            'success' => true,
            'status' => 200,
        ]);
    }
}
