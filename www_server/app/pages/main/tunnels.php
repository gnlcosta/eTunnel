<div class="container">
    <div class="col-xs-12 form-horizontal">
        <table class="table table-striped">
            <tr>
                <td class="vert-align"><strong>Nome</strong></td>
                <td class="vert-align"><strong>Porta sorgente</strong></td>
                <td class="vert-align"><strong>Host destinazione</strong></td>
                <td class="vert-align"><strong>Porta destinazione</strong></td>
                <td></td>
            </tr>
            <?php foreach ($tunnels as $tunnel) : ?>
            <tr>
                <td class="vert-align"><?php echo $tunnel['name']; ?></td>
                <td class="vert-align"><?php echo $tunnel['sport']; ?></td>
                <td class="vert-align"><?php echo $tunnel['dhost']; ?></td>
                <td class="vert-align"><?php echo $tunnel['dport']; ?></td>
                <td>
                    <a href="<?php echo EsNewUrl('main', 'tunnel_edit'); ?>" class="btn btn-default">
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                    </a>
                    <a href="<?php echo EsNewUrl('main', 'tunnel_remove', 'id='.$tunnel['id']); ?>" class="btn btn-danger">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <div class="form-group">
            <div class="col-sm-6">
                <a href="<?php echo EsNewUrl('main', 'start'); ?>" class="btn btn-block btn-info btn-lg">Avvia Tunnel</a>
            </div>
            <div class="col-sm-6">
                <a href="<?php echo EsNewUrl('main', 'stop'); ?>" class="btn btn-block btn-info btn-lg">Stop Tunnel</a>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <a href="<?php echo EsNewUrl('main', 'tunnel_add'); ?>" class="btn btn-block btn-info btn-lg">Aggiungi Tunnel</a>
            </div>
        </div>
    </div>
</div> <!-- /container -->
