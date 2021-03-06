<?php

/**
 * HTML Tab Pane Builder Class
 *
 * Creates tabs
 *
 * @category   Plugins.Bootstrap.Tab
 * @package    tab.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
*/
class Tab extends BasePlugin
{
	const name_space = 'Plugins.Bootstrap.Tab';
	const version = '1.0.0';

	private $_tab = '';
	private $_pages = array();
	private $_nav = '';
	private $_tabs = '';

	private $_indent = '';
	private $_built = false;

	function __construct($parent)
	{
		$this->parent = $parent;
		$this->parent->parent->debug('***** ' . $this::name_space . ' *****');
		$this->parent->parent->debug($this::name_space . ': Version ' . $this::version);
	}

	function addPage($id, $title, $disabled=false, $content='')
	{
		$this->parent->parent->debug($this::name_space . ': Adding page ('.$id.')');
		$this->_pages[$id] = array(
			'title'=>$title,
			'disabled'=>$disabled,
			'content'=>$content
		);
		return $this;
	}
	
	function addPageContent($id, $content){
		$this->_pages[$id]['content'] = $content;
		return $this;
	}

	function build()
	{
		$this->parent->parent->debug($this::name_space . ': Building tab panes...');

		$this->_buildNav();
		
		$this->_buildTabs();
		$this->_tbody = '';

		$this->_genTableHead();

		if (count($this->_rows) != 0) {
			foreach($this->_rows as $row) {
				$this->_tbody .= $this->_genRow($row);
			}
		}

		$this->_genTableFoot();

		$this->_genScripts();

		$this->_table = $this->_thead . $this->_tbody . $this->_tfoot . $this->_script;

		if ($this->_responsive) {
			$this->_table = $this->_indent . '<div class="table-responsive">' . PHP_EOL . $this->
				_table . $this->_indent . '</div>' . PHP_EOL;
		}
		$this->_built = true;
	}

	/**
	 * Table::getTable()
	 * 
	 * @return
	 */
	function getTab()
	{
		if (!$this->_built) {
			trigger_error('Call Tab::build() before Tab::getTab()', E_USER_WARNING);
		}
		return $this->_tab;
	}

	/**
	 * Table::_genTableHead()
	 * 
	 * @return
	 */
	private function _genTableHead()
	{
		$this->parent->parent->debug($this::name_space . ': Generating table header...');
		$table = $this->_indent . '<table class="' . implode(' ', $this->_tableClasses) .
			'" id="' . $this->_tableID . '">' . PHP_EOL;
		if (count($this->_header) != 0) {
			$table .= $this->_indent . '  <thead>' . PHP_EOL;
			$table .= $this->_indent . '    <tr>' . PHP_EOL;
			foreach($this->_header as $col) {
				$table .= $this->_indent . '      <th';
				if (array_key_exists('class', $col))
					if ($col['class'] != '')
						$table .= ' class="' . $col['class'] . '"';
				if (array_key_exists('id', $col))
					if ($col['id'] != '')
						$table .= ' id="' . $col['id'] . '"';
				if ($col['nowrap'])
					$table .= ' nowrap="nowrap"';
				if ($col['width'] != '')
					$table .= ' width="' . $col['width'] . '"';
				$table .= '>' . $col['html'] . '</th>' . PHP_EOL;
			}
			$table .= $this->_indent . '    </tr>' . PHP_EOL;
			$table .= $this->_indent . '  </thead>' . PHP_EOL;
		}
		$this->_thead = $table;
	}
	/**
	 * Table::_genTableFoot()
	 * 
	 * @return
	 */
	private function _genTableFoot()
	{
		$table = '';
		$this->parent->parent->debug($this::name_space . ': Generating table footer...');
		if (count($this->_footer) != 0 || $this->_page) {
			$table .= $this->_indent . '  <tfoot>' . PHP_EOL;
			$table .= $this->_tpage;
			if (count($this->_footer) != 0) {
				$table .= $this->_indent . '    <tr>' . PHP_EOL;
				foreach($this->_footer as $col) {
					$table .= $this->_indent . '      <th';
					if (array_key_exists('class', $col))
						if ($col['class'] != '')
							$table .= ' class="' . $col['class'] . '"';
					if (array_key_exists('id', $col))
						if ($col['id'] != '')
							$table .= ' id="' . $col['id'] . '"';
					$table .= '>' . $col['html'] . '</th>' . PHP_EOL;
				}
				$table .= $this->_indent . '    </tr>' . PHP_EOL;
			}
			$table .= $this->_indent . '  </tfoot>' . PHP_EOL;
		}
		$table .= $this->_indent . '</table>' . PHP_EOL;
		$this->_tfoot = $table;
	}
	/**
	 * Table::_genTablePager()
	 * 
	 * @return
	 */
	private function _genTablePager()
	{
		$table = '';
		if (count($this->_pager) != 0) {
			$table .= $this->_indent .
				'    <tr class="ts-pager form-horizontal"><td colspan="' . count($this->_header) .
				'"><table class="table-condensed">' . PHP_EOL;
			foreach($this->_pager as $col) {
				$table .= $this->_indent . '      <th';
				if (array_key_exists('class', $col))
					if ($col['class'] != '')
						$table .= ' class="' . $col['class'] . '"';
				if (array_key_exists('id', $col))
					if ($col['id'] != '')
						$table .= ' id="' . $col['id'] . '"';
				$table .= '>' . $col['html'] . '</th>' . PHP_EOL;
			}
			$table .= $this->_indent . '    </table></td></tr>' . PHP_EOL;
		}
		$this->_tpage = $table;
	}
	/**
	 * Table::_genRow()
	 * 
	 * @param mixed $row
	 * @return
	 */
	private function _genRow($row)
	{
		$this->parent->parent->debug($this::name_space . ': Generating row...');
		$table = '';
		if (is_array($row)) {
			if (count($row) != 0) {
				$table .= $this->_indent . '    <tr>' . PHP_EOL;
				foreach($row as $col) {
					$table .= $this->_indent . '      <td';
					if (array_key_exists('class', $col))
						if ($col['class'] != '')
							$table .= ' class="' . $col['class'] . '"';
					if (array_key_exists('id', $col))
						if ($col['id'] != '')
							$table .= ' id="' . $col['id'] . '"';
					if ($col['nowrap'])
						$table .= ' nowrap="nowrap"';
					$table .= '>' . $col['html'] . '</td>' . PHP_EOL;
				}
				$table .= $this->_indent . '    </tr>' . PHP_EOL;
			}
		} else {
			$table = $row;
		}
		return $table;
	}

