<?php
/**
 * Akeeba Strapper
 * A handy distribution of namespaced jQuery, jQuery UI and Twitter
 * Bootstrapper for use with Akeeba components.
 */

defined('_JEXEC') or die();

if(!defined('FOF_INCLUDED'))
{
 if(phpversion() <= '5.3'){
	include_once JPATH_SITE . '/libraries/fof/template/utils.php';
	include_once JPATH_SITE . '/libraries/fof/dispatcher/dispatcher.php';	
	}	
  include_once JPATH_SITE . '/libraries/fof/include.php';
}

class TjAkeebaStrapper
{

    /** @var bool True when jQuery is already included */
    public static $_includedJQuery = false;

    /** @var bool True when jQuery UI is already included */
    public static $_includedJQueryUI = false;

    /** @var bool True when Bootstrap is already included */
    public static $_includedBootstrap = false;

    /** @var array List of URLs to Javascript files */
    public static $scriptURLs = array();

    /** @var array List of script definitions to include in the head */
    public static $scriptDefs = array();

    /** @var array List of URLs to CSS files */
    public static $cssURLs = array();

    /** @var array List of URLs to LESS files */
    public static $lessURLs = array();

    /** @var array List of CSS definitions to include in the head */
    public static $cssDefs = array();

    /** @var string The jQuery UI theme to use, default is 'smoothness' */
    protected static $jqUItheme = 'smoothness';

    /** @var string A query tag to append to CSS and JS files for versioning purposes */
    public static $tag = null;

    /**
     * Is this something running under the CLI mode?
     * @staticvar bool|null $isCli
     * @return null
     */
    public static function isCli()
    {
        static $isCli = null;

        if (is_null($isCli))
        {
            try
            {
                if (is_null(JFactory::$application))
                {
                    $isCli = true;
                }
                else
                {
                    $isCli = version_compare(JVERSION, '1.6.0', 'ge') ? (JFactory::getApplication() instanceof JException) : false;
                }
            }
            catch (Exception $e)
            {
                $isCli = true;
            }
        }

        return $isCli;
    }

	public static function getPreference($key, $default = null)
	{
		static $config = null;

		if(is_null($config))
		{
			// Load a configuration INI file which controls which files should be skipped
			$iniFile = FOFTemplateUtils::parsePath('media://techjoomla_strapper/strapper.ini', true);

			$config = parse_ini_file($iniFile);
		}

		if (!array_key_exists($key, $config))
		{
			$config[$key] = $default;
		}

		return $config[$key];
	}

    /**
     * Loads our namespaced jQuery, accessible through akeeba.jQuery
     */
    public static function jQuery()
    {
        if (self::isCli())
		{
            return;
		}

		$jQueryLoad = self::getPreference('jquery_load', 'auto');
		if (!in_array($jQueryLoad, array('auto', 'full', 'namespace', 'none')))
		{
			$jQueryLoad = 'auto';
		}

        self::$_includedJQuery = true;

		if ($jQueryLoad == 'none')
		{
			return;
		}
		elseif ($jQueryLoad == 'auto')
		{
			if (version_compare(JVERSION, '3.0', 'gt'))
			{
				$jQueryLoad = 'namespace';
				JHtml::_('jquery.framework');
			}
			else
			{
				$jQueryLoad = 'full';
			}
		}

        if ($jQueryLoad == 'full')
        {
            // Joomla! 2.5 and earlier, load our own library
            self::$scriptURLs[] = FOFTemplateUtils::parsePath('media://techjoomla_strapper/js/akeebajq.js');
        }
        else
        {
            self::$scriptURLs[] = FOFTemplateUtils::parsePath('media://techjoomla_strapper/js/namespace.js');
        }
    }

    /**
     * Sets the jQuery UI theme to use. It must be the name of a subdirectory of
     * media/techjoomla_strapper/css or templates/<yourtemplate>/media/techjoomla_strapper/css
     *
     * @param $theme string The name of the subdirectory holding the theme
     */
    public static function setjQueryUItheme($theme)
    {
        if (self::isCli())
		{
            return;
		}

        self::$jqUItheme = $theme;
    }

