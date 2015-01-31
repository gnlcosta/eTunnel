<div class="container-fluid">
    <div class="row row-centered">
        <div class="col-xs-12 col-md-8 col-md-offset-2">
            <form class="form-horizontal" method="post" action="<?php echo EsNewUrl('main', 'node_add'); ?>">
                <div class="form-group">
                    <label for="sn" class="col-sm-4 control-label">Serial Number:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control kbtouch" id="sn" name="sn" placeholder="Numero di serie" value="<?php echo $sn;?>" readonly></input>
                    </div>
                </div>
                <div class="form-group">
                    <label for="name" class="col-sm-4 control-label">Nome:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control kbtouch" id="name" name="name" placeholder="Nome del nodo" value=""></input>
                    </div>
                </div>
                <div class="form-group">
                    <label for="descrip" class="col-sm-4 control-label">Descrizione (opzionale):</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control kbtouch" id="descrip" name="descrip" placeholder="Descrizione del Nodo (opzionale)" value=""></input>
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone" class="col-sm-4 control-label">Numero di telefono (opzionale):</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control kbtouch" id="phone" name="phone" placeholder="Numero di telefono del Nodo (opzionale)" value=""></input>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                    <input type="submit" class="btn btn-default btn-block btn-warning btn-lg" value="Conferma"></input>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> <!-- /container -->
