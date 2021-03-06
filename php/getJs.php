<?php

$dir        = dirname( __DIR__ ).'/js';
$content    = '';
// Open a known directory, and proceed to read its contents

$values= array();

if (is_dir($dir))
{
    if ($dh = opendir($dir))
    {
        while (($file = readdir($dh)) !== false)
        {
			// &&
            if( endswith($file,'.js') && startsWith($file,'page') && !endswith($file,'.min.js'))
              $values[$file]=file_get_contents($dir.'/'.$file);
        }
        closedir($dh);
    }
}

ksort( $values );

foreach( $values as $key => $value )
{
  $content.=$value;
}
header("Content-type: application/javascript");
echo $content;

function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);

    return $length === 0 || (substr($haystack, -$length) === $needle);
}
