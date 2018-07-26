# Delete Document
This endpoint is used to delete a specific document. It attempts to delete the document, then returns whether or not that operation was successful.

**URL :** `/api/documents/{id}`

**Method :** `DELETE`

**Required Params :**

|Name     | Type    | Description |
|---      |---      |---          |
|id       |int      |The ID of the document we are retrieving|

**Optional Params :** None

**Success Response :**
```json
{
  "success": true,
  "status": 200
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

