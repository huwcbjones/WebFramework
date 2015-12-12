<?php
if(WebApp::get('cat4')===NULL||is_numeric(WebApp::get('cat4'))===false){
	$page->setStatus(404);
}
$ID = intval(WebApp::get('cat4'));

$location = $page->getResource('location');
if($location->getLocation($ID)===false){
	$page->setStatus(404);
	return false;
}

$closeBtn = array('a'=>array('t'=>'url', 'a'=>'../'), 'ic'=>'remove-sign');
$saveBtn = array('s'=>B_T_SUCCESS, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'location_edit\', this, \'save\')'), 'ic'=>'floppy-disk');
$applyBtn = array('s'=>B_T_PRIMARY, 'a'=>array('t'=>'url', 'a'=>'#', 'oc'=>'processForm(\'location_edit\', this, \'apply\')'), 'ic'=>'ok-sign');
$form = $page->getPlugin('form', array('location_edit', WebApp::action('location', 'edit', true), 'post'));

$form->setIndent('    ');
$form
	->addTextField(
		'Location ID',
		'id',
		$ID,
		array('t'=>'ID of Location.', 'p'=>'ID'),
		array(
			'ro'=>true,
			'd'=>false
		)
	)
	->addTextField(
		'Venue Name',
		'name',
		$location->name,
		array('t'=>'Name of venue', 'p'=>'Venue'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A Venue Name is required.', 's'=>B_T_FAIL),
				'textfieldMinCharsMsg'=>array('m'=>'A Venu Name is required.', 's'=>B_T_FAIL),
				'textfieldMaxCharsMsg'=>array('m'=>'Venu Name is limited to 100 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'minChars: 0, maxChars: 100, validateOn:["blur"]',
			'r'=>true
		)
	)
	->addTextField(
		'Address Line 1',
		'addr1',
		$location->address['line1'],
		array('t'=>'Address Line 1', 'p'=>'Address Line 1'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'Address Line 1 is required.', 's'=>B_T_FAIL),
				'textfieldMinCharsMsg'=>array('m'=>'Address Line 1 is required.', 's'=>B_T_FAIL),
				'textfieldMaxCharsMsg'=>array('m'=>'Address Line 1 is limited to 250 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'minChars: 0, maxChars: 250, validateOn:["blur"]',
			'r'=>true
		)
	)
	->addTextField(
		'Address Line 2',
		'addr2',
		$location->address['line2'],
		array('t'=>'Address Line 2', 'p'=>'Address Line 2'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldMaxCharsMsg'=>array('m'=>'Address Line 2 is limited to 250 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'minChars: 0, maxChars: 250, validateOn:["blur"]'
		)
	)
	->addTextField(
		'Town/City',
		'city',
		$location->address['city'],
		array('t'=>'City', 'p'=>'City'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A City is required.', 's'=>B_T_FAIL),
				'textfieldMinCharsMsg'=>array('m'=>'A City is required.', 's'=>B_T_FAIL),
				'textfieldMaxCharsMsg'=>array('m'=>'City is limited to 100 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'minChars: 0, maxChars: 100, validateOn:["blur"]',
			'r'=>true
		)
	)
	->addTextField(
		'County',
		'county',
		$location->address['county'],
		array('t'=>'County', 'p'=>'County'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A County is required.', 's'=>B_T_FAIL),
				'textfieldMinCharsMsg'=>array('m'=>'A County is required.', 's'=>B_T_FAIL),
				'textfieldMaxCharsMsg'=>array('m'=>'County is limited to 100 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'minChars: 0, maxChars: 100, validateOn:["blur"]',
			'r'=>true
		)
	)
	->addTextField(
		'Postcode',
		'post',
		$location->address['postcode'],
		array('t'=>'Postcode', 'p'=>'AB012CD'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A Postcode is required.', 's'=>B_T_FAIL),
				'textfieldMinCharsMsg'=>array('m'=>'A Postcode is required.', 's'=>B_T_FAIL),
				'textfieldMaxCharsMsg'=>array('m'=>'Postcode is limited to 7 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'minChars: 0, maxChars: 7, validateOn:["blur"]',
			'r'=>true
		)
	)
	->addTextField(
		'Phone Number',
		'phone',
		$location->phone['number'],
		array('t'=>'Phone', 'p'=>'01234 123455'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldRequiredMsg'=>array('m'=>'A phone number is required.', 's'=>B_T_FAIL),
				'textfieldMinCharsMsg'=>array('m'=>'A phone number is required.', 's'=>B_T_FAIL),
				'textfieldMaxCharsMsg'=>array('m'=>'Phone number is limited to a max of 15 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'minChars: 0, maxChars: 15, validateOn:["blur"]',
			't'=>'tel',
			'vt'=>'text',
			'r'=>true
		)
	)
	->addTextField(
		'Phone extension',
		'ext',
		$location->phone['ext'],
		array('t'=>'Extension for phone (if required)', 'p'=>'Extension Number'),
		array(
			'v'=>true,
			'vm'=>array(
				'textfieldMaxCharsMsg'=>array('m'=>'Extension is limited to 11 chars.', 's'=>B_T_FAIL)
			),
			'vo'=>'minChars: 0, maxChars: 50, validateOn:["blur"]'
		)
	)
	->addButtonGroup(
		'Google Maps',
		'maps',
		array(
			array(
				'i'=>'mapsY',
				's'=>B_T_SUCCESS,
				'v'=>1,
				'l'=>'Yes',
				'c'=>$location->map
			),
			array(
				'i'=>'mapsN',
				's'=>B_T_FAIL,
				'v'=>0,
				'l'=>'No',
				'c'=>!$location->map
			)
		),
		array('t'=>'Enable Google Maps integration on the location\'s page.')
	)
	->addBtnLine(array('close'=>$closeBtn, 'save'=>$saveBtn, 'apply'=>$applyBtn));
$form->build();
?>

<div class="row pane">
  <div class="col-xs-12">
  <h1 class="page-header">Edit Location</h1>
<?php print $form->getForm();?>
  </div>
</div>