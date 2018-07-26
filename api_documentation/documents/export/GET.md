# Download Document
This endpoint is used to download a document. It returns a document in a specified format (currently only CSV) with the specified name.

**URL :** `/api/documents/{id}/export`

**Method :** `GET`

**Required Params :**

|Name     | Type    | Description |
|---      |---      |---          |
|id       |int      |The ID of the document we are exporting|

**Optional Params :**

|Name     | Type    | Description |
|---      |---      |---          |
|format   |string   |The format of the file we should export (Default is CSV)|
|name     |string   |What the document should be named|
|date_format|string |How we should format the dates in the file|

**Success Response :**
```bash
HTTP/1.1 200 OK
Date: Tue, 24 Jul 2018 02:07:18 GMT
Server: Apache/2.4.18 (Ubuntu)
Cache-Control: public
Last-Modified: Tue, 24 Jul 2018 02:07:18 GMT
Content-Disposition: attachment; filename=document-1-export-1532398038.csv
Content-Length: 936
Accept-Ranges: bytes
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 58
Vary: Accept-Encoding
Connection: close
Content-Type: text/plain;charset=UTF-8

"1989-11-19 08:27:39","2018-07-24 02:03:43"
key,value
quas,"Dignissimos nemo architecto fugiat eveniet eos rem id. Ratione rem sunt doloremque voluptatem est tenetur."
alias,"1986-09-16 08:34:06"
enim,47.506672078
doloremque,54750703.3076
ut,4
vitae,"Id blanditiis non dignissimos dolore laboriosam dicta ut ad. Praesentium eligendi ut doloremque consequuntur. Dicta ratione totam sint aut sunt exercitationem."
veniam,"Voluptate hic voluptatum ut cum perspiciatis asperiores atque. Veritatis et sed libero totam quam."
voluptatem,"Dolorum dolorem praesentium est. Illo eligendi aliquam architecto autem neque placeat. Maiores libero aperiam id minima ad vel inventore. Amet amet id id repudiandae ut dolores est asperiores."
minima,"Eius molestiae et aliquid provident odit. Consequuntur numquam temporibus illum sint et facilis odit. Velit vel laboriosam autem ipsum ut illum sit. Ea magni impedit porro ut."
et,"2011-08-18 06:51:42"
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
