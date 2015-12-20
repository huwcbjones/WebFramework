<?php

/**
 * XML Shortcuts
 *
 * Provides shortcuts to xml functions
 *
 * @category   WebApp.XML
 * @package    class.xmlcut.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 * 
 */

class XMLCut
{
	const name_space = 'WebApp.XML';
	const version = '1.0.0';

	public static function fetchTagValue($xml, $tag)
	{
		if (!is_a($xml, 'DOMNodeList')) {
			$tag = $xml->getElementsByTagName($tag);
			if ($tag->length == 1) {
				return $tag->item(0)->nodeValue;
			} elseif ($tag->length > 1) {
				$values = array();
				foreach($tag as $item) {
					$values[] = $item->nodeValue;
				}
				return $values;
			} else {
				return null;
			}
		} else {
			trigger_error('You must not pass a DOMNodeList Object to fetchTagValue',
				E_USER_WARNING);
		}
	}
}

?>