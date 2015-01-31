<div class="container">
<div class="row">
  <div class="col-md-6  col-md-offset-3 col-xs-12">
    <div class="jumbotron ftop">
        <form method="post" action="<?php echo EsNewUrl('user', 'login'); ?>">
        <h2>Nome Utente:</h2>
        <div class="form-group has-success has-feedback">
            <input type="text" class="form-control input-lg kbtouch" name="user" placeholder="Nome Utente"></input>
        </div>
        <h2>Password:</h2>
        <div class="form-group has-success has-feedback">
            <input type="password" class="form-control input-lg kbtouch" name="password" placeholder="Password"></input>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-default btn-block btn-lg" value="Conferma"></input>
        </div>
        </form>
    </div>
  </div>
</div>
</div> <!-- /container -->
