# Export Document
This endpoint is used to download a document. It returns a document in a specified format (currently only CSV) with the specified name to the specified service (default S3).

**URL :** `/api/documents/{id}/export`

**Method :** `POST`

**Required Params :**

|Name     | Type    | Description |
|---      |---      |---          |
|id       |int      |The ID of the document we are exporting|

**Optional Params :**

|Name     | Type    | Description |
|---      |---      |---          |
|format   |string   |The format of the file we should export (Default is CSV)|
|service  |string   |The service we will be exporting the document to (Default is S3)|
|name     |string   |What the document should be named|
|date_format|string |How we should format the dates in the file|

**Success Response :**
```json
{
  "success": true,
  "status": 200,
  "url": "https:\/\/formstack-challenge.s3.amazonaws.com\/name.csv"
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
  "status": 400,
  "error": {
    "title": "Bad Request",
    "message": "The requested format 'bad_format' is not valid."
  }
}
```

```json
{
  "success": false,
  "status": 400,
  "error": {
    "title": "Bad Request",
    "message": "The requested service 'bad_service' is not valid."
  }
}
```
