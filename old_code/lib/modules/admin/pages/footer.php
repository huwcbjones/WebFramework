  </div>
<div class="row pane">
  <div class="col-xs-12">
    <footer>
      <div class="row">
        <div class="col-xs-3">
          <h4><?php print $page->parent->user->get_fullName(); ?></h4>
        </div>
        <div class="col-xs-6">
          <h4>&copy;&nbsp;Biggleswade Swimming Club <?php print date("Y"); ?></h4>
        </div>
        <div class="col-xs-3">
          <a href="//twitter.com/BiggleswadeSC"><img class="footer-logo social-logo" src="/images/core/icon/social_media/twitter.png" alt="BWSC Twitter" /></a>
          <a href="//facebook.com/BiggleswadeSC"><img class="footer-logo social-logo" src="/images/core/icon/social_media/facebook.png" alt="BWSC Facebook" /></a>
        </div>
      </div>
    </footer>
  </div>
</div>
</div>
<script type="text/javascript" src="<?php print $page->getCDN()?>js/core/bootstrap_attr_tooltip.js"></script>
<script type="text/javascript" src="<?php print $page->getCDN()?>js/core/processdata.js"></script>
</body>
</html>