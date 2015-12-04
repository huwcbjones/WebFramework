<?php
/**
 * Tooltip HTML Class
 *
 * Creates HTML Tooltipa
 *
 * @category   Plugins.Bootstrap.ToolTip
 * @package    tooltip.php
 * @site       www.biggleswadesc.org
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */
class Tooltip{
	
	static function helpTooltip($help){
		print('<a class="glyph-tooltip" ');
		print('href="#" data-toggle="tooltip" ');
		print('data-trigger="click hover focus" data-placement="right" title="');
		print $help;	
		print('"><span class="'.B_ICON.' '.B_ICON.'-question-sign text-'.B_T_INFO.'"></span></a>');
	}
}
?>