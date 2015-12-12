<?php
if(WebApp::get('cat4')===NULL||is_numeric(WebApp::get('cat4'))===false){
	$page->setStatus(404);
}
$ID = intval(WebApp::get('cat4'));

$article_query = $mySQL_r->prepare("SELECT `ID`, `publish`, `rw`, `title`, `aid`, `user`, `group`, `article`, `date_p`, `date_e`, `publish_f`, `publish_u`, `hits`, `revision` FROM `news_articles` WHERE `ID`=?");
$user_query = $mySQL_r->prepare("SELECT `ID`, `username` FROM `core_users`");
$group_query = $mySQL_r->prepare("SELECT `GID`,`name` FROM `core_groups` WHERE `type`='p' AND `GID`>0");

$article_query->bind_param('i',$ID);
$article_query->execute();
$article_query->store_result();
if($article_query->num_rows!=1){
	$page->setStatus(404);
}

$user_query->execute();
$user_query->store_result();
$user_query->bind_result($u_ID, $u_Name);
$user_data = array();
while($user_query->fetch()){
	$user_data[$u_ID] = $u_Name;
}
$user_query->free_result();

$group_query->execute();
$group_query->store_result();
$group_query->bind_result($g_ID, $g_Name);
$group_data = array();
while($group_query->fetch()){
	$group_data[$g_ID] = $g_Name;
}
$group_query->free_result();

$article_query->bind_result($ID,$publish,$perms,$title,$aid, $a_user, $a_group, $article, $date_p, $date_e, $publish_f, $publish_u, $hits, $revision);
$article_query->fetch();

$users = array();
foreach($user_data as $k=>$v){
	$users[$k]['v'] = $k;
	$users[$k]['n'] = $v;
	if($a_user==$k){
		$users[$k]['s'] = true;
	}else{
		$users[$k]['s'] = false;
	}
	$users[$k]['d'] = 0;
}

$groups = array();
foreach($group_data as $k=>$v){
	$groups[$k]['v'] = $k;
	$groups[$k]['n'] = $v;
	if($a_group==$k){
		$groups[$k]['s'] = true;
	}else{
		$groups[$k]['s'] = false;
	}
	$groups[$k]['d'] = 0;
}

if($publish_f===NULL){
	$publish_f = '';
}else{
	$publish_f = date(DATET_SQL, strtotime($publish_f));
}

if($publish_u===NULL){
	$publish_u = '';
}else{
	$publish_u = date(DATET_SQL, strtotime($publish_u));
}

$closeBtn = array('a'=>array('t'=>'url', 'a'=>'../article_view'), 'ic'=>'remove-sign');
$saveBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'article_edit\', this, \'save\')'), 'ic'=>'floppy-disk');
$applyBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'article_edit\', this, \'apply\')'), 'ic'=>'ok-sign');
$form = $page->getPlugin('form', array('article_edit', WebApp::action('article', 'user_edit', true), 'post'));

$form
	->setIndent('    ')
	->setColumns(2, 10)
	->addTextField(
		'ID',
		'id',
		$ID,
		array(
			't'=>'ID of Article',
		),
		array(
			'ro'=>true
		)
	)
	->addTextField(
		'Title',
		'title',
		$title,
		array(
			't'=>'Title of Article',
			'p'=>'Article Title'
		),
		array(
			'r'=>true, 'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'An Article Title is required.', 's'=>B_T_FAIL),
				'textfieldMinCharsMsg'=>array('m'=>'An Article Title is required.', 's'=>B_T_FAIL),
				'textfieldMaxCharsMsg'=>array('m'=>'Article title is limited to 250 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'minChars: 0, maxChars: 250, validateOn:["blur"]'
		)
	)
	->addTextField(
		'Article ID',
		'aid',
		$aid,
		array(
			't'=>'Article ID'
		),
		array(
			'ro'=>true
		)
	)
	->addButtonGroup(
		'Published',
		'publish',
		array(
			array(
				'i'=>'publishY',
				's'=>B_T_SUCCESS,
				'v'=>1,
				'l'=>'Yes <span class="'.B_ICON.' '.B_ICON.'-eye-open"></span>',
				'c'=>$publish
			),
			array(
				'i'=>'publishN',
				's'=>B_T_FAIL,
				'v'=>0,
				'l'=>'No <span class="'.B_ICON.' '.B_ICON.'-eye-close"></span>',
				'c'=>not($publish),
			)
		),
		array('t'=>'An article has to be published (regardless of the permission settings) before it can be viewed')
	)
	->addDateTime(
		'Publish From',
		'p_from',
		$publish_f,
		array('t'=>'Date/time to start publishing, leave blank for always')
	)
	->addDateTime(
		'Publish To',
		'p_to',
		$publish_u,
		array('t'=>'Date/time to finish publishing, leave blank for always')
	)
	->addTextArea(
		'Article',
		'article',
		$article,
		20,
		array('t'=>'The article'),
		array('ck'=>true, 'ckt'=>'Full')
	)
	->addSelect(
		'User',
		'user',
		$users,
		array('t'=>'Select the owner of this article (this user gains the user permissions)')
	)
	->addSelect(
		'Group',
		'group',
		$groups,
		array('t'=>'Select the owner of this article (this group gains the group permissions)')
	)
	->addTextField(
		'Revision',
		'rev',
		$revision,
		array(
			't'=>'Number of times this article has been revised.',
		),
		array(
			'ro'=>true
		)
	)
	->addTextField(
		'Hits',
		'hits',
		$hits,
		array(
			't'=>'Number of times this article has been loaded.',
		),
		array(
			'ro'=>true
		)
	)
	
	->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn, 'apply'=>$applyBtn));
$form->build();
?>

<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">Edit Article</h1>
<?php print $form->getForm();?>
  </div>
</div>