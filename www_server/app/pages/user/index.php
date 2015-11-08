<div class="container-fluid">
    <?php if (count($users)) : ?>
    <div class="row">
        <div class="col-xs-12 col-md-8 col-md-offset-2">
            <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <th><strong>Utente</strong></th>
                    <th><strong>Tipo</strong></th>
                    <th><strong>Azioni</strong></th>
                </tr>
                <?php foreach($users as $user) : ?>
                <tr>
                    <td class="vert-align col-md-6"><strong><?php echo $user['user']; ?></strong></td>
                    <td class="vert-align col-md-3"><?php echo $types[$user['type']]; ?></td>
                    <td>
                        <?php if ($user['type'] > $user_tp || $user['id'] == $user_id || $user_id < 3): ?>
                        <a href="<?php echo EsNewUrl('user', 'change_password').'?id='.$user['id']; ?>" type="button" class="btn btn-default"><span class="fa fa-key" aria-hidden="true"></span></a>
                        <a href="<?php echo EsNewUrl('user', 'edit').'?id='.$user['id']; ?>" type="button" class="btn btn-default"><span class="fa fa-cog" aria-hidden="true"></span></a>
                        <?php if ($contr->users->Permanent($user['id']) == FALSE) : ?>
                        <a href="<?php echo EsNewUrl('main', 'user_delete').'?id='.$user['id']; ?>" type="button" class="btn btn-default"><span class="fa fa-trash" aria-hidden="true"></span></a>
                        <?php endif; ?>
                        <?php if ($user['type'] == 3): ?>
                        <a href="<?php echo EsNewUrl('main', 'user_nodes').'?id='.$user['id']; ?>" type="button" class="btn btn-default"><span class="fa fa-sitemap" aria-hidden="true"></span></a>
                        <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            </div>
            <!--
            <nav>
            <ul class="pager pagination-lg">
                <li class="previous"><a href="#"><span aria-hidden="true">&larr;</span><?php echo _('Prec.'); ?></a></li>
                <li class="next"><a href="#"><?php echo _('Suc.'); ?><span aria-hidden="true">&rarr;</span></a></li>
            </ul>
            </nav>
            -->
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6 col-md-4 col-md-offset-2">
             <a href="<?php echo EsNewUrl('user', 'add'); ?>" class="btn btn-default btn-block btn-lg">Nuovo Utente</a>
        </div>
        <div class="col-xs-6 col-md-4">
             <a href="<?php echo EsNewUrl('user', 'log'); ?>" class="btn btn-default btn-block btn-lg">Log Utenti</a>
        </div>
    </div>
    <p/>
    <div class="row">
        <div class="col-xs-12 col-md-8 col-md-offset-2">
             <a href="<?php echo EsNewUrl('user', 'login'); ?>" class="btn btn-default btn-block btn-lg">Cambia Utente</a>
        </div>
    </div>
    <?php else: ?>
    
    <div class="row">
        <div class="col-xs-12 col-md-8 col-md-offset-2">
             <a href="<?php echo EsNewUrl('user', 'login'); ?>" class="btn btn-default btn-block btn-lg">Cambia Utente</a>
        </div>
    </div>
    <?php endif; ?>
    
</div> <!-- /container -->
