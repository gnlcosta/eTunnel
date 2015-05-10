<div class="container">
        <?php foreach ($nodes as $node): ?>
		<div class="node" title="<?php echo $node['descrip']; ?>">
			<div id="ip_<?php echo $node['id']; ?>" class="nlabel">---</div>
			<div class="ncont display">
                <div class="displ-count"> <?php echo $node['name']; ?></div>
                <div id="on_<?php echo $node['id']; ?>" class="dispoff"> 
                    <button data-id="<?php echo $node['id']; ?>" class="start btn btn-block btn-info btn-xs">Start</button>
                    <button data-id="<?php echo $node['id']; ?>" class="stop btn btn-block btn-warning btn-xs">Stop</button>
                    <?php if ($node['sms_updown']): ?>
                    <button data-id="<?php echo $node['id']; ?>" class="sms_start btn btn-block btn-warning btn-xs">SMS-Stop</button>
                    <?php endif;?>
                </div>
                <div id="off_<?php echo $node['id']; ?>" class="dispoff">
                <?php if ($node['sms_updown']): ?>
                    <button data-id="<?php echo $node['id']; ?>" class="sms_start btn btn-block btn-warning btn-xs">SMS-Start</button>
                <?php endif;?>
                </div>
                <div id="lmsg_<?php echo $node['id']; ?>" class="displ-title"></div>
				<span id="ball_a_<?php echo $node['id']; ?>" class="ball_a nlost"> </span>
				<span id="ball_b_<?php echo $node['id']; ?>" class="ball_b ndisab"> </span>
			</div>
            <div class="blabel" data-id="<?php echo $node['id']; ?>">
				<?php echo _('SN:').$node['sn'];?>
			</div>
		</div>
        <?php endforeach; ?>
</div> <!-- /container -->


<script>
var instatus;

function VisualStatus(data) {
    var values;
    try {
        values = jQuery.parseJSON(data);
        if (values.e) {
            return;
        }
    } catch (e) {
        // error
        location.reload();
        return;
    }

    $.each(values, function( key, value ) {
        var id = value['id'];
        $('#ip_'+id).html(value['ip']);
        $('#lmsg_'+id).html(value['lmsg']);
        if (value['st']) {
            $('#ball_a_'+id).attr('class', 'ball_a');
            $('#off_'+id).hide(300);
            $('#on_'+id).show(300);
        }
        else {
            $('#ball_a_'+id).attr('class', 'ball_a nlost');
            $('#on_'+id).hide(300);
            $('#off_'+id).show(300);
        }
        switch (value['tunnel']) {
        case 0: // tunnel off
            $('#ball_b_'+id).attr('class', 'ball_b ndisab');
            break;
        case 1: // tunnel off
            $('#ball_b_'+id).attr('class', 'ball_b');
            break;
        case 2: // tunnel started o stoped
            $('#ball_b_'+id).attr('class', 'ball_b ntrans');
            break;
        }
    });
}

function NodesStatus() {
    $.ajax({
        url: "<?php echo EsNewUrl('main', 'nodes_list_update'); ?>",
        context: document.body
    }).done(VisualStatus)
    .always(function() {
        instatus = setTimeout(NodesStatus, 1000);
	});
}

function Action(url) {
    $.ajax({
        url: url,
        context: document.body
    }).done(AlertPrint)
    .always();
}


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
    
	$('.blabel').click(function() {
        document.location.href = '<?php echo EsNewUrl('main','tunnels');?>'+'?id='+$(this).data('id');
	});
    
	$('.start').click(function() {
        Action('<?php echo EsNewUrl('main','start');?>'+'?id='+$(this).data('id'));
	});
    
	$('.stop').click(function() {
        Action('<?php echo EsNewUrl('main','stop');?>'+'?id='+$(this).data('id'));
	});
    
	$('.sms_start').click(function() {
        Action('<?php echo EsNewUrl('main','sms_start');?>'+'?id='+$(this).data('id'));
	});
    
	$('.sms_stop').click(function() {
        Action('<?php echo EsNewUrl('main','sms_stop');?>'+'?id='+$(this).data('id'));
	});

    
	NodesStatus();
});
</script>
