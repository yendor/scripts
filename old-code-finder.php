#!/usr/bin/env php
<?php

/**
* Search a given path for files that are potentially old, backup
* temporary or otherwise disposable
*
* @author Rodney Amato <rodnet@gmail.com>
*/

$patterns = array(
	'#\Wold$#i',
	'#[\-\.]orig$#',
	'#~$#',
	'#^.DS_Store$#',
	'#\.php\-?\d+$#',
	'#CVS$#',
	'#.svn$#',
	'#\Wold\W#i',
	'#\.php-bu$#i',
	'#\.save$#i',
	'#\-simple$#i',
);

if (!isset($_SERVER['argv'][1])) {
	usage_and_die();
}

if (!is_dir($_SERVER['argv'][1])) {
	usage_and_die('ERROR: '.$_SERVER['argv'][1].' is not a valid starting path.');
}

traverseTree($_SERVER['argv'][1], $patterns);

/**
* Recursively traverse a tree looking for filenames that match a list of suspected old file patterns
*
* @var $path The path to traverse from
* @var $patterns The array of pcre patterns to match against the filenames
*
* @return void
*/
function traverseTree($path, $patterns)
{
	if (!is_dir($path)) {
		return;
	}

	$dir = new DirectoryIterator($path);

	foreach ($dir as $file) {
		$fullPath = $path.'/'.$file;

		if ($file == '.' || $file == '..') {
			continue;
		}

		foreach ($patterns as $pattern) {
			if (preg_match($pattern, $file)) {
				echo $fullPath."\n";
				break;
			}
		}

		if (is_dir($fullPath) && !is_link($fullPath)) {
			traverseTree($fullPath, $patterns);
		}
	}
}

/**
* Display the usage (with an optional message) and then end the script
*
* @var $message String the optional message
*
* @return void
*/
function usage_and_die($message=false)
{
	if ($message) {
		echo $message."\n";
	}

	echo 'Usage: '.$_SERVER['argv'][0].' /starting/path'."\n";

	exit(1);
}

