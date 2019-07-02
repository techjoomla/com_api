<?php
/**
 * Script used to make a version bump
 * Updates all versions xmls and version.php
 *
 * Usage: php build/bump.php -v <version> -c <codename>
 *
 * Examples:
 * - php build/bump.php -v 3.6.0-dev
 * - php build/bump.php -v 3.6.0-beta1
 * - php build/bump.php -v 3.6.0-beta1-dev
 * - php build/bump.php -v 3.6.0-beta2
 * - php build/bump.php -v 3.6.0-rc1
 * - php build/bump.php -v 3.6.0
 * - php build/bump.php -v 3.6.0 -c Unicorn
 * - php build/bump.php -v 3.6.0 -c "Custom Codename"
 * - /usr/bin/php /path/to/joomla-cms/build/bump.php -v 3.7.0
 *
 * @package    Techjoomla.Build
 *
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2016 - 2018 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Display the use of command
 *
 * @param   string  $command  The command name
 *
 * @return  void
 */
function usage($command)
{
	echo PHP_EOL;
	echo 'Usage: php ' . $command . ' [options]' . PHP_EOL;
	echo PHP_TAB . '[options]:' . PHP_EOL;
	echo PHP_TAB . PHP_TAB . '-v <version>:' . PHP_TAB . 'Version (ex: 3.6.0-dev, 3.6.0-beta1, 3.6.0-beta1-dev, 3.6.0-rc1, 3.6.0)' . PHP_EOL;
	echo PHP_TAB . PHP_TAB . '-c <codename>:' . PHP_TAB . 'Codename [optional] (ex: Unicorn)' . PHP_EOL;
	echo PHP_EOL;
}

// Constants.
const PHP_TAB = "\t";

// File paths. THe version file path (If applicable)
$versionFile = 'PATH_OF_VERSION_FILE';

// This file will vary from component to component
$coreXmlFiles = array(
	'/code/api.xml',
	'/code/plugins/authentication/tjapi/tjapi.xml',
	'/code/plugins/system/tjtokenlogin/tjtokenlogin.xml'
);

$antJobFile = '/build.xml';

$readMeFiles = array(
	'/README.md',
	'/README.txt',
);

// Change copyright date exclusions. Some systems may try to scan the .git directory, exclude it.
$directoryLoopExcludeDirectories = array(
	'/.git',
	'/.gitlab',
	'/scripts/ansible-deploy',
	'/scripts/gulp',
	'/tests/codeception',
);

$directoryLoopExcludeFiles = array(
	'.gitignore',
	'/build/bump.php',
	'/scripts/phing/build.xml',
);

// Check arguments (exit if incorrect cli arguments).
$opts = getopt("v:c:");

if (empty($opts['v']))
{
	usage($argv[0]);
	die();
}

// Check version string (exit if not correct).
$versionParts = explode('-', $opts['v']);

if (!preg_match('#^[0-9]+\.[0-9]+\.[0-9]+$#', $versionParts[0]))
{
	usage($argv[0]);
	die();
}

if (isset($versionParts[1]) && !preg_match('#(dev|alpha|beta|rc)[0-9]*#', $versionParts[1]))
{
	usage($argv[0]);
	die();
}

if (isset($versionParts[2]) && $versionParts[2] !== 'dev')
{
	usage($argv[0]);
	die();
}

// Make sure we use the correct language and timezone.
setlocale(LC_ALL, 'en_GB');
date_default_timezone_set('Asia/Kolkata');

// Make sure file and folder permissions are set correctly.
umask(022);

// Get version dev status.
$dev_status = 'Stable';

if (!isset($versionParts[1]))
{
	$versionParts[1] = '';
}
else
{
	if (preg_match('#^dev#', $versionParts[1]))
	{
		$dev_status = 'Development';
	}
	elseif (preg_match('#^alpha#', $versionParts[1]))
	{
		$dev_status = 'Alpha';
	}
	elseif (preg_match('#^beta#', $versionParts[1]))
	{
		$dev_status = 'Beta';
	}
	elseif (preg_match('#^rc#', $versionParts[1]))
	{
		$dev_status = 'Release Candidate';
	}
}

