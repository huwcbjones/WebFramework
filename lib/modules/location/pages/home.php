<?php
if(WebApp::get('cat2')===NULL||is_numeric(WebApp::get('cat2'))===false){
	$page->setStatus(404);
	return false;
}
$location = $page->getResource('location');
if($location->getLocation(WebApp::get('cat2'))===false){
	$page->setStatus(404);
	return false;
}
$page->setTitle($location->name);
?>
  <div class="row pane">
    <div class="col-xs-12">
      <div class="row">
        <div class="col-xs-4">
          <div class="row">
            <address>
              <div class="col-xs-12"><h3>
                <strong><?php print($location->name); ?></strong><br />
                <?php print $location->address['line1'] ?>,<br />
                <?php if($location->address['line2']!='') print($location->address['line2'].',<br />'); ?>
                <?php print $location->address['city'] ?>,<br />
                <?php print $location->address['county'] ?>.<br />
                <?php print substr($location->address['postcode'],0,strlen($location->address['postcode'])-3).' '.substr($location->address['postcode'],-3) ?><br />
                <abbr title="Phone">P:</abbr> <?php print $location->phone['number']?><br />
                <?php if($location->phone['ext']!='') print('<abbr title="Extension">Ext:</abbr> '.$location->phone['ext'].'<br />'); ?>
              </h3></div>
            </address>
          </div>
        </div>
<?php
if($location->map){
	print('        <div class="col-xs-8" id="googleMap">'.PHP_EOL);
	print('          <div id="map-canvas" style="height:30em;width:100%"></div>'.PHP_EOL);
	print('        </div>'.PHP_EOL);
	print('        <script type="text/javascript">'.PHP_EOL);
	print('        var map;'.PHP_EOL);
	print('        function initialize() {'.PHP_EOL);
	print('          var address = "'.$location->address['line1'].', ');
	if($location->address['line2']!='') print($location->address['line2'].', ');
	print($location->address['city'].', '.$location->address['county'].'";'.PHP_EOL);
	print('          var geocoder = new google.maps.Geocoder();'.PHP_EOL);
	print('          geocoder.geocode( {"address": address}, function(results, status){'.PHP_EOL);
	print('            if(status==google.maps.GeocoderStatus.OK){'.PHP_EOL);
	print('              var geolocation = results[0].geometry.location'.PHP_EOL);
	print('              var marker = new google.maps.Marker({'.PHP_EOL);
	print('                title: "'.html_entity_decode($location->name).'",'.PHP_EOL);
	print('                map: map,'.PHP_EOL);
	print('                position: results[0].geometry.location'.PHP_EOL);
	print('              });'.PHP_EOL);
	print('              map.setCenter(marker.getPosition());'.PHP_EOL);
	print('            }'.PHP_EOL);
	print('          });'.PHP_EOL);
	print('          var mapOptions = {'.PHP_EOL);
	print('            zoom: 14,'.PHP_EOL);
	print('            center: new google.maps.LatLng(52,0),'.PHP_EOL);
	//print('            mapTypeId: google.maps.MapTypeId.HYBRID'.PHP_EOL);
	print('          };'.PHP_EOL);
	print('          map = new google.maps.Map(document.getElementById(\'map-canvas\'),'.PHP_EOL);
	print('              mapOptions);'.PHP_EOL);
	print('        }'.PHP_EOL);
	print('        google.maps.event.addDomListener(window, \'load\', initialize);'.PHP_EOL);
	print('        </script>'.PHP_EOL);
}
?>
      </div>
    </div>
  </div>

