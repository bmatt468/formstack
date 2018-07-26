# Get Document
This endpoint is used to retrieve a specific document. It returns the document, it's metadata, and the data associated with the document.

**URL :** `/api/documents/{id}`

**Method :** `GET`

**Required Params :**

|Name     | Type    | Description |
|---      |---      |---          |
|id       |int      |The ID of the document we are retrieving|

**Optional Params :** None

**Success Response :**
```json
{
  "success": true,
  "status": 200,
  "document": {
    "id": 15,
    "title": "Et fugit dolor dolorem vel. Tempore eligendi ex ipsa eius cumque officia. Odio expedita quod assumenda aliquam omnis.",
    "creator": {
      "id": 10,
      "username": "altenwerth.clay"
    },
    "meta": {
      "created_at": {
        "value": "2007-08-20 01:34:36",
        "epoch": 1187573676,
        "user": {
          "id": 10,
          "username": "altenwerth.clay"
        }
      },
      "last_update": {
        "value": "2010-10-23 12:37:34",
        "epoch": 1287837454,
        "user": {
          "id": 7,
          "username": "farrell.laisha"
        }
      },
      "last_export": null
    },
    "data": {
      "sint": {
        "type": "number",
        "value": "45946"
      },
      "debitis": {
        "type": "string",
        "value": "Natus dolorum tempora et. Labore debitis qui illo sunt impedit sed earum. Ut similique quos culpa animi."
      },
      "ea": {
        "type": "number",
        "value": "52317352"
      }
    }
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

