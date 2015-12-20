<div class="row pane">
  <div class="col-xs-12">
    <h1 class="page-header">Email</h1>
<?php
$checkBtn = array('s'=>B_T_INFO, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'checkNames(this)'), 'ic'=>'user');
$sendBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'email\', this, \'send\', \'updateEditor\')'), 'ic'=>'send');
$form = $page->getPlugin('form', array('email', WebApp::action('email', 'send', true), 'post'));
$form
	->setIndent('    ')
	->setColumns(2, 8, 2)
	->addSelect2(
		'To',
		'to',
		'',
		array('t'=>'Contacts to send email to. * denotes group'),
		array(
			'r'=>true
		)
	)
	->addTextField(
		'Subject',
		'subject',
		'',
		array('t'=>'The subject of the email', 'p'=>'Subject'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A subject is required.', 's'=>B_T_FAIL)
			),
			'vo'=>'validateOn:["blur"]',
			'r'=>true
		)
	)
	->addTextArea(
		'Message',
		'message',
		'<p>'.$page->parent->user->getFullName().'</p>',
		10,
		array('t'=>'The email message'),
		array('ck'=>true, 'ckt'=>'Basic')
	)
	->addBtnLine(array('Check Addresses'=>$checkBtn, 'Send Email'=>$sendBtn));
$form->build();
print $form->getForm();
?>
  </div>
</div>
<script type="text/javascript">
$('[name="to"]').select2({
	multiple: true,
	placeholder: "Search for contacts",
	minimumInputLength: 1,
	ajax: {
		url: "/ajax/email/contacts",
		dataType: 'json',
		data: function(term, page){
			return {
				q: term
			}
		},
		results: function (data, page){
			return {results: data.data.contacts}
		}
	}
});
function updateEditor(){
	for ( instance in CKEDITOR.instances ) {
		CKEDITOR.instances[instance].updateElement();
	}
	return true;
}
function checkNames(btn){
	if(typeof btn !== "undefined"){
		jbtn = $(btn);
		jbtn.attr('data-loading-text', 'Checking...').button('loading');
	}
	$.post('/action/email/checknames', $('#email').serialize() , function(data){
		$(".alert").fadeOut(500).parent().remove();
		$($.parseHTML(data.msg)).css("display", "none").insertAfter("#status_bar").fadeIn();
		$(".alert").each(function(i,e){
			clearStatusMessage(e.id);
		});
		if(data.form != null){
			$.each(data.form, function(key, value) {
				$('[name="'+key+'"').val(value);
			});
		}
		
	},'json')
	.always(function(){
		if(typeof btn !== "undefined") jbtn.button('reset');
	});
}
</script>