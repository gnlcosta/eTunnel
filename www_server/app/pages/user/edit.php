<div class="container">
<div class="row">
  <div class="col-md-8  col-md-offset-2 col-xs-12">
    <div class="jumbotron">
        <form method="post" action="<?php echo EsNewUrl('user', 'edit'); ?>">
        <div class="row">
        <div class="col-md-6 col-xs-12">
        <h2>Utente:</h2>
        <div class="form-group has-success has-feedback">
            <input type="text" class="form-control input-lg" readonly="" name="user" value="<?php echo $user['user'];?>"></input>
        </div>
        </div>
        <div class="col-md-6 col-xs-12">
        <h2>Email:</h2>
        <div class="form-group has-success has-feedback">
            <input type="text" class="form-control input-lg kbtouch" name="email" placeholder="Email" value="<?php echo $user['email'];?>"></input>
        </div>
        </div>
        </div>
        <div class="row">
        <div class="col-xs-12">
            <input type="submit" class="btn btn-default btn-block btn-lg" value="Conferma Modifiche"></input>
            <a href="<?php echo EsNewUrl('main', 'list'); ?>" class="btn btn-block btn-info btn-lg">Annulla</a>
        </div>
        </div>
        <div class="fclear"></div>
        </form>
    </div>
  </div>
</div>
</div> <!-- /container -->
