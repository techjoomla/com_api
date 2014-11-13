<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/
defined('_JEXEC') or die('Restricted access');
JFactory::getApplication()->input->set('tmpl', 'component');
$doc = JFactory::getDocument();
JHTML::stylesheet(JURI::root() . 'components/com_api/libraries/swagger/css/reset.css');
JHTML::stylesheet(JURI::root() . 'components/com_api/libraries/swagger/css/screen.css');


JHTML::script(JURI::root() . 'components/com_api/libraries/swagger/lib/jquery-1.8.0.min.js');
JHTML::script(JURI::root() . 'components/com_api/libraries/swagger/lib/jquery.slideto.min.js');
JHTML::script(JURI::root() . 'components/com_api/libraries/swagger/lib/shred.bundle.js');
JHTML::script(JURI::root() . 'components/com_api/libraries/swagger/lib/jquery.wiggle.min.js');
JHTML::script(JURI::root() . 'components/com_api/libraries/swagger/lib/jquery.ba-bbq.min.js');
JHTML::script(JURI::root() . 'components/com_api/libraries/swagger/lib/handlebars-1.0.0.js');
JHTML::script(JURI::root() . 'components/com_api/libraries/swagger/lib/underscore-min.js');
JHTML::script(JURI::root() . 'components/com_api/libraries/swagger/lib/backbone-min.js');
JHTML::script(JURI::root() . 'components/com_api/libraries/swagger/lib/swagger.js');
JHTML::script(JURI::root() . 'components/com_api/libraries/swagger/swagger-ui.js');
JHTML::script(JURI::root() . 'components/com_api/libraries/swagger/lib/highlight.7.3.pack.js');

$doc_path = JURI::root() . 'components/com_api/documentation/api-docs.json';
$initjs = <<<EOT
    $(function () {
      window.swaggerUi = new SwaggerUi({
      url: "$doc_path",
      dom_id: "swagger-ui-container",
      supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'head'],
      onComplete: function(swaggerApi, swaggerUi){
        log("Loaded SwaggerUI");

        if(typeof initOAuth == "function") {
          /*
          initOAuth({
            clientId: "your-client-id",
            realm: "your-realms",
            appName: "your-app-name"
          });
          */
        }
        $('pre code').each(function(i, e) {
          hljs.highlightBlock(e)
        });
      },
      onFailure: function(data) {
        log("Unable to Load SwaggerUI");
      },
      docExpansion: "none",
      sorter : "alpha"
    });

    $('#input_apiKey').change(function() {
      var key = $('#input_apiKey')[0].value;
      log("key: " + key);
      if(key && key.trim() != "") {
        log("added key " + key);
        window.authorizations.add("key", new ApiKeyAuthorization("api_key", key, "query"));
      }
    })
    window.swaggerUi.load();
  });
EOT;

$doc->addScriptDeclaration($initjs);
?>
<div class="swagger-section">
<div id='header'>
  <div class="swagger-ui-wrap">
    <a id="logo" href="http://swagger.wordnik.com">swagger</a>
    <form id='api_selector'>
      <div class='input icon-btn'>
        <img id="show-pet-store-icon" src="images/pet_store_api.png" title="Show Swagger Petstore Example Apis">
      </div>
      <div class='input icon-btn'>
        <img id="show-wordnik-dev-icon" src="images/wordnik_api.png" title="Show Wordnik Developer Apis">
      </div>
      <div class='input'><input placeholder="http://example.com/api" id="input_baseUrl" name="baseUrl" type="text"/></div>
      <div class='input'><input placeholder="api_key" id="input_apiKey" name="apiKey" type="text"/></div>
      <div class='input'><a id="explore" href="#">Explore</a></div>
    </form>
  </div>
</div>

<div id="message-bar" class="swagger-ui-wrap">&nbsp;</div>
<div id="swagger-ui-container" class="swagger-ui-wrap"></div>
</div>
