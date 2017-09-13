# Writing your own API Plugin
Each resorce can support the GET, POST and DELETE operations. These are exposed by creating methods of the same name, i.e. `get()` `post()` and `delete()` in each of the resources. If a resouce URL is accessed via HTTP POST, the post() method is called, and similarly for the rest.

### API plugin file structure
* language/en-GB - Resource folder having resource file, keep name same as plugin name.
	- en-GB.plg_api_users.ini - add plugin language constant.
	- en-GB.plg_api_users.sys.ini
* users - Resource folder having resource file, keep name same as plugin name.
	- login.php - Resource file
	- users.php - Resource file
* users.php - plugin file
* users.xml - xml file 

You can add multiple resource in resource folder and use them for different purpose. Usually, each resource will map to an object type for your extension.

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
		$lang->load('com_users', JPATH_ADMINISTRATOR, '', true);
		
		// Set the login resource to be public
		$this->setResourceAccess('login', 'public', 'post');
	}
}
```

### Create resource file login.php file
Although you can place the resource files anywhere, the recommended approach is to place them within a folder inside your plugin.  Below is example code for a resource file. Notice how the methods get() and post() are implemented. The methods may return an array or an object which will be automatically converted to JSON or XML.

```php
<?php
class UsersApiResourceLogin extends ApiResource
{
	public function get()
	{
        $result = new \stdClass;
        $result->id = 45;
        $result->name = "John Doe"
		 
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
		ApiError::raiseError(10001, "Invalid Email", 'APIValidationException');

		// Access Error sets HTTP 403
		ApiError::raiseError(11001, "Not authorised", 'APIUnauthorisedException');

		// Not Found Error sets HTTP 404
		ApiError::raiseError(12001, "Record not found", 'APINotFoundException');

		// General Error sets HTTP 400
		ApiError::raiseError(10000, "Bad Request", 'APIException');

	}
```

You are free to specify your own error code and message. It is also possible to add more Exceptions in the `components/com_api/libraries/exceptions` folder. When using `APIError::raiseError()` there is no need to use `$this->plugin->setResponse()` since com_api handles the response and setting the http code.

Note : The exception classes extend PHP's `Exception` class. So you will need to use numeric only codes, since PHP does not support non-numeric Exception codes. 


### Private and public resources
 
Unless specified, all resources are private, which means an API token is needed to access. However, it is possible to make certain resource and methods public by using the setResourceAccess() access method as
```php
$this->setResourceAccess('login', 'public', 'post') 
```

The first parameter is the resource name, second is status (should be public to make it public) and last is HTTP method to make public. Setting a resource public will mean that the API URL for that resource will not need any authentication.

### Access Control
ACL needs to be handled by the respective plugins. com_api makes a `$this->user` object available in the resource class. This is same as the JFactory::getUser() object for the user to whom the token belongs. It is upto the resource to use the user object and apply the necessary access control and produce authorisation errors.

```php
<?php
class ExamplesApiResourceExample extends ApiResource
{
	public function get()
	{
		// Will dump the object for the user who is making the API call
		var_dump($this->user);
	}
}
```

### Create .xml file
Finally create a manifest XML so that your plugin can be installed. Set group as 'api', add plugin name and other details.

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension version="3.0.0" type="plugin" group="api" method="upgrade">
    <name>YourPlugin</name>
    <version>1.0</version>
    <creationDate>10/11/2016</creationDate>
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
- Think of API plugins as a replacement of controllers. Any business logic that you won't put in a controller, leave it out of the plugin too. Load and use your models in the plugin code.
- It is not recommended to have API specific language files unless absolutely necessary. You will ususally make plugins for an existing component, so load the language files from that component.
- To create the list and details for an object type, you can either add a condition based on `id` query parameter in the `get()` method, or have a separate resource for the list. 
