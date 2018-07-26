# Update Document
This endpoint is used to update a document. It requires the ID of the document that is being updated, and the key/value pairs that are being updated. If the key is found in the documents data, its value will be updated. If the key is not found in the documents data, that key/value pair will be created. If the request is successful the API will return the data that was changed.

**URL :** `/api/documents/{id}`

**Method :** `PUT`

**Required Params :**

|Name     | Type    | Description |
|---      |---      |---          |
|id       |int      |The ID of the document we are retrieving|
|"<key>"  |string   |The key of the data we are updating|

**Optional Params :** None

**Success Response :**
```json
{
  "success": true,
  "status": 200,
  "changed": {
    "testKey": "testKeyValUpdated"
  }
}
```

**Error Response :**
```json
{
  "success": false,
  "status": 404,
  "error": {
    "title": "Not Found",
    "message": "The requested document was not found."
  }
}
```

