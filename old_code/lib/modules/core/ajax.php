<?php
/**
 * Core Ajax Class
 *
 * @category   Module.Core.Ajax
 * @package    core/ajax.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
*/

/*
 */

class AjaxController extends BaseAjax
{
	const	 name_space	 = 'Module.Core.Ajax';
	const	 version	 = '1.0.0';
	
	public function menu_pages(){
		$q = WebApp::get('q');
		$m = WebApp::get('m');
		if($q === NULL){
			return new ActionResult(
				$this,
				Server::get('HTTP_Referer'),
				0,
				'No search term sent',
				B_T_FAIL,
				array(
					'pages'=>array()
				)
			);
		}
		if($m === NULL || $m === ''){
			return new ActionResult(
				$this,
				Server::get('HTTP_Referer'),
				0,
				'No module selected',
				B_T_FAIL,
				array(
					'pages'=>array()
				)
			);
		}
		
		$pages = array();
		
		$q = '%'.$q.'%';
		
		$page_query = $this->mySQL_r->prepare("SELECT `ID`,`title` FROM `core_pages` WHERE `title` LIKE ? AND `module_id`=?");
		if(!$page_query){
			return new ActionResult(
				$this,
				Server::get('HTTP_Referer'),
				0,
				'Query failed',
				B_T_FAIL,
				array(
					'pages'=>array()
				)
			);
		}
		$page_query->bind_param('si', $q, $m);
		$page_query->execute();
		$page_query->store_result();
		$page_query->bind_result($id, $value);
		while($page_query->fetch()){
			$page['id'] = $id;
			
			$page['text'] = $value;
			if($id >= pow(10, 6)) $page['text'] = '* '.$page['text'];
			$pages[] = $page;
		}
		
		return new ActionResult(
			$this,
			'/admin/core/menu_add',
			0,
			'Success',
			B_T_SUCCESS,
			array(
				'pages'=>$pages
			)
		);
	}
}
?>