if (!isset($versionParts[2]))
{
	$versionParts[2] = '';
}
else
{
	$dev_status = 'Development';
}

// Set version properties.
$versionSubParts = explode('.', $versionParts[0]);

$version = array(
		'main'       => $versionSubParts[0] . '.' . $versionSubParts[1],
		'major'      => $versionSubParts[0],
		'minor'      => $versionSubParts[1],
		'patch'      => $versionSubParts[2],
		'extra'      => (!empty($versionParts[1]) ? $versionParts[1] : '') .
						(!empty($versionParts[2]) ? (!empty($versionParts[1]) ? '-' : '') . $versionParts[2] : ''),
		'release'    => $versionSubParts[0] . '.' . $versionSubParts[1] . '.' . $versionSubParts[2],
		'dev_devel'  => $versionSubParts[2] . (!empty($versionParts[1]) ? '-' .
						$versionParts[1] : '') . (!empty($versionParts[2]) ? '-' . $versionParts[2] : ''),
		'dev_status' => $dev_status,
		'build'      => '',
		'reldate'    => date('j-F-Y'),
		'reltime'    => date('H:i'),
		'reltz'      => 'GMT',
		'credate'    => date('jS M Y')
		);

// Version Codename.
if (!empty($opts['c']))
{
	$version['codename'] = trim($opts['c']);
}

// Prints version information.
echo PHP_EOL;
echo 'Version data:' . PHP_EOL;
echo '- Main:' . PHP_TAB . PHP_TAB . PHP_TAB . $version['main'] . PHP_EOL;
echo '- Release:' . PHP_TAB . PHP_TAB . $version['release'] . PHP_EOL;
echo '- Full:' . PHP_TAB . PHP_TAB . PHP_TAB . $version['main'] . '.' . $version['dev_devel'] . PHP_EOL;
echo '- Build:' . PHP_TAB . PHP_TAB . $version['build'] . PHP_EOL;
echo '- Dev Level:' . PHP_TAB . PHP_TAB . $version['dev_devel'] . PHP_EOL;
echo '- Dev Status:' . PHP_TAB . PHP_TAB . $version['dev_status'] . PHP_EOL;
echo '- Release date:' . PHP_TAB . PHP_TAB . $version['reldate'] . PHP_EOL;
echo '- Release time:' . PHP_TAB . PHP_TAB . $version['reltime'] . PHP_EOL;
echo '- Release timezone:' . PHP_TAB . $version['reltz'] . PHP_EOL;
echo '- Creation date:' . PHP_TAB . $version['credate'] . PHP_EOL;

if (!empty($version['codename']))
{
	echo '- Codename:' . PHP_TAB . PHP_TAB . $version['codename'] . PHP_EOL;
}

echo PHP_EOL;

$rootPath = dirname(__DIR__);

