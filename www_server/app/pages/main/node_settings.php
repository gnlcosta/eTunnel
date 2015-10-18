<div class="container-fluid">
    <div class="row row-centered">
        <div class="col-xs-12 col-md-8 col-md-offset-2">
            <form class="form-horizontal" method="post" action="<?php echo EsNewUrl('main', 'node_settings'); ?>">
                <div class="form-group">
                    <label for="name" class="col-sm-4 control-label">Nome:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control kbtouch" id="name" name="name" placeholder="Nome del nodo" value="<?php echo $node['name'];?>"></input>
                    </div>
                </div>
                <div class="form-group">
                    <label for="descrip" class="col-sm-4 control-label">Descrizione (opzionale):</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control kbtouch" id="descrip" name="descrip" placeholder="Descrizione del Nodo (opzionale)" value="<?php echo $node['descrip'];?>"></input>
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone" class="col-sm-4 control-label">Numero di telefono (opzionale):</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control kbtouch" id="phone" name="phone" placeholder="Numero di telefono del Nodo (opzionale)" value="<?php echo $node['phone'];?>"></input>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">SMS Start/Stop:</label>
                    <div class="col-sm-8">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default <?php if ($node['sms_updown']) echo "active"; ?>"><input type="radio" name="sms_updown" autocomplete="off" value="1" <?php if ($node['sms_updown']) echo "checked"; ?>></input> Si</label>
                            <label class="btn btn-default <?php if (!$node['sms_updown']) echo "active"; ?>"><input type="radio" name="sms_updown" autocomplete="off" value="0" <?php if (!$node['sms_updown']) echo "checked"; ?>></input> No</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Tunnel On in riconnessione:</label>
                    <div class="col-sm-8">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default <?php if ($node['auto_start']) echo "active"; ?>"><input type="radio" name="auto_start" autocomplete="off" value="1" <?php if ($node['auto_start']) echo "checked"; ?>></input> Si</label>
                            <label class="btn btn-default <?php if (!$node['auto_start']) echo "active"; ?>"><input type="radio" name="auto_start" autocomplete="off" value="0" <?php if (!$node['auto_start']) echo "checked"; ?>></input> No</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Disabilitato:</label>
                    <div class="col-sm-8">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default <?php if ($node['disable']) echo "active"; ?>"><input type="radio" name="disable" autocomplete="off" value="1" <?php if ($node['disable']) echo "checked"; ?>></input> Si</label>
                            <label class="btn btn-default <?php if (!$node['disable']) echo "active"; ?>"><input type="radio" name="disable" autocomplete="off" value="0" <?php if (!$node['disable']) echo "checked"; ?>></input> No</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                    <input type="submit" class="btn btn-default btn-block btn-success btn-lg" value="Conferma"></input>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <a href="<?php echo EsNewUrl('main', 'nodes_list'); ?>" class="btn btn-block btn-warning btn-lg">Annulla</a>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <a href="<?php echo EsNewUrl('main', 'node_remove', 'id='.$node['id']); ?>" class="btn btn-block btn-danger btn-xs">Rimuovi Nodo</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> <!-- /container -->
