# Formstack Programming Challenge
This repository contains my submission for the Formstack coding challenge given by Jakub Jakubiec.

## Getting Started

### Notes About Data
This application was designed to be used with random data that is populated using Laravel's migration functionality. Running `php artisan migrate:refresh --seed` will prep the database, add the 10 predefined users, and create 100 documents with random data, modification dates, and export dates.

### Launching
This application can be provisioned and launched through Docker. To simplify the usage of the application, several of the Docker commands have been wrapped inside of `make` commands. To install the application, run the following commands:

```bash
make build
make seed
```

`make build` will build the docker container, and will launch the application on `localhost:8000`. `make seed` will run the initial migration, and will seed the database with test data, so that the endpoints can be used right away. Here are a full list of quick commands that can be run through the Makefile:

* `make build`: build the container, and launch the application
* `make seed`: run the initial migration to populate test data
* `make kill`: shutdown the app, and remove the docker containers
* `make up`: launch the application
* `make down`: close the application. If done this way, data should be persisted when it is relaunched.
* `make clean`: clean the application of the folders installed by a package manager
* `make rebuild`: shutdown and remove the app, and then rebuild it from scratch
* `make test`: Run the battery of unit tests, then reseed the database with random data. Please note that this command will remove any manually added data.

## Endpoints
This section contains an overview of all the endpoints that are offered by the application. Each of the endpoints will link to more detailed documentation about the endpoint.

**A Note On Authentication :**

The API is wrapped behind HTTP Basic Authentication. When the database is seeded, ten predefined users are added. Their usernames are `user1` through `user10`, and they all have the password `secret`. In case there are issues authenticating the users manually, I have provided each of their headers here so that they can be added to any requests:

|username |header|
|---      |---  |
|*user1*  |`Authorization: Basic dXNlcjE6c2VjcmV0`|
|*user2*  |`Authorization: Basic dXNlcjI6c2VjcmV0`|
|*user3*  |`Authorization: Basic dXNlcjM6c2VjcmV0`|
|*user4*  |`Authorization: Basic dXNlcjQ6c2VjcmV0`|
|*user5*  |`Authorization: Basic dXNlcjU6c2VjcmV0`|
|*user6*  |`Authorization: Basic dXNlcjY6c2VjcmV0`|
|*user7*  |`Authorization: Basic dXNlcjc6c2VjcmV0`|
|*user8*  |`Authorization: Basic dXNlcjg6c2VjcmV0`|
|*user9*  |`Authorization: Basic dXNlcjk6c2VjcmV0`|
|*user10*  |`Authorization: Basic dXNlcjEwOnNlY3JldA==`|

If authenticaion fails, the API will return the following response:

```json
{
  "success": false,
  "status": 401,
  "error": {
    "title": "Invalid Credentials",
    "message": "The provided credentials were not valid."
  }
}
```

### Documents

* [Get All Documents](api_documentation/documents/ALL.md) : `GET /api/documents`
* [Create Document](api_documentation/documents/POST.md) : `POST /api/documents`
* [Get Specific Document](api_documentation/documents/GET.md) : `GET /api/documents/{id}`
* [Update Document](api_documentation/documents/PUT.md) : `PUT /api/documents/{id}`
* [Delete Document](api_documentation/documents/DELETE.md) : `DELETE /api/documents/{id}`

### Document Data

* [Get Document Data Point](api_documentation/documents/data/GET.md) : `GET /api/documents/{id}/data/{key}`
* [Update Document Data Point](api_documentation/documents/data/PATCH.md) : `PATCH /api/documents/{id}/data/{key}`
* [Delete Document Data Point](api_documentation/documents/data/DELETE.md) : `DELETE /api/documents/{id}/data/{key}`

### Document Export

* [Download Document](api_documentation/documents/export/GET.md) : `GET /api/documents/{id}/export`
* [Export Document](api_documentation/documents/export/POST.md) : `POST /api/documents/{id}/export`

## Future Iterations
This section contains my thoughts on the "Future Iterations" section of the spec sheet.