// Updates the version in version class.
if (file_exists($rootPath . $versionFile))
{
	$fileContents = file_get_contents($rootPath . $versionFile);
	$fileContents = preg_replace("#MAJOR_VERSION\s*=\s*[^;]*#", "MAJOR_VERSION = " . $version['major'], $fileContents);
	$fileContents = preg_replace("#MINOR_VERSION\s*=\s*[^;]*#", "MINOR_VERSION = " . $version['minor'], $fileContents);
	$fileContents = preg_replace("#PATCH_VERSION\s*=\s*[^;]*#", "PATCH_VERSION = " . $version['patch'], $fileContents);
	$fileContents = preg_replace("#EXTRA_VERSION\s*=\s*'[^\']*'#", "EXTRA_VERSION = '" . $version['extra'] . "'", $fileContents);
	$fileContents = preg_replace("#RELEASE\s*=\s*'[^\']*'#", "RELEASE = '" . $version['main'] . "'", $fileContents);
	$fileContents = preg_replace("#DEV_LEVEL\s*=\s*'[^\']*'#", "DEV_LEVEL = '" . $version['dev_devel'] . "'", $fileContents);
	$fileContents = preg_replace("#DEV_STATUS\s*=\s*'[^\']*'#", "DEV_STATUS = '" . $version['dev_status'] . "'", $fileContents);
	$fileContents = preg_replace("#BUILD\s*=\s*'[^\']*'#", "BUILD = '" . $version['build'] . "'", $fileContents);
	$fileContents = preg_replace("#RELDATE\s*=\s*'[^\']*'#", "RELDATE = '" . $version['reldate'] . "'", $fileContents);
	$fileContents = preg_replace("#RELTIME\s*=\s*'[^\']*'#", "RELTIME = '" . $version['reltime'] . "'", $fileContents);
	$fileContents = preg_replace("#RELTZ\s*=\s*'[^\']*'#", "RELTZ = '" . $version['reltz'] . "'", $fileContents);

	if (!empty($version['codename']))
	{
		$fileContents = preg_replace("#CODENAME\s*=\s*'[^\']*'#", "CODENAME = '" . $version['codename'] . "'", $fileContents);
	}

	file_put_contents($rootPath . $versionFile, $fileContents);
}

// + TJ - chanages
// Prints TJ specific information.
$author      = 'Techjoomla';
$authorEmail = 'extensions@techjoomla.com';
$authorUrl   = 'https://techjoomla.com';
$license     = 'http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL';
$copyright   = 'Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.';

echo PHP_EOL;

echo 'Techjoomla copyright info to be added in xml:' . PHP_EOL;
echo '- author:' . PHP_TAB . PHP_TAB . PHP_TAB . $author . PHP_EOL;
echo '- authorEmail:' . PHP_TAB . PHP_TAB . PHP_TAB . $authorEmail . PHP_EOL;
echo '- authorUrl:' . PHP_TAB . PHP_TAB . PHP_TAB . $authorUrl . PHP_EOL;
echo '- license:' . PHP_TAB . PHP_TAB . PHP_TAB . $license . PHP_EOL;
echo '- copyright:' . PHP_TAB . PHP_TAB . PHP_TAB . $copyright . PHP_EOL;

echo PHP_EOL;

// + TJ - chanages - end

// Updates the version and creation date in core xml files.
foreach ($coreXmlFiles as $coreXmlFile)
{
	if (file_exists($rootPath . $coreXmlFile))
	{
		// @echo 'Processed xml file: ' .$rootPath . $coreXmlFile . PHP_EOL;

		$fileContents = file_get_contents($rootPath . $coreXmlFile);

		$fileContents = preg_replace('#<version>[^<]*</version>#', '<version>' . $version['main'] .
		'.' . $version['dev_devel'] . '</version>', $fileContents
		);

		$fileContents = preg_replace('#<creationDate>[^<]*</creationDate>#', '<creationDate>' .
		$version['credate'] . '</creationDate>', $fileContents
		);

		// + TJ - chanages
		$fileContents = preg_replace('#<author>[^<]*</author>#', '<author>' .
		$author . '</author>', $fileContents
		);

		$fileContents = preg_replace('#<authorEmail>[^<]*</authorEmail>#', '<authorEmail>' .
		$authorEmail . '</authorEmail>', $fileContents
		);

		$fileContents = preg_replace('#<authorUrl>[^<]*</authorUrl>#', '<authorUrl>' .
		$authorUrl . '</authorUrl>', $fileContents
		);

		$fileContents = preg_replace('#<license>[^<]*</license>#', '<license>' .
		$license . '</license>', $fileContents
		);

		$fileContents = preg_replace('#<copyright>[^<]*</copyright>#', '<copyright>' .
		$copyright . '</copyright>', $fileContents
		);

		// + TJ - chanages - end

		file_put_contents($rootPath . $coreXmlFile, $fileContents);
	}
}

