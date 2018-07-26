# Delete Document Data Point
This endpoint is used to delete a specific data point within a document. It requires the ID of the document we are working with, and the key of the data we are deleting. 

**URL :** `/api/documents/{id}/data/{key}`

**Method :** `DELETE`

**Required Params :**

|Name     | Type    | Description |
|---      |---      |---          |
|id       |int      |The ID of the document we are retrieving|
|key      |string   |The key of the data we are deleting|

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

```json
{
  "success": false,
  "status": 404,
  "error": {
    "title": "Not Found",
    "message": "The requested data point was not found."
  }
}
```

