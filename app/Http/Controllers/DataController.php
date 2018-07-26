<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Http\Request;

class DataController extends Controller
{
    /**
     * This function is used to fetch a specific data point within a document.
     *
     * @param Request $request - information about the request
     * @param int $id - the ID of the document that we are working with
     * @param string $key - the key of the data we wish to delete
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request, $id, $key)
    {
        // Attempt to get the document from the database. Since we are working with data,
        // we go ahead and eager load it here.
        $document = Document::with('data')->find($id);

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

        // Now that we have the document, we can check its data for the key we are looking for.
        $data = $document->data()->where('key', $key)->first();

        // If we don't have the data point, return that back to the user.
        if (!$data) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'error' => [
                    'title' => 'Not Found',
                    'message' => 'The requested data point was not found.',
                ],
            ], 404);
        }

        // Otherwise return the value back to the user.
        return response()->json([
            'success' => true,
            'status' => 200,
            'value' => $data->value,
        ]);
    }

    /**
     * This function is used to update a specific data point within a document.
     *
     * @param Request $request - the new type/value of the datapoint
     * @param int $id - the ID of the document that we are working with
     * @param string $key - the key of the data we wish to delete
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function patch(Request $request, $id, $key)
    {
        // Attempt to get the document from the database. Since we are working with data,
        // we go ahead and eager load it here.
        $document = Document::with('data')->find($id);

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

        // Now that we have the document, we can check its data for the key we are looking for.
        $data = $document->data()->where('key', $key)->first();

        // If we don't have the data point, return that back to the user.
        if (!$data) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'error' => [
                    'title' => 'Not Found',
                    'message' => 'The requested data point was not found.',
                ],
            ], 404);
        }

        // Check to make sure we are updating either the type or the value of the data point
        $type = $request->get('type');
        $value = $request->get('value');
        if (!$type && !$value) {
            return response()->json([
                'success' => false,
                'status' => 400,
                'error' => [
                    'title' => 'Bad Request',
                    'message' => 'A new type or a new value for this data point must be provided.',
                ],
            ], 400);
        }

        $data->type = $type;
        $data->value = $value;

        if ($data->isDirty()) {
            $data->save();
            $document->wasUpdated();
        }

        return response()->json([
            'success' => true,
            'status' => 200,
        ]);
    }

    /**
     * This function is used to delete a specific data point within a document.
     *
     * @param Request $request - information about the request
     * @param int $id - the ID of the document that we are working with
     * @param string $key - the key of the data we wish to delete
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $id, $key)
    {
        // Attempt to get the document from the database. Since we are working with data,
        // we go ahead and eager load it here.
        $document = Document::with('data')->find($id);

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

        // Now that we have the document, we can check its data for the key we are looking for.
        $data = $document->data()->where('key', $key)->first();

        // If we don't have the data point, return that back to the user.
        if (!$data) {
            return response()->json([
                'success' => false,
                'status' => 404,
                'error' => [
                    'title' => 'Not Found',
                    'message' => 'The requested data point was not found.',
                ],
            ], 404);
        }

        // Otherwise, delete the datapoint and return a success back to the user. Since deleting
        // data is technically a modification, we update the meta values in the document.
        $data->delete();
        $document->wasUpdated();

        return response()->json([
            'success' => true,
            'status' => 200,
        ]);
    }
}
