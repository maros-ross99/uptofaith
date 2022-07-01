<?php

function unescape($string)
{
	//return htmlspecialchars_decode($string, ENT_QUOTES, "UTF-8");
	return html_entity_decode($string, ENT_QUOTES, "UTF-8");
}

function escape($string)
{
	// dont escape already escaped string
	if ($string != unescape($string))
		return $string;

	//return htmlspecialchars($string, ENT_QUOTES, "UTF-8");
	return htmlentities($string, ENT_QUOTES, "UTF-8");
}

function encrypt($string)
{
	return sha1(escape($string));
}

?>
