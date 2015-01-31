<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-10 col-md-offset-1">
            <div class="table-responsive">
            <table class="table table-striped">
                <?php foreach($logs as $log) : ?>
                <tr>
                    <td class="vert-align">
                        <?php echo $log; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            </div>
        </div>
    </div>
    <?php if (count($logs) == 0): ?>
    <div class="row">
        <div class="col-xs-12 col-md-8 col-md-offset-2">
             <h2 class="text-center"><?php echo _('Nessun Log utente'); ?></h2>
        </div>
    </div>
    <?php endif; ?>
</div> <!-- /container -->
