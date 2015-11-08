
<table class="table table-striped">
    <tr>
        <td class="vert-align"><strong>Nome</strong></td>
        <td class="vert-align"><strong>Porta d'accesso</strong></td>
    </tr>
    <?php foreach ($tunnels as $tunnel) : ?>
    <tr>
        <td class="vert-align"><a href="http://<?php echo $_SERVER['SERVER_NAME'].':'.$tunnel['sport'];?>"><?php echo $tunnel['name']; ?></a></td>
        <td class="vert-align"><?php echo $tunnel['sport']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
