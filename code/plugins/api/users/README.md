This Joomla user API will return users details based on search criteria

## Common API Structure
APIs has the following characteristics:
 - REST based (JSON over HTTPS)

## HTTP Methods

The APIs follow the standard HTTP methods to bind to operations as follows:

 - GET - Read only operations, one or more resources
 - POST - Create Operation, insert a particular resource
 - PUT - Update operation (e.g. update student data)
 - PATCH - Partial update of an object (only given fields are updated)
 - DELETE - Delete the object and return current state

## Status Code

The APIs differentiate between HTTP status code and the result of the operation. Successful execution of the API returns HTTP response code 200. 
In a case of failure, the response body contains JSON encoded error details with the appropriate error code as listed below. In general, the APIs do not go down to fine-grained status code (e.g. to differentiate between 200 - success and 201 - created).

 - 200 - Completed successfully (results may contain warnings, but no errors)
 - 400 - Client side error - client may fix the error (e.g. bad data) and retry
 - 404 - Resource not found - when a particular object ID is invalid
 - 500 - Server-side error - client need not retry the request, most likely it will fail again
 
The response body contains appropriate error details even in a case of 400 or 500 responses, indicating the exception encountered:

## The API has the following resources:

 - ```v3/users``` (for search/browse)
 - ```/v3/users/:id``` (for user details)
 - ```/v3/users/create``` (for creating a new user)
 - ```/v3/users/update``` (for updating a new user) 

`Method - POST` [ Internal URL ] 
- `Endpoint - index.php?option=com_api&app=users&resource=user&format=raw`

This API takes optional parameters for 'limit' to control the number of results to be returned. Additional search filters can be specified with the request and matching results are returned.

**Request Params**
- `request/query`     - Text query string to match
- `request/filters`   - Additional filters to apply based on the attributes of the objects
- `request/sort`      - sort result - ASC and DESC
- `request/limit`     - Number of results to be returned
- `request/offset` - Index of the first result returned, use '0' to fetch from the first result

**Search filters field**

| Search field | Required | Comment |
| ---------- | -------- | ------- |
| search     | NO | Query string (search by user name) | 
| userState  | NO | |
| id         | NO | |
| email      | NO | |
| username   | NO | |

**NOTE** 
 - All fields are optional but while making APIs call at least one field should not be empty  

**Request body**
```javascript
{
"request": { 
      "search":"",
      "filters":{
           "block" : [0, 1],
           "id": [1,2,3],
           "email":["john@mailinator.com", "peter@mailinator.com"],
           "username":["john"]
       },
      "sort_by":{"name":"asc"}, // sort by id,name, email, username
      "offset":0,
      "limit":10
  }
}
```

**Response body**
```javascript
{
  "err_code" : "", 
  "err_message" : "", 
    "data": {
      "results": [
       {
         "id": "1",
         "name": "john",
         "email": "john@mailinator.com",
         "username": "john@mailinator.com",
         "registerDate" : "2016-02-25 08:44:48",
         ...
       },
       {
         "id": "2",
         "name": "peter",
         "email": "peter@mailinator.com",
         "username": "peter@mailinator.com",
         "registerDate" : "2017-02-25 08:44:48",
         ...
       },
       ...
     ]
    }
}
```

**Note**
 - Any of the attributes of user model can be used in the filters.
 - This API will not return the relations of the user(s). For eg, member of any EkStep organizations

**Operators**

Filters are by default searched based on contains a match, that is case insensitive but the request can specify other operators to search against the fields. These are as follows:

1. String fields
   - Default - String match, contains, case insensitive
   - Array   - Exact match with any of the given values

2. Number fields

**Examples**

> Sample 1 - Find only registered users. 
```javascript
{
  "request": { 
      "query":"jhon",
       "filters":{
           "userGroup": "Registered"
       },
      "sort_by":{"name":"asc"},
      "limit":10
  }
}
```
> Sample 2 - Find only Content creators
```javascript
{
  "request": {
     "filters": {
        "userGroup": "Content-creator"
     },
      "sort_by":{"name":"asc"},
      "limit":10
  }
}
```

> Sample 3 - Find all registered users whose contains "A" (e.g. for alphabetic navigation)
```javascript
{
  "request": {
     "filters": {
        "userGroup": "Registered"
        "name": {
          "contains": "A"
        }
     },
      "sort_by":{"name":"asc"},
      "limit":10
  }
}
```

