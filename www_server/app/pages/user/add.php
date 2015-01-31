<div class="container">
<div class="row">
  <div class="col-md-8  col-md-offset-2 col-xs-12">
    <div class="jumbotron">
        <form method="post" action="<?php echo EsNewUrl('user', 'add'); ?>">
        <div class="row">
        <div class="col-md-6 col-xs-12">
        <h2>Nome Utente:</h2>
        <div class="form-group has-success has-feedback">
            <input type="text" class="form-control input-lg kbtouch" name="user" placeholder="Nome Utente"></input>
        </div>
        </div>
        <div class="col-md-6 col-xs-12">
        <h2>Password:</h2>
        <div class="form-group has-success has-feedback">
            <input type="password" class="form-control input-lg kbtouch" name="password" placeholder="Password"></input>
        </div>
        </div>
        </div>
        <div class="row">
        <div class="col-md-6 col-xs-12">
        <h2>Email:</h2>
        <div class="form-group has-success has-feedback">
            <input type="text" class="form-control input-lg kbtouch" name="email" placeholder="Email"></input>
        </div>
        </div>
        <div class="col-md-6 col-xs-12">
        <h2>Tipo:</h2>
        <div class="form-group has-success has-feedback">
            <select name="type" class="form-control input-lg">
            <?php foreach ($types as $key => $type) :?>
              <option value=<?php echo $key;?>><?php echo $type;?></option>
            <?php endforeach; ?>
            </select>
        </div>
        </div>
        </div>
        <div class="row">
        <div class="col-xs-12">
            <input type="submit" class="btn btn-default btn-block btn-lg" value="Conferma"></input>
        </div>
        </div>
        <div class="fclear"></div>
        </form>
    </div>
  </div>
</div>
</div> <!-- /container -->
