# Create Document
This endpoint is used to create a document. It accepts the title of the document, and an optional array of data points. If the document creation is successful, the end point will return the document to the user. 

**URL :** `/api/documents`

**Method :** `POST`

**Required Params :** 

|Name     | Type    | Description |
|---      |---      |---          |
|title    |string   |The title of the document|

**Optional Params :**

|Name     | Type    | Description |
|---      |---      |---          |
|data     |array    |Array of data points (see below)|

**Data Points :**
Data points are defined using one of the two following formats:

```json
{
  "<key>": "<value>"
}
```

or 

```json
{
  "<key>": {
    "type":"string|number|date",
    "value":"<value>"
  }
}
```

The first style will cause the type of the data point to be inferred as a string. If you would like control over the data type, you are able to use the second method. If you use the second method, and pass in a `date` type, the string will be converted to an epoch timestamp before being stored.

**Sample Request :**
A sample request using both types of data points could look like the following

```bash
POST /api/documents HTTP/1.1
Content-Type: application/json; charset=utf-8
Host: formstack.devs
Connection: close
User-Agent: Paw/3.1.7 (Macintosh; OS X/10.13.5) GCDHTTPRequest
Content-Length: 94

{
  "title":"Test Document Title",
  "data":{
    "string":"Here is a string",
    "number":{
      "type":"number",
      "value":42},
    "date":{
      "type":"date",
      "value":"2018-01-01 12:34:56"
    }
  }
}
```

**Success Response :**
```json
{
  "success": true,
  "status": 200,
  "document": {
    "id": 135,
    "title": "Test Document Title",
    "creator": {
      "id": 1,
      "username": "gislason.karl"
    },
    "meta": {
      "created_at": {
        "value": "2018-07-24 17:41:07",
        "epoch": 1532454067,
        "user": {
          "id": 1,
          "username": "gislason.karl"
        }
      },
      "last_update": null,
      "last_export": null
    },
    "data": {
      "string": {
        "type": "string",
        "value": "Here is a string"
      },
      "number": {
        "type": "number",
        "value": "42"
      },
      "date": {
        "type": "date",
        "value": "1514810096"
      }
    }
  }
}
```

**Error Response :**
```json
{
  "success": false,
  "status": 400,
  "error": {
    "title": "Bad Request",
    "message": "A title must be specified to create a document."
  }
}
```

```json
{
  "success": false,
  "status": 400,
  "error": {
    "title": "Bad Request",
    "message": "'numbers' is not a valid type of data point."
  }
}
```

```json
{
  "success": false,
  "status": 400,
  "error": {
    "title": "Bad Request",
    "message": "'A value for key2' was not set."
  }
}
```

