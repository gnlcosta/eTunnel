<div class="container">
        <?php foreach ($nodes as $node): ?>
		<div class="node" title="<?php echo $node['descrip']; ?>">
			<div id="ip_<?php echo $node['id']; ?>" class="nlabel">---</div>
			<div class="ncont display">
                <div class="displ-count"> <?php echo $node['name']; ?></div>
                <div id="on_<?php echo $node['id']; ?>" class="dispoff">
                    <button class="dispoff start btn btn-block btn-info btn-xs"></button>
                    <button id="boff_<?php echo $node['id']; ?>" data-id="<?php echo $node['id']; ?>" class="dispoff start btn btn-block btn-info btn-xs">Start</button>
                    <button id="bon_<?php echo $node['id']; ?>" data-id="<?php echo $node['id']; ?>" class="dispoff stop btn btn-block btn-warning btn-xs">Stop</button>
                    <button id="con_<?php echo $node['id']; ?>" data-id="<?php echo $node['id']; ?>" class="dispoff tlink btn btn-block btn-primary btn-xs" data-toggle="popover" title="Tunnel attivi su <?php echo $_SERVER['SERVER_ADDR']; ?>" data-container="body" data-placement="bottom">Tunnel</button>
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
var tunnels = null;

function details_in_popup(link, div_id){
    $.ajax({
        url: link,
        success: function(response){
            $('#'+div_id).html(response);
        }
    });
    return '<div id="'+ div_id +'"><table class="table table-striped"><tr><td class="vert-align"><strong>Nome</strong></td><td class="vert-align"><strong>Porta d\'accesso</strong></td></tr></div>';
}

$('.tlink').popover({
    "html": true,
    "content": function() {
        var div_id =  "tmp-id-" + $.now();
        return details_in_popup('<?php echo EsNewUrl('main','tunnels_on');?>'+'?id='+$(this).data('id'), div_id);
    }
});

$('.tlink').on('show.bs.popover', function () {
    var id = $(this).data('id');
    if (tunnels != null && tunnels != id)
        $('#con_'+tunnels).popover('hide');
    //alert('sd '+);
    tunnels = id;
});


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
            $('#con_'+id).popover('hide');
        }
        switch (value['tunnel']) {
        case -1: // no tunnels available
            $('#ball_b_'+id).attr('class', 'ball_b ndisab');
            $('#bon_'+id).hide();
            $('#boff_'+id).hide();
            $('#con_'+id).hide();
            $('#con_'+id).popover('hide');
            break;
        case 0: // tunnel off
            $('#ball_b_'+id).attr('class', 'ball_b ndisab');
            $('#bon_'+id).hide();
            $('#boff_'+id).show();
            $('#con_'+id).hide();
            $('#con_'+id).popover('hide');
            break;
        case 1: // tunnel on
            $('#ball_b_'+id).attr('class', 'ball_b');
            $('#boff_'+id).hide();
            $('#bon_'+id).show();
            $('#con_'+id).show();
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
        $('.tlink').popover('hide');
        Action('<?php echo EsNewUrl('main','start');?>'+'?id='+$(this).data('id'));
	});
    
	$('.stop').click(function() {
        $('.tlink').popover('hide');
        Action('<?php echo EsNewUrl('main','stop');?>'+'?id='+$(this).data('id'));
	});
    
	$('.sms_start').click(function() {
        $('.tlink').popover('hide');
        Action('<?php echo EsNewUrl('main','sms_start');?>'+'?id='+$(this).data('id'));
	});
    
	$('.sms_stop').click(function() {
        $('.tlink').popover('hide');
        Action('<?php echo EsNewUrl('main','sms_stop');?>'+'?id='+$(this).data('id'));
	});

    
	NodesStatus();
});
</script>
