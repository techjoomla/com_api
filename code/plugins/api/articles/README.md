API to create and get content

## Create / Update Content

```http
POST /index.php?option=com_api&app=articles&resource=article&format=raw
```
OR update an existing article
```http
POST /index.php?option=com_api&app=articles&resource=article&format=raw&id=:id
```

#### Request Params

| Param Name | Required | Type | Comment  |
| ---------- | -------- | ------- | :---- |
| title      | YES      | STRING |         |
| alias      | NO      | STRING | URL alias. Will be generated based on title if kept empty |
| introtext    | YES      | STRING |        |
| fulltext     | NO      | STRING |        |
| state    | NO      | INT | 1 = Published (Default) / 0 = Unpublished / -1 = Archived |
| catid      | YES      | INT |  Category ID |
| publish_up      | NO      | DATETIME | Defaults to current date/time |
| publish_down | NO | DATETIME | Defaults to 0000-00-00 i.e. never |
| language | NO | STRING |Will use the site's default language if none provided. |


#### Response Params

| Param Name | Comment |
| ---------- | :------ |
| success | true if the article was created, false if there was a problem |
| message | Error mesage in case success is false |
| data.results | Array containing a single [Article Object](#article-object) in case of success. Empty array in case of failure. |

## Get Articles List
```http
GET /index.php?option=com_api&app=articles&resource=article&format=raw
```
#### Request Params

| Param Name | Required | Comment |
| ---------- | -------- | :------ |
| limit         | NO       | Defaults to 20        | 
| limitstart      | NO      | Defaults to 0        |
| filters | NO | Key value pairs of values to filter on |
| search | NO | search key for searching article titles |
| fields         | NO       | Defaults to id, title, modified, created_by, catid | 


#### Response Params

| Param Name | Comment |
| ---------- | :------- |
| success | true if the article was created, false if there was a problem |
| message | Error mesage in case success is false |
| data.results | Array of [Article Objects](#article-object) in case of success. Empty array in case of failure. |
| data.total |  Total should be the total count that match the filters, not the total of items in the current set, i.e. if there are 240 articles matching the filters, and the API returns first 20 then the total should contain 240 not 20. |


## Get Single Article 
```http
GET /index.php?option=com_api&app=articles&resource=article&format=raw&id=:id
```

#### Request Params

| Param Name | Required | Comment |
| ---------- | -------- | :------ |
| fields         | NO       | Defaults to id, title, modified, created_by, catid | 


#### Response Params

| Param Name | Comment  |
| ---------- | :------- |
| success | true if the request succeeds, false if there was a problem |
| message | Error mesage in case success is false |
| data.results | Array containing a single [Article Object](#article-object) in case of success. Empty array in case of failure. |


## Article Object
The actual contents of the article object will vary based on which fields are requested, however the below is the list of all possible fields.

```json
{
  "id" : "",
  "title" : "",
  "alias" : "",
  "introtext" : "",
  "fulltext" : "",
  "catid" : {
    "id" : "",
    "title" : ""
  },
  "state" : "",
  "created" : "",
  "modified" : "",
  "publish_up" : "",
  "publish_down" : "",
  "images" : "",
  "access" : "",
  "featured" : "",
  "language" : "",
  "hits": "",
  "created_by": {
    "id" : "",
    "name" : "",
    "avatar" : ""
  },
  "tags" : {}
}
```
