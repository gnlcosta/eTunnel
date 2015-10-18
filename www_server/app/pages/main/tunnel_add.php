<div class="container-fluid">
    <div class="row row-centered">
        <div class="col-xs-12 col-md-8 col-md-offset-2">
            <form class="form-horizontal" method="post" action="<?php echo EsNewUrl('main', 'tunnel_add'); ?>">
                <div class="form-group">
                    <label for="name" class="col-sm-4 control-label">Nome:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control kbtouch" id="name" name="name" placeholder="Nome del servizio (es: http, ftp, FINS, PLC, ...)" value=""></input>
                    </div>
                </div>
                <div class="form-group">
                    <label for="sport" class="col-sm-4 control-label">Porta di accesso:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control kbtouch" id="sport" name="sport" placeholder="Porta di accesso" value=""></input>
                    </div>
                </div>
                <div class="form-group">
                    <label for="dhost" class="col-sm-4 control-label">IP del dispositivo remoto:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control kbtouch" id="dhost" name="dhost" placeholder="IP del dispositivo remoto" value=""></input>
                    </div>
                </div>
                <div class="form-group">
                    <label for="dport" class="col-sm-4 control-label">Porta del dispositivo remoto:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control kbtouch" id="dport" name="dport" placeholder="Port del dispositivo remoto" value=""></input>
                    </div>
                </div>
                <div class="form-group">
                    <label for="dport" class="col-sm-4 control-label">Livello accesso:</label>
                    <div class="col-sm-8">
                        <select name="utype" class="form-control input-lg">
                        <?php foreach ($levels as $key => $type) :?>
                          <option value="<?php echo $key;?>"><?php echo $type;?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                    <input type="submit" class="btn btn-default btn-block btn-success btn-lg" value="Conferma"></input>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                    <a href="<?php echo EsNewUrl('main', 'tunnels', 'id='.$node_id); ?>" class="btn btn-default btn-block btn-warning btn-lg">Annulla</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> <!-- /container -->
