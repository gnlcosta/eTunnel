<div class="container">
        <?php foreach ($nodes as $node): ?>
		<div class="node" title="<?php echo $node['descrip']; ?>">
			<div class="nlabel">
				<?php echo $node['name'];?>
			</div>
			<div class="ncont display" data-sn="<?php echo $node['sn']; ?>">
                <div class="displ-count"> <?php echo date("Y-m-d", $node['lastmsg']); ?></div>
                <div class="displ-title"> <?php echo date("H:i:s", $node['lastmsg']); ?></div>
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
        document.location.href = '<?php echo EsNewUrl('main','node_add');?>'+'?sn='+$(this).data('sn');
	});
    
    setTimeout(function(){ location.reload(); }, 30000);
});
</script>