// Updates the version for the `phpdoc` task in the Ant job file.
if (file_exists($rootPath . $antJobFile))
{
	$fileContents = file_get_contents($rootPath . $antJobFile);
	$fileContents = preg_replace('#<arg value="Joomla! CMS [^ ]* API" />#', '<arg value="Joomla! CMS ' .
		$version['main'] . ' API" />', $fileContents
		);
	file_put_contents($rootPath . $antJobFile, $fileContents);
}

// Updates the version in readme files.
foreach ($readMeFiles as $readMeFile)
{
	if (file_exists($rootPath . $readMeFile))
	{
		$fileContents = file_get_contents($rootPath . $readMeFile);
		$fileContents = preg_replace('#Joomla! [0-9]+\.[0-9]+ (|\[)version#', 'Joomla! ' . $version['main'] . ' $1version', $fileContents);
		$fileContents = preg_replace('#Joomla_[0-9]+\.[0-9]+_version#', 'Joomla_' . $version['main'] . '_version', $fileContents);
		file_put_contents($rootPath . $readMeFile, $fileContents);
	}
}

// Updates the copyright date in core files.
$changedFilesCopyrightDate = 0;
$changedFilesSinceVersion  = 0;
$year                      = date('Y');
$directory                 = new \RecursiveDirectoryIterator($rootPath);
$iterator                  = new \RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);

foreach ($iterator as $file)
{
	if ($file->isFile())
	{
		$filePath     = $file->getPathname();
		$relativePath = str_replace($rootPath, '', $filePath);

		// Exclude certain extensions.
		if (preg_match('#\.(png|jpeg|jpg|gif|bmp|ico|webp|svg|woff|woff2|ttf|eot)$#', $filePath))
		{
			continue;
		}

		// Exclude certain files.
		if (in_array($relativePath, $directoryLoopExcludeFiles))
		{
			continue;
		}

		// Exclude certain directories.
		$continue = true;

		foreach ($directoryLoopExcludeDirectories as $excludeDirectory)
		{
			if (preg_match('#^' . preg_quote($excludeDirectory) . '#', $relativePath))
			{
				$continue = false;
				break;
			}
		}

		if ($continue)
		{
			$changeSinceVersion  = false;
			$changeCopyrightDate = false;

			// Load the file.
			$fileContents = file_get_contents($filePath);

			// Check if need to change the copyright date.
			if (preg_match('#2016\s+-\s+[0-9]{4}\s+Techjoomla.\s+All\s+rights#', $fileContents)
				&& !preg_match('#2016\s+-\s+' . $year . '\s+Techjoomla.\s+All\s+rights#', $fileContents))
			{
				$changeCopyrightDate = true;
				$fileContents = preg_replace('#2016\s+-\s+[0-9]{4}\s+Techjoomla.\s+All\s+rights#', '2016 - ' .
				$year . ' Techjoomla. All rights', $fileContents
				);
				$changedFilesCopyrightDate++;
			}

			// Check if need to change the since version.
			if ($relativePath !== '/build/bump.php' && preg_match('#__DEPLOY_VERSION__#', $fileContents))
			{
				$changeSinceVersion = true;
				$fileContents = preg_replace('#__DEPLOY_VERSION__#', $version['release'], $fileContents);
				$changedFilesSinceVersion++;
			}

			// Save the file.
			if ($changeCopyrightDate || $changeSinceVersion)
			{
				file_put_contents($filePath, $fileContents);
			}

			// @echo 'Processed file: ' . $filePath . PHP_EOL;
		}
	}
}

if ($changedFilesCopyrightDate > 0 || $changedFilesSinceVersion > 0)
{
	if ($changedFilesCopyrightDate > 0)
	{
		echo '- Copyright Date changed in ' . $changedFilesCopyrightDate . ' files.' . PHP_EOL;
	}

	if ($changedFilesSinceVersion > 0)
	{
		echo '- Since Version changed in ' . $changedFilesSinceVersion . ' files.' . PHP_EOL;
	}

	echo PHP_EOL;
}

echo 'Version bump complete!' . PHP_EOL;
