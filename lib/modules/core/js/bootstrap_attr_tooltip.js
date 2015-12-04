/**
 * Bootstrap Attribute Tooltips
 *
 * Allows touch devices to tap on <attr></attr> elements
 * and get the attribute title in a toolip
 *
 * @site		www.biggleswadesc.org
 * @package		bootstrap_attr_tooltip.js
 * @author		Huw Jones <huwcbjones@gmail.com>
 * @copyright	Biggleswade Swimming Club 2013
 */

$().alert();
$(".glyph-tooltip").tooltip({container:'body'});
$('abbr').each(function() {
	$(this).tooltip();
});
$('.bstooltip').each(function() {
  $(this).tooltip();
});