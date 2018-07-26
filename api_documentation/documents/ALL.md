# Get Documents
This endpoint returns a JSON payload containing information about all of the registered documents. By default, this endpoint will return 25 documents, unless the count parameter is given.

**URL :** `/api/documents`

**Method :** `GET`

**Required Params :** None

**Optional Params :**

|Name     | Type    | Description |
|---      |---      |---          |
|count    |int      |Max number of records to return|
|page     |int      |The page to start counting records from|

**Success Response :**
```json
{
  "success": true,
  "status": 200,
  "total": 100,
  "per_page": "3",
  "current_page": 4,
  "last_page": 34,
  "documents": [
    {
      "id": 10,
      "title": "Sequi cumque dolores ut atque vel enim placeat. Sed sequi culpa enim debitis et. Itaque sapiente quia esse iste et odio quis ipsum.",
      "creator": {
        "id": 10,
        "username": "dgraham"
      },
      "meta": {
        "created_at": {
          "value": "1987-07-09 19:56:58",
          "epoch": 552859018,
          "user": {
            "id": 10,
            "username": "dgraham"
          }
        },
        "last_update": {
          "value": "2014-09-09 19:55:21",
          "epoch": 1410292521,
          "user": {
            "id": 5,
            "username": "gabrielle20"
          }
        },
        "last_export": null
      }
    },
    {
      "id": 11,
      "title": "Unde illo perspiciatis dolore cumque. Aliquam repudiandae vitae dolor. Qui repellendus quo esse debitis commodi. Modi delectus ut in maxime dolorum quia explicabo exercitationem.",
      "creator": {
        "id": 2,
        "username": "isabell.marquardt"
      },
      "meta": {
        "created_at": {
          "value": "2004-01-24 19:40:22",
          "epoch": 1074973222,
          "user": {
            "id": 2,
            "username": "isabell.marquardt"
          }
        },
        "last_update": {
          "value": "2007-03-24 08:51:13",
          "epoch": 1174726273,
          "user": {
            "id": 8,
            "username": "danial.koss"
          }
        },
        "last_export": {
          "value": "2006-07-09 11:08:23",
          "epoch": 1152443303,
          "user": {
            "id": 7,
            "username": "von.jany"
          }
        }
      }
    },
    {
      "id": 12,
      "title": "Omnis et et est odit. Optio est reiciendis dolore id corrupti. Quasi rerum suscipit dicta et quam modi iusto. Qui et error debitis culpa.",
      "creator": {
        "id": 2,
        "username": "isabell.marquardt"
      },
      "meta": {
        "created_at": {
          "value": "2004-01-28 11:09:48",
          "epoch": 1075288188,
          "user": {
            "id": 2,
            "username": "isabell.marquardt"
          }
        },
        "last_update": null,
        "last_export": null
      }
    }
  ]
}
```

