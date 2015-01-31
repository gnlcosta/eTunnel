<div class="container">
        <?php foreach ($nodes as $node): ?>
		<div class="node" title="<?php echo $node['descrip']; ?>">
			<div class="nlabel">
				<?php echo $node['ip'];?>
			</div>
			<div class="ncont display" data-id="<?php echo $node['id']; ?>">
                <div class="displ-count"> <?php echo $node['name']; ?></div>
                <div class="displ-title"> <?php echo date("Y-m-d", $node['lastmsg']); ?></div>
				<span class="ball_a <?php if ($node['lastmsg'] < time()-2*$node['freq']) echo 'nlost'; ?>"> </span>
				<span class="ball_b <?php if (!$node['tunnel'] && $node['start_type'] != -1) echo 'ntrans'; elseif (!$node['tunnel']) echo 'ndisab'; ?>"> </span>
			</div>
            <div class="blabel">
				<?php echo _('SN:').$node['sn'];?>
			</div>
		</div>
        <?php endforeach; ?>
</div> <!-- /container -->


<script>
$(function() {	
    $('.node[title]').qtip({
		position: {
			my: 'bottom center',
			at: 'top center'
		},
		style: {
			classes: 'qtip-shadow qtip-dark'
		},
		show: {
			delay: 100
		}
    });
    
	$('.ncont').click(function() {
        document.location.href = '<?php echo EsNewUrl('main','tunnels');?>'+'?id='+$(this).data('id');
	});
    
});
</script>