	/**
	 * Table::_genPageArray()
	 * 
	 * @return
	 */
	private function _genPageArray()
	{
		$select = '<select class="ts-pagesize form-control" title="Page Size">';
		$select .= '<option selected="selected" value="10">10</option>';
		$select .= '<option value="20">20</option>';
		$select .= '<option value="30">30</option>';
		$select .= '<option value="40">40</option>';
		$select .= '<option value="50">50</option>';
		$select .= '<option value="100">100</option>';
		$select .= '<option value="150">150</option>';
		$select .= '</select>';

		$pager['first'] = self::addCell('<button class="btn btn-default ts-first"><span class="' .
			B_ICON . ' ' . B_ICON . '-step-backward"></span></button>');
		$pager['prev'] = self::addCell('<button class="btn btn-default ts-prev"><span class="' .
			B_ICON . ' ' . B_ICON . '-backward"></span></button>');
		$pager['page'] = self::addCell('<span class="form-control-static pagedisplay"></span>',
			'');
		$pager['spage'] = self::addCell('<select class="form-control ts-pagenum" title="Select Page Number"></select>');
		$pager['next'] = self::addCell('<button class="btn btn-default ts-next"><span class="' .
			B_ICON . ' ' . B_ICON . '-forward"></span></button>');
		$pager['last'] = self::addCell('<button class="btn btn-default ts-last"><span class="' .
			B_ICON . ' ' . B_ICON . '-step-forward"></span></button>');
		$pager['view'] = self::addCell('<span class="form-control-static">Rows per Page:</span>');
		$pager['size'] = self::addCell($select);

		$this->_pager = $pager;
	}

	/**
	 * Table::_genScripts()
	 * 
	 * @return
	 */
	private function _genScripts()
	{
		$script = '';
		if (count($this->_scripts) != 0) {
			$script .= $this->_indent . '<script type="text/javascript">' . PHP_EOL;
			$script .= implode('', $this->_scripts);
			$script .= $this->_indent . '</script>' . PHP_EOL;
		}
		$this->_script = $script;
	}

