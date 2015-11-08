<div class="container">
    <div class="col-xs-12 form-horizontal">
        <h3>Nodi utente: <strong><?php echo $user_info['user'];?></strong></h3>
        <table class="table table-striped">
            <tr>
                <td class="vert-align"><strong>Nodo</strong></td>
                <td class="vert-align"><strong>Abilitato</strong></td>
                <td></td>
            </tr>
            <?php foreach ($nodes as $node) : ?>
            <tr>
                <td class="vert-align"><?php echo $node['name']; ?></td>
                <td class="vert-align"><?php if ($node['enabled']) echo 'SI'; else echo 'NO'; ?></td>
                <td>
                    <?php if ($node['enabled']): ?>
                    <a href="<?php echo EsNewUrl('main', 'user_del_node', 'id='.$node['id']); ?>" class="btn btn-default"><span class="fa fa-exchange" aria-hidden="true"></span></a>
                    <?php else: ?>
                    <a href="<?php echo EsNewUrl('main', 'user_add_node', 'id='.$node['id']); ?>" class="btn btn-default"><span class="fa fa-exchange" aria-hidden="true"></span></a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div> <!-- /container -->
