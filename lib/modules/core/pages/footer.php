<?php
/**
 * Footer Page
 *
 * @category   WebApp.Page.Footer
 * @package    modules/core/pages/footer.php
 * @author     Huw Jones <huwcbjones@gmail.com>
 * @copyright  2014 Huw Jones
 */

/*
 */
?>
<div class="container">
  <footer>
    <div class="row pane text-center">
      <div class="col-xs-12 col-sm-2">
        <a href="http://www.swimming.org/asa/clubs-and-members/swimline/"> <img class="footer-logo" src="<?php print $page->getCDN()?>images/core/logo/asaswimline.jpg" alt="ASA Swimline" /> </a>
      </div>
      <div class="col-xs-12 col-sm-8">
        <div class="row">
          <div class="col-xs-3 hidden-sm hidden-xs">
            <a href="feed://<?php print $_SERVER['HTTP_HOST']?>/feeds/rss.xml"><img class="footer-logo" src="/images/core/icon/rss.png" alt="BWSC RSS Feed" /></a>
          </div>
          <div class="col-xs-8 col-md-6">
            <h4>&copy;&nbsp;Biggleswade Swimming Club <?php print date("Y"); ?></h4>
          </div>
          <div class="col-xs-4 col-md-3">
            <a href="//twitter.com/BiggleswadeSC"><img class="footer-logo social-logo" src="/images/core/icon/social_media/twitter.png" alt="BWSC Twitter" /></a>
            <a href="//facebook.com/BiggleswadeSC"><img class="footer-logo social-logo" src="/images/core/icon/social_media/facebook.png" alt="BWSC Facebook" /></a>
          </div>
        </div>
      </div>
      <div class="col-xs-12 col-sm-2">
        <img class="footer-logo" src="<?php print $page->getCDN()?>images/core/logo/swim21.png" alt="Swim21 Accredited" />
      </div>
    </div>
  </footer>
</div>
<script type="text/javascript" src="<?php print $page->getCDN()?>js/core/bootstrap_attr_tooltip.js"></script>
<script type="text/javascript" src="<?php print $page->getCDN()?>js/core/processdata.js"></script>
</body>
</html>