    /**
     * Loads our namespaced jQuery UI and its stylesheet
     */
    public static function jQueryUI()
    {
        if (self::isCli())
		{
            return;
		}

        if (!self::$_includedJQuery)
        {
            self::jQuery();
        }

        self::$_includedJQueryUI = true;

		$jQueryUILoad = self::getPreference('jqueryui_load', 1);
		if (!$jQueryUILoad)
		{
			return;
		}

		$theme = self::getPreference('jquery_theme', self::$jqUItheme);

        $url = FOFTemplateUtils::parsePath('media://techjoomla_strapper/js/akeebajqui.js');

		self::$scriptURLs[] = $url;
        self::$cssURLs[] = FOFTemplateUtils::parsePath("media://techjoomla_strapper/css/$theme/theme.css");
    }

    /**
     * Loads our namespaced Twitter Bootstrap. You have to wrap the output you want style
     * with an element having the class akeeba-bootstrap added to it.
     */
    public static function bootstrap()
    {
        if (self::isCli())
		{
            return;
		}

		if (version_compare(JVERSION, '3.0', 'gt'))
		{
			$key = 'joomla3';
			$default = 'lite';
		}
		else
		{
			$key = 'joomla2';
			$default = 'full';
		}
		$loadBootstrap = self::getPreference('bootstrap_' . $key, $default);

		if (!in_array($loadBootstrap, array('full','lite','none')))
		{
			if ($key == 'joomla3')
			{
				$loadBootstrap = 'lite';
			}
			else
			{
				$loadBootstrap = 'full';
			}
		}

		if (($key == 'joomla3') && ($loadBootstrap == 'lite'))
		{
			// Use Joomla!'s Javascript
			JHtml::_('bootstrap.framework');
		}

        if (!self::$_includedJQuery)
        {
            self::jQuery();
        }

		if ($loadBootstrap == 'none')
		{
			return;
		}

		self::$_includedBootstrap = true;

		$source = self::getPreference('bootstrap_source', 'css');
		if (!in_array($source, array('css','less')))
		{
			$source = 'css';
		}

        $altCss = array('media://techjoomla_strapper/css/strapper.css');
        if ($loadBootstrap == 'full')
        {
            array_unshift($altCss, 'media://techjoomla_strapper/css/bootstrap.min.css');
						array_unshift($altCss, 'media://techjoomla_strapper/css/bootstrap-responsive.min.css');/*added by manoj*/
            self::$scriptURLs[] = FOFTemplateUtils::parsePath('media://techjoomla_strapper/js/bootstrap.min.js');
			if ($source == 'less')
			{
				self::$lessURLs[] = array('media://techjoomla_strapper/less/bootstrap.j25.less', $altCss);
			}
        }
        else
        {
            array_unshift($altCss, 'media://techjoomla_strapper/css/bootstrap.j3.css');
			if ($source == 'less')
			{
				self::$lessURLs[] = array('media://techjoomla_strapper/less/bootstrap.j3.less', $altCss);
			}
        }

		if ($source == 'css')
		{
			foreach($altCss as $css)
			{
				self::$cssURLs[] = FOFTemplateUtils::parsePath($css);
			}
		}
    }

    /**
     * Adds an arbitraty Javascript file.
     *
     * @param $path string The path to the file, in the format media://path/to/file
     */
    public static function addJSfile($path)
    {
		if (self::isCli())
		{
            return;
		}

        self::$scriptURLs[] = FOFTemplateUtils::parsePath($path);
    }

    /**
     * Add inline Javascript
     *
     * @param $script string Raw inline Javascript
     */
    public static function addJSdef($script)
    {
		if (self::isCli())
		{
            return;
		}

        self::$scriptDefs[] = $script;
    }

