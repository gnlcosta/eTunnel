<div class="container">
<div class="row">
  <div class="col-md-10  col-md-offset-1 col-xs-12">
    <div class="jumbotron">
        <form method="post" action="<?php echo EsNewUrl('user', 'change_password'); ?>">
        <div class="row">
        <div class="col-md-6 col-xs-12">
        <h2>Nuova Password:</h2>
        <div class="form-group has-success has-feedback">
            <input type="password" class="form-control input-lg kbtouch" name="password" placeholder="Nuova Password"></input>
        </div>
        </div>
        <div class="col-md-6 col-xs-12">
        <h2>Ripeti Password:</h2>
        <div class="form-group has-success has-feedback">
            <input type="password" class="form-control input-lg kbtouch" name="password_rep" placeholder="Nuova Password"></input>
        </div>
        </div>
        </div>
        <div class="row">
        <div class="col-xs-12">
            <input type="submit" class="btn btn-default btn-block btn-lg" value="Conferma"></input>
            <a href="<?php echo EsNewUrl('user', 'index'); ?>" class="btn btn-default btn-block btn-info btn-lg">Annulla</a>
        </div>
        </div>
        <div class="fclear"></div>
        </form>
    </div>
  </div>
</div>
</div> <!-- /container -->
