<div class="row pane" id="header">
  <div class="col-xs-12">
    <h1 class="page-header">Updating <?php print WebApp::get('cat4') ?> Module</h1>
    <div class="progress progress-striped active">
      <div id="prog_bar" class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
        <span class="sr-only">0% Complete</span>
      </div>
    </div>
    <p class="text-muted" id="status_txt">Connecting to updater...</p>
    <p class="text-muted" id="close_txt">Do not close this page until the update finishes.</p>
  </div>
</div>
<script type="text/javascript">
var module_id = "<?php print WebApp::get('cat5') ?>";
var process = 'update';
var status_txt = $("#status_txt");
var close_txt = $("#close_txt");
var prog_bar = $("#prog_bar");
$(document).ready(function(e) {
    processStep(1);
});

</script>