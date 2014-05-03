$(function(){
	AlertShow();
});

function AlertOff() {
    $('#message_box').fadeOut(1000);
}

function AlertShow() {
	//  message alert
	$('#message_box').click(function() {
		$(this).fadeOut(500);
	});
	if ($('#alert').text().length) {
		$('#message_box').fadeIn(600);
		setTimeout('AlertOff()', 5000);
	}
}

function AlertPrint(message) {
	$('#alert').text(message);
	$('#message_box').fadeIn(600);
	setTimeout('AlertOff()', 5000);
}
