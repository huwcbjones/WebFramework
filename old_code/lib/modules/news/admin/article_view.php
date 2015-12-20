<div class="row pane">
  <div class="col-xs-12">
    <div class="row">
<?php
// Add Article BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(1, true)){
	print('          <a class="btn btn-xs btn-block btn-success" href="article_add?r=v">New&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-plus-sign"></span></a>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Delete Article BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->inGroup(3, true)){
	print('          <button class="btn btn-xs btn-danger btn-block disabled article_need_check" onclick="delete_mod()">Delete&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-trash"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Edit Article BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(2, true)){
	print('          <button class="btn btn-xs btn-primary btn-block disabled" id="edit_btn">Edit&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-edit"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Publish Article BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(2, true)){
	print('          <button class="btn btn-xs btn-success btn-block disabled article_need_check" onclick="publish_mod()">Publish&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-eye-open"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Unpublish Article BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(2, true)){
	print('          <button class="btn btn-xs btn-danger btn-block disabled article_need_check" onclick="unpublish_mod()">Unpublish&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-eye-close"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);

// Preview Article BTN
print('        <div class="col-lg-2 col-sm-4 col-xs-3">'.PHP_EOL);
if($this->accessAdminPage(4, true)){
	print('          <button class="btn btn-xs btn-info btn-block disabled" id="preview_btn">Preview&nbsp;&nbsp;&nbsp;<span class="'.B_ICON.' '.B_ICON.'-new-window"></span></button>'.PHP_EOL);
}
print('        </div>'.PHP_EOL);
?>
    </div><br />
    <div class="row">
      <div class="col-xs-12">
<?php
$articles = $page->getPlugin('table', array('articles'));
$articles
	->setIndent('      ')
	->addClass('table-bordered')
	->addClass('table-hover')
	->addClass('table-striped')
	->sort(true)
	->sticky(true);
	
$thead = array();
if($this->accessAdminPage(3)||$this->accessAdminPage(4)){
	$thead['selectAll'] = Table::addTHeadCell('<input type="checkbox" id="selectAll" />', false);
}
$thead['id']		= Table::addTHeadCell('ID');
$thead['title']		= Table::addTHeadCell('Title');
$thead['pub']		= Table::addTHeadCell('<abbr title="Published">P</abbr>');
$thead['perms']		= Table::addTHeadCell('<abbr title="Permissions">Perm</abbr>');
$thead['user']		= Table::addTHeadCell('User');
$thead['group']		= Table::addTHeadCell('Group');
$thead['revs']		= Table::addTHeadCell('<abbr title="Revisions">R</abbr>');
$thead['hits']		= Table::addTHeadCell('<abbr title="Hits">H</abbr>');
if($this->accessAdminPage(2)){
	$thead['Edit']	= Table::addTHeadCell('Edit', false);
}
if($this->accessAdminPage(4)){
	$thead['Preview']	= Table::addTHeadCell('<abbr title="Preview">Prev</abbr>', false);
}

$articles->addHeader($thead);

$article_query = $this->mySQL_r->prepare(
"SELECT `news_articles`.`ID`, `title`, `aid`, `publish`, `rw`, `core_users`.`username`, `core_groups`.`name`, `revision`, `hits` FROM `news_articles`
LEFT JOIN `core_users`
ON `user`=`core_users`.`ID`
LEFT JOIN `core_groups`
ON `group`=`core_groups`.`GID`
ORDER BY `date_p` DESC");

if($article_query!==false){
	$article_query->execute();
	$article_query->store_result();
	$article_query->bind_result($id, $title, $aid, $publish, $perm, $user, $group, $revs, $hits);
	
	while($article_query->fetch()){
		$perms = (substr($perm, 0, 1)==1)?'r':'-';
		$perms.= (substr($perm, 1, 1)==1)?'w':'-';
		$perms.= ':';
		$perms.= (substr($perm, 2, 1)==1)?'r':'-';
		$perms.= (substr($perm, 3, 1)==1)?'w':'-';
		$perms.= ':';
		$perms.= (substr($perm, 4, 1)==1)?'r':'-';
		$perms.= (substr($perm, 5, 1)==1)?'w':'-';
		$perm = $perms; unset($perms);
		$row = array();
		if($this->accessAdminPage(3)||$this->accessAdminPage(4))	$row['check']	= Table::addCell('<input class="articles_check" type="checkbox" value="'.$id.'" name="article[]" />');
		$row['id']		= Table::addCell($id);
		$row['title']	= Table::addCell($title, 'i_'.$id);
		$row['pub']	= Table::addCell(
			Form::toggleLink($this, $publish, '', 3,
				array(
					's'=>array(
						'h'=>'Click to unpublish article.',
						'i'=>'eye-open',
						'u'=>'/action/news/articles_unpublish?a='.$id,
						'c'=>'processData(this.href);return false;'
					),
					'f'=>array(
						'h'=>'Click to publish article.',
						'i'=>'eye-close',
						'u'=>'/action/news/article_publish?a='.$id,
						'c'=>'processData(this.href);return false;'
					)
				)
			)
		);
		$row['perm']	= Table::addCell('<code>'.$perm.'</code>');
		$row['user']	= Table::addCell($user);
		$row['group']	= Table::addCell($group);
		$row['revs']	= Table::addCell($revs);
		$row['hits']	= Table::addCell($hits);
		if($this->accessAdminPage(2)){
		$row['Edit']	= Table::addCell('<a href="article_edit/'.$id.'"><span class="'.B_ICON.' '.B_ICON.'-edit"></span></a>');
		}
		if($this->accessAdminPage(4)){
		$row['preview']	= Table::addCell('<a href="article_preview/'.$aid.'" target="_blank" id="aid_'.$id.'"><span class="'.B_ICON.' '.B_ICON.'-new-window"></span></a>');
		}
		
		$articles->addRow($row);
	}
}

$articles->build();
print $articles->getTable();
?>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(function() {
	$("#selectAll").click(function(){
		$(".articles_check").prop('checked', this.checked);
		if(this.checked){
			$(".article_need_check").removeClass("disabled");
		}else{
			$("#edit_btn").addClass("disabled");
			$("#preview_btn").addClass("disabled");
			$(".article_need_check").addClass("disabled");
		}
	});
	$(".articles_check").change(function(){
		var check = ($('.articles_check').filter(":checked").length == $('.articles_check').length);
		$('#selectAll').prop("checked", check);
		if($('.articles_check').filter(":checked").length>0){
			if($('.articles_check').filter(":checked").length==1){
				$("#edit_btn").removeClass("disabled");
				$("#preview_btn").removeClass("disabled");
			}else{
				$("#edit_btn").addClass("disabled");
				$("#preview_btn").addClass("disabled");
			}
			$(".article_need_check").removeClass("disabled");
		}else{
			$("#edit_btn").addClass("disabled");
			$("#preview_btn").addClass("disabled");
			$(".article_need_check").addClass("disabled");
		}
	});
	$("#edit_btn").click(function(e){
		var articles = $('.articles_check').filter(":checked")
		if(articles.length==1){
			var article_id = articles.first().val();
			document.location.href = "article_edit/"+article_id;
		}else if(articles.length>1){
			alert("Please select one article only to edit");
		}
		return false;
	});
	$("#preview_btn").click(function(e){
		var articles = $('.articles_check').filter(":checked")
		if(articles.length==1){
			var article_id = articles.first().val();
			window.open($("#aid_"+article_id).attr('href'));
		}else if(articles.length>1){
			alert("Please select one article only to edit");
		}
		return false;
	});
})
        </script>