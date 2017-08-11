# API framework for Joomla

com_api is a quick and easy way to add REST APIs to Joomla. Extendible via plugins, you an easily add support for more Joomla extensions. To get started, download the component and install the API plugins you need. Enable the plugins and you are ready to fetch your content via APIs


# Using APIs

To add additional resources to the API, plugins need to be created. Each plugin can provide multiple API resources. Plugins are a convenient way to group several resources. Eg: A single plugin could be created for Quick2Cart with separate resources for products, cart, checkout, orders etc.

## Plugin Terms

### app
An app is essentially a Joomla plugin. However the plugin itself does nothing more than load the resources it contains. So the app is mainly used to package the API plugin and to enable adding any API specific parameters. Each app will have one or more resources. 

### resource
Resources are files that have code to accept input and set the API output. You will usually have multiple resources in an app. A common use case is for an extension like Easysocial or Jomsocial to have a single app. The app contains resources for various objects like groups, events, photos, newsfeed etc. The resource will contain the methods `get()` `post()` `delete()` to perform CRUD operations on that type of object.

### key / token
The key is used to access authenticated resources. The admin section allows you to create keys. It's also possible to use the `/api/user/login` API to login using username and password and get a token in response.


## Calling resources

### API URLs
The URL to access any route is of the format - 

`/api/{plugin}/{resource}`

Tip : If your resource expects an `id` parameter in the URL then the endpoint URL then you can use `/api/{plugin}/{resource}/{id}` as the API url. Other query parameters may be sent as is.

To enable SEF URLs for endpoints, make sure you have created a Joomla menu of the type API > API Endpoint. If you create the menu using any other alias than `api` make sure you use the apppropriate slug in the endpoint. If you do not have SEF URLs enabled, use  the endpoint URL as index.php?option=com_api&app={app}&resource={resource}&format=raw.

### Authentication
The API token is used for authentication. The token needs to be passed via the Authorization header using the Bearer scheme eg: `Authorization: Bearer <token>`. Previous versions also allowed passing the token as a querystring variable with the name `key`. The querystring approach will be deprecated in the future version. It is possible for an app to make an entire resource or a specific HTTP method in a resource public.

### Output Format
The default output format is JSON. However it's also possible to get XML output by setting the `Accept: application/xml` header.

## Overriding Output
If you wish to modify the 'envelope' of the response, you can copy the file `components/com_api/libraries/response/jsonresponse.php` into `templates/{your template}/json/api.php` and modify the structure of the output.


# Writing your own API Plugin
Each resorce can support the GET, POST, DELETE & PUT (needs some work) operations. These are exposed by creating methods of the same name, i.e. get() post() put() and delete() in each of the resources. This way, if a resouce URL is accessed via HTTP POST , the post() method is called, and similarly for the rest.

### API plugin file structure
* language/en-GB - Resource folder having resource file, keep name same as plugin name.
	- en-GB.plg_api_users.ini - add plugin language constant.
	- en-GB.plg_api_users.sys.ini
* users - Resource folder having resource file, keep name same as plugin name.
	- login.php - Resource file
	- users.php - Resource file
* users.php - plugin file
* users.xml - xml file 

You can add multiple resource in resource folder and use them for different purpose.

### Create plugin entry file users.php file
This is the entry file for the API plugin, the things that re deifned in the file are resource locations, and making certain resources public. Below is the code for the file - 

```php
jimport('joomla.plugin.plugin');
//class structure example
class plgAPIUsers extends ApiPlugin
{
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config = array());
		
		// Set resource path
		ApiResource::addIncludePath(dirname(__FILE__).'/users');
		
		// Load language files
		$lang = JFactory::getLanguage(); 
		$lang->load('plg_api_your_plugin_name', JPATH_ADMINISTRATOR,'',true);
		
		// Set the login resource to be public
		$this->setResourceAccess('your_plugin_name', 'public', 'post');

	}
}
```

### Create resource file login.php file
Although you can place the resource files anywhere, the recommended approach is to place them within a folder inside your plugin.  Below is example code for a resource file. Notice how the methods get() and post() are implemented. The methods may return an array or an object which will be automatically converted to JSON.

```php

<?php
//class structure example
//ex - class UsersApiResourceLogin extends ApiResource
class UsersApiResourceLogin extends ApiResource
{
	public function get()
	{
		// Add your code here
		 
		$this->plugin->setResponse( $result );
	}

	public function post()
	{
		// Add your code here
		
		$this->plugin->setResponse( $result );
	}
}
```

The array or object from the plugin should be set via `$this->plugin->setResponse()`.

### Error Handling
It is possible to send HTTP errors with the right HTTP codes using the `APIError::raiseError()` method. Depending on the type of error you can raise different Exceptions that set the appropriate HTTP code. 

```php
<?php
	public function post()
	{
		// Validation Error sets HTTP 400
		ApiError::raiseError("VAL001", "Invalid Email", 'APIValidationException');

		// Access Error sets HTTP 403
		ApiError::raiseError("ACC001", "Not authorised", 'APIUnauthorisedException');

		// Not Found Error sets HTTP 404
		ApiError::raiseError("ERR005", "Record not found", 'APINotFoundException');

		// General Error sets HTTP 400
		ApiError::raiseError("ERR001", "Bad Request", 'APIException');

	}
```

You are free to specify your own error code and message. It is also possible to add more Exceptions in the `site/libraries/exceptions` folder. When using `APIError::raiseError()` there is no need to use `$this->plugin->setResponse()`. com_api will take care of sending the right HTTP code and error messages. 


### Make some resources public
 
It is possible to make certain resource method public by using the setResourceAccess() access method as
```php
$this->setResourceAccess('users', 'public', 'post') 
```

The first parameter is the resource name, second is status (should be public to make it public and last is method,
which is you want to make public. Setting a resource public will mean that the URL will not need any authentication.
  

### Create .xml file
Finally create a manifest XML so that your plugin can be installed. Set group as 'api', add plugin name and other details.

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension version="3.0.0" type="plugin" group="api" method="upgrade">
    <name>YourPlugin</name>
    <version>1.0</version>
    <creationDate>10/11/2014</creationDate>
    <author></author> 
    <description></description>
    <files>
        <filename plugin="your_plugin_name">your_plugin_name.php</filename>
        <folder>your_plugin_name</folder> 
    </files>
    <languages folder="language">
		<language tag="en-GB">en-GB/en-GB.plg_api_plugin_name.ini</language>
		<language tag="en-GB">en-GB/en-GB.plg_api_plugin_name.sys.ini</language>
	</languages>
	
</extension> 
```

### Tips for writing plugins
- Think of API plugins as a replacement of controllers. So any business logic that you won't put in a controller, leave it out of the plugin too.
- It is not recommended to have language files unless absolutely necessary. You will ususally make plugins for an existing component, so load the language files from that component.
- To create the list and details for an object type, you can either add a condition based on `id` query parameter in the `get()` method, or have a separate resource for the list. 