    /**
     * Adds an arbitraty CSS file.
     *
     * @param $path string The path to the file, in the format media://path/to/file
     */
    public static function addCSSfile($path)
    {
		if (self::isCli())
		{
            return;
		}

        self::$cssURLs[] = FOFTemplateUtils::parsePath($path);
    }

    /**
     * Adds an arbitraty LESS file.
     *
     * @param $path string The path to the file, in the format media://path/to/file
     * @param $altPaths string|array The path to the alternate CSS files, in the format media://path/to/file
     */
    public static function addLESSfile($path, $altPaths = null)
    {
		if (self::isCli())
		{
            return;
		}

        self::$lessURLs[] = array($path, $altPaths);
    }

    /**
     * Add inline CSS
     *
     * @param $style string Raw inline CSS
     */
    public static function addCSSdef($style)
    {
		if (self::isCli())
		{
            return;
		}

        self::$cssDefs[] = $style;
    }

}

/**
 * This is a workaround which ensures that Akeeba's namespaced JavaScript and CSS will be loaded
 * wihtout being tampered with by any system pluign. Moreover, since we are loading first, we can
 * be pretty sure that namespacing *will* work and we won't cause any incompatibilities with third
 * party extensions loading different versions of these GUI libraries.
 *
 * This code works by registering a system plugin hook :) It will grab the HTML and drop its own
 * JS and CSS definitions in the head of the script, before anything else has the chance to run.
 *
 * Peace.
 */