	/**
	 * Table::_genSortScript()
	 * 
	 * @return
	 */
	private function _genSortScript()
	{
		$script = $this->_indent . '$.extend($.tablesorter.themes.bootstrap, {' .
			PHP_EOL;
		$script .= $this->_indent . '	table : "' . implode(' ', $this->_tableClasses) .
			'"' . PHP_EOL;
		$script .= $this->_indent . '});' . PHP_EOL;
		$script .= $this->_indent . '$("#' . $this->_tableID . '").tablesorter({' .
			PHP_EOL;
		$script .= $this->_indent . '	theme : "bootstrap",' . PHP_EOL;
		$script .= $this->_indent . '	widthFixed: false,' . PHP_EOL;
		$script .= $this->_indent . '	headerTemplate : "{content} {icon}",' . PHP_EOL;
		$script .= $this->_indent . '	widgets : [ ';
		$script .= '"uitheme"';
		if ($this->_sticky)
			$script .= ', "stickyHeaders"';
		$script .= ' ],' . PHP_EOL;
		if ($this->_sticky) {
			$script .= $this->_indent . '	widgetOptions: {' . PHP_EOL;
			$script .= $this->_indent . '	  stickyHeaders: "",' . PHP_EOL;
			$script .= $this->_indent . '	  stickyHeaders_offset: 50,' . PHP_EOL;
			$script .= $this->_indent . '	  stickyHeaders_cloneID: "-sticky",' . PHP_EOL;
			$script .= $this->_indent . '	  stickyHeaders_addResizeEvent: true,' . PHP_EOL;
			$script .= $this->_indent . '	  stickyHeaders_includeCpation: false,' . PHP_EOL;
			$script .= $this->_indent . '	  stickyHeaders_zIndex: 2,' . PHP_EOL;
			$script .= $this->_indent . '	  stickyHeaders_attachTo: null,' . PHP_EOL;
			$script .= $this->_indent . '	  uitheme: "bootstrap"' . PHP_EOL;
			$script .= $this->_indent . '	},' . PHP_EOL;
		}
		$script .= $this->_indent . '	headers: {' . PHP_EOL;

		$c = 0;
		foreach($this->_header as $col) {
			if (!$col['sort']) {
				$script .= $this->_indent . '		' . $c . ': { sorter: false },' . PHP_EOL;
			}
			$c++;
		}

		$script .= $this->_indent . '	}' . PHP_EOL;
		$script .= $this->_indent . '})' . PHP_EOL;
		if ($this->_page) {
			$script .= $this->_indent . '.tablesorterPager({' . PHP_EOL;
			$script .= $this->_indent . '	container : $(".ts-pager"),' . PHP_EOL;
			$script .= $this->_indent . '	cssPageSize: ".ts-pagesize",' . PHP_EOL;
			$script .= $this->_indent . '	cssGoto: ".ts-pagenum",' . PHP_EOL;
			$script .= $this->_indent . '	cssFirst: ".ts-first",' . PHP_EOL;
			$script .= $this->_indent . '	cssLast: ".ts-last",' . PHP_EOL;
			$script .= $this->_indent . '	cssNext: ".ts-next",' . PHP_EOL;
			$script .= $this->_indent . '	cssPrev: ".ts-prev",' . PHP_EOL;
			$script .= $this->_indent . '	fixedHeight: true,' . PHP_EOL;
			$script .= $this->_indent . '	removeRows : false,' . PHP_EOL;
			$script .= $this->_indent . '	widgets : [ "uitheme" ],' . PHP_EOL;
			$script .= $this->_indent . '	output: "Page {page} of {totalPages}"' . PHP_EOL;
			$script .= $this->_indent . '})' . PHP_EOL;
		}
		$this->_scripts['sort'] = $script;
	}

	/**
	 * Table::addTHeadCell()
	 * 
	 * @param mixed $html
	 * @param bool $sort
	 * @param string $id
	 * @param string $class
	 * @param bool $noWrap
	 * @param string $width
	 * @return
	 */
	static function addTHeadCell($html, $sort = true, $id = '', $class = '', $noWrap = false,
		$width = '')
	{
		if ($noWrap == true && $width != '') {
			$noWrap = false;
		}

		$cell['html'] = $html;
		$cell['class'] = $class;
		$cell['id'] = $id;
		$cell['sort'] = $sort;
		$cell['nowrap'] = $noWrap;
		$cell['width'] = $width;
		return $cell;
	}

	/**
	 * Table::addCell()
	 * 
	 * @param mixed $html
	 * @param string $id
	 * @param string $class
	 * @param bool $noWrap
	 * @return
	 */
	static function addCell($html, $id = '', $class = '', $noWrap = false)
	{
		$cell['html'] = $html;
		$cell['class'] = $class;
		$cell['id'] = $id;
		$cell['nowrap'] = $noWrap;
		return $cell;
	}
}

?>