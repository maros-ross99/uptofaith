<?php
// @copyright Jakub Vrána, http://php.vrana.cz/
function url($name)
{	 
	// replace special chars to -
	$url = preg_replace('~[^\\pL0-9_]+~u', '-', $name);
	$url = trim($url, "-");
	// convert to lower
	$url = mb_strtolower($url, "UTF-8");
	// convert diacritic chars to non-diacritic
	setlocale(LC_CTYPE, "cs_CS.utf-8");
	$url = iconv("UTF-8", "US-ASCII//TRANSLIT", $url);
	// remove special chars after transliteration
	$url = preg_replace('~[^-a-z0-9_]+~', '', $url);
	
	return $url;
}


function anchor_img($uri = '', $src = '', $title = '', $attributes = '')
{
	$title = (string) $title;

	if ( ! is_array($uri))
	{
		$site_url = ( ! preg_match('!^\w+://! i', $uri)) ? site_url($uri) : $uri;
	}
	else
	{
		$site_url = site_url($uri);
	}

	$src_url = site_url($src);

	if ($title == '')
	{
		$title = $site_url;
	}

	if ($attributes != '')
	{
		$attributes = _parse_attributes($attributes);
	}

	return '<a href="'.$site_url.'"'.$attributes.'><img src="'.$src_url.'" alt="'.$title.'" title="'.$title.'"></a>';
}

?>
