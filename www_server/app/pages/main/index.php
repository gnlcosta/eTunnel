<div class="container">
  <div class="col-md-8  col-md-offset-2 col-xs-12">
    <div class="jumbotron">
    <div class="container">
        <h1>eTunnel <small><?php echo $appl['version']; ?></small></h1>
        <?php if ($cfg == FALSE) :?>
        <div class="alert alert-danger" role="alert">File di configurazione assente o incompleto</div>
        <?php else :?>
        <a href="<?php echo EsNewUrl('main', 'nodes_list'); ?>" class="btn btn-default btn-block btn-lg">Lista Nodi</a>
        <?php endif; ?>
    </div>
    </div>
  </div>
</div> <!-- /container -->
