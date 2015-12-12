<?php
$page->createTitle();
print $page->getHeader();
$result_query = $mySQL['r']->query("SELECT `title`,`resServ`,`resText`,`meet`,`series` from `comp_meet` WHERE `resServ`=1 ORDER BY `meet`,`date_f`");
if($result_query!==false&&$result_query->num_rows!=0){
	while($meet = $result_query->fetch_array()){
		print("<div class=\"row pane\">\n");
		print("  <div class=\"col-xs-11 col-offset-1\">\n");
		print("    <div class=\"row\">\n");
		print("      <div class=\"col-xs-12\">\n");
		print("        <h3>".$meet['title']."</h3>");
		print("      </div>\n");
		print("    </div>\n");
		print("    <div class=\"row\">\n");
		print("      <div class=\"col-xs-12\">\n");
		print("        <p>".$meet['resText']."</p>");
		print("      </div>\n");
		print("    </div>\n");
		print("    <div class=\"row\">\n");
		print("      <div class=\"col-xs-12\">\n");
		print("        <p>Find the results service <a href=\"http://results.biggleswadesc.org/?m=".$meet['meet']."&series=".$meet['series']."\">here</a></p>\n");
		print("      </div>\n");
		print("    </div>\n");
		print("  </div>\n");
		print("</div>\n");
	}
}else{
	print("<div class=\"row pane\">\n");
	print("  <div class=\"col-xs-12\">\n");
	print("    <h4>There are no competition results.</h4>\n");
	print("  </div>\n");
	print("</div>\n");
}
?>