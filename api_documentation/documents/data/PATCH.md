# Get Document Data Point
This endpoint is used to update a specific data point within a document. It requires the ID of the document we are working with, and the key of the data we are deleting. If also requires either a new type, or a new value for the datapoint.

**URL :** `/api/documents/{id}/patch/{key}`

**Method :** `GET`

**Required Params :**

|Name     | Type    | Description |
|---      |---      |---          |
|id         |int      |The ID of the document we are retrieving|
|key        |string   |The key of the data we are deleting|
|type|value |string   |The new type or value for the data point. At least one is required|

**Optional Params :** None

**Success Response :**
```json
{
  "success": true,
  "status": 200,
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

```json
{
  "success": false,
  "status": 400,
  "error": {
    "title": "Bad Request",
    "message": "Please provide either a new type or a new value for this data point."
  }
}
```
