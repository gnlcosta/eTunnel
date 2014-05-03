<div class="container">
    <div class="jumbotron">
    <div class="container">
        <h1>eTunnel-Client<small> SSH tunnel</small></h1>
        <?php if ($cfg): ?>
        <div class="col-sm-6">
        <h3>Vesione <small class="label label-danger"><?php echo $appl['version']; ?></small></h3>
        </div>
        <div class="col-sm-6">
        <h3>IDN <small class="label label-info"><?php echo $idn; ?></small></h3>
        </div>
        <?php else: ?>
        <div class="col-sm-6">
        <h3>Vesione <small class="label label-danger"><?php echo $appl['version']; ?> </small></h3>
        </div>
        <div class="col-sm-6">
        <h3>SN <small class="label label-primary"><?php echo $sn; ?> </small></h3>
        </div>
        <hr/>
        <div class="row row-centered">
            <form class="form-horizontal" method="post" action="<?php echo EsNewUrl('main', 'param_cfg'); ?>">
                <div class="form-group">
                    <label for="url" class="col-sm-3 control-label">URL del Server di Gestione:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control kbtouch" id="url" name="url" placeholder="Esempio: http://www.evolka.it/eTunnel" value="<?php echo $url;?>"></input>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                    <input type="submit" class="btn btn-block btn-info btn-lg" value="Salva"></input>
                    </div>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
    </div>
</div> <!-- /container -->