function TjAkeebaStrapperLoader()
{
    // If there are no script defs, just go to sleep
    if (
        empty(TjAkeebaStrapper::$scriptURLs) &&
        empty(TjAkeebaStrapper::$scriptDefs) &&
        empty(TjAkeebaStrapper::$cssDefs) &&
        empty(TjAkeebaStrapper::$cssURLs) &&
        empty(TjAkeebaStrapper::$lessURLs)
    )
    {
        return;
    }

    // Get the query tag
    $tag = TjAkeebaStrapper::$tag;
    if (empty($tag))
    {
        $tag = '';
    }
    else
    {
        $tag = '?' . ltrim($tag, '?');
    }

    $myscripts = '';

	$preload = (bool)TjAkeebaStrapper::getPreference('preload_joomla2', 1);

    if (version_compare(JVERSION, '3.0', 'lt') && $preload)
    {
        $buffer = JResponse::getBody();
    }
	else
	{
		$preload = false;
	}

    // Include Javascript files
    if (!empty(TjAkeebaStrapper::$scriptURLs))
        foreach (TjAkeebaStrapper::$scriptURLs as $url)
        {
            if ($preload && (basename($url) == 'bootstrap.min.js'))
            {
                // Special case: check that nobody else is using bootstrap[.min].js on the page.
                $scriptRegex = "/<script [^>]+(\/>|><\/script>)/i";
                $jsRegex = "/([^\"\'=]+\.(js)(\?[^\"\']*){0,1})[\"\']/i";
                preg_match_all($scriptRegex, $buffer, $matches);
                $scripts = @implode('', $matches[0]);
                preg_match_all($jsRegex, $scripts, $matches);
                $skip = false;
                foreach ($matches[1] as $scripturl)
                {
                    $scripturl = basename($scripturl);
                    if (in_array($scripturl, array('bootstrap.min.js', 'bootstrap.js')))
                    {
                        $skip = true;
                    }
                }
                if ($skip)
                    continue;
            }
            if (version_compare(JVERSION, '3.0', 'lt') && $preload)
            {
                $myscripts .= '<script type="text/javascript" src="' . $url . $tag . '"></script>' . "\n";
            }
            else
            {
                JFactory::getDocument()->addScript($url . $tag);
            }
        }

    // Include Javscript snippets
    if (!empty(TjAkeebaStrapper::$scriptDefs))
    {
        if (version_compare(JVERSION, '3.0', 'lt') && $preload)
        {
            $myscripts .= '<script type="text/javascript" language="javascript">' . "\n";
        }
        else
        {
            $myscripts = '';
        }
        foreach (TjAkeebaStrapper::$scriptDefs as $def)
        {
            $myscripts .= $def . "\n";
        }
        if (version_compare(JVERSION, '3.0', 'lt') && $preload)
        {
            $myscripts .= '</script>' . "\n";
        }
        else
        {
            JFactory::getDocument()->addScriptDeclaration($myscripts);
        }
    }

    // Include LESS files
    if (!empty(TjAkeebaStrapper::$lessURLs))
    {
        foreach (TjAkeebaStrapper::$lessURLs as $entry)
        {
            list($lessFile, $altFiles) = $entry;

            $url = FOFTemplateUtils::addLESS($lessFile, $altFiles, true);

            if (version_compare(JVERSION, '3.0', 'lt') && $preload)
            {
                if (empty($url))
                {
                    if (!is_array($altFiles) && empty($altFiles))
                    {
                        $altFiles = array($altFiles);
                    }
                    if (!empty($altFiles))
                    {
                        foreach ($altFiles as $altFile)
                        {
                            $url = FOFTemplateUtils::parsePath($altFile);
                            $myscripts .= '<link type="text/css" rel="stylesheet" href="' . $url . $tag . '" />' . "\n";
                        }
                    }
                }
                else
                {
                    $myscripts .= '<link type="text/css" rel="stylesheet" href="' . $url . $tag . '" />' . "\n";
                }
            }
            else
            {
                if (empty($url))
                {
                    if (!is_array($altFiles) && empty($altFiles))
                    {
                        $altFiles = array($altFiles);
                    }
                    if (!empty($altFiles))
                    {
                        foreach ($altFiles as $altFile)
                        {
                            $url = FOFTemplateUtils::parsePath($altFile);
                            JFactory::getDocument()->addStyleSheet($url . $tag);
                        }
                    }
                }
                else
                {
                    JFactory::getDocument()->addStyleSheet($url . $tag);
                }
            }
        }
    }

    // Include CSS files
    if (!empty(TjAkeebaStrapper::$cssURLs))
        foreach (TjAkeebaStrapper::$cssURLs as $url)
        {
            if (version_compare(JVERSION, '3.0', 'lt') && $preload)
            {
                $myscripts .= '<link type="text/css" rel="stylesheet" href="' . $url . $tag . '" />' . "\n";
            }
            else
            {
                JFactory::getDocument()->addStyleSheet($url . $tag);
            }
        }

    // Include style definitions
    if (!empty(TjAkeebaStrapper::$cssDefs))
    {
        $myscripts .= '<style type="text/css">' . "\n";
        foreach (TjAkeebaStrapper::$cssDefs as $def)
        {
            if (version_compare(JVERSION, '3.0', 'lt') && $preload)
            {
                $myscripts .= $def . "\n";
            }
            else
            {
                JFactory::getDocument()->addScriptDeclaration($def . "\n");
            }
        }
        $myscripts .= '</style>' . "\n";
    }

    if (version_compare(JVERSION, '3.0', 'lt') && $preload)
    {
        $pos = strpos($buffer, "<head>");
        if ($pos > 0)
        {
            $buffer = substr($buffer, 0, $pos + 6) . $myscripts . substr($buffer, $pos + 6);
            JResponse::setBody($buffer);
        }
    }
}

// Add our pseudo-plugin to the application event queue
if (!TjAkeebaStrapper::isCli())
{
	$preload = (bool)TjAkeebaStrapper::getPreference('preload_joomla2', 1);
    $app = JFactory::getApplication();
    if (version_compare(JVERSION, '3.0', 'lt') && $preload)
    {
        $app->registerEvent('onAfterRender', 'TjAkeebaStrapperLoader');
    }
    else
    {
        $app->registerEvent('onBeforeRender', 'TjAkeebaStrapperLoader');
    }
}