* Ability to export to different document formats (XLS, PDF)
    * I opted to have `ExportController` take care of parsing the format parameter of a download or export request. In future iterations, I would expand the switch statement in that file, and add additional methods to `Document.php` to take care of the different formats that may be required. However, if the codebase kept growing, I would opt for an official `Exporter` class that could be constructed, and could expose methods to the application about what formats were allowed. This exporter would also take care of creating the documents -- whether is be inline/syncronous or queued to be handled by a CLI job.
* Ability to export to other 3rd party cloud storage service (like AWS S3, Dropbox, OneDrive etc.)
    * Similar to the answer above, I opted to have `ExportController` take care of parsing the 3rd party service request. Following the same thought as above, if the application grew, I would either extend the functionality of `ExportController` or I would create the `Exporter` class.
* Ability to add more metadata, for example information about the IP address document was created from
    * I gave this iteration a fair bit of thought. I debated for a while whether or not I wanted to store additional metadata in a "horizontal" (i.e., on the document record), or a "vertical" (i.e., a "meta" table, with values pointing at a document via a foreign key) method. Ultimately, I decided that if it were a small request like an IP address, I would store it on the document record itself, and just update the structure of the table to have that new column. However, if the application grew, and functionality was desired to have more granular control over a documents metadata, I would switch to a vertical approach so that the changes could be tracked better.
* Ability to customize exported file by passing date format as an argument to the export call, which should end up formatting all the date fields properly
    * I decided to add this functionality in as I was building the application. The `export` endpoints have an option to specify the format of the dates in the file, and as the CSV is built, is a date record is encountered, it is formatted properly.

## Usage

### Paw
This repository contains a `.paw` file that will work with [Paw](https://paw.cloud/). This file contains working examples of the endpoints that the application exposes. The file makes an assumption that the application is being run from its Docker container; as such, the hostname for each of the endpoints has been set to `localhost:8000`. If you want to use the file, but are not running the application using Docker, you will need to update that URL to point to the necessary hostname.

## Additional Notes
This section contains some notes about the application. There are certain steps / considerations that I either did, or did not do in this application, and this section reflects on the "What" and the "Why" of certain parts of the application.

* **Fake Data:** As mentioned above, by default this application generates fake data each time it is launched. This functionality can be disabled if need be.
* **Human Readable IDs:** This application uses human readable IDs for it's endpoints. I chose this because of the nature of the project, and to make it easier to test / look through. In a real-world environment, I would opt for some form of hashing (along with other security), so that a user could not guess a document at random and view its data  
* **Soft Deleting:** This application is set to "soft delete" documents and data using [Laravel's SoftDeltes trait](https://laravel.com/docs/5.6/eloquent#soft-deleting). I opted for this functionality to protect documents and data from accidental deletion, both in this test project, and in the potential real-world application.
* **Deleted By Field:** Along the lines of the note above, if this were a real-world application, I would add a `deleted_by` field on a document, so that the deletion could be tracked to a user.
* **Non-Restrictive Deleting:** This application allows anyone to delete a document. If this application were in production, there would be preventative measures to make sure a user had the appropriate permissions to delete a document.
* **Tracking Exports:** I had debated storing export information inside of an `exports` table, so that a document's URL for given format/service combo could be retrieved. In the real-world I would implement it to save processing power / resource space, but I did not have the time to properly implement it here.
* **API Rate Limiting:** Laravel by default has API rate limiting. I disabled it for this application, so that API requests could always be made.
* **Tracking Data:** In future iterations, I would have added a `user_id` field to data points, so that the documents history could be tracked, and a user could see who made what edits.
* **Data Types:** This application currently only has three types for data (string, number, and date). I would have liked to extend the functionality to add more types (such as floats vs. ints), but I was not able to do so. Additionally, I would have like to have done better about parsing the data. Currently the application tries to infer data types more than I would like. In future iterations I would have liked to have added stricter checking to data types and their values.
* **User Management:** I would have liked to have expanded the functionality of the API to allow management of users (creation, deletion, etc), but unfortunately did not have the time I had hoped.
