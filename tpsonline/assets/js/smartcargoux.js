function initialize(){
	var el_modal =
	'<div class="modal fade" id="smartcargo_alert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">' +
    '    <div class="modal-dialog">' +
    '        <div class="modal-content">' +
    '           <div class="modal-header">' +
    '                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
    '                <h4 class="modal_title">Modal title</h4>' +
    '        	 </div>' +
    '             <div class="modal-body">' +
    '             </div>' +
    '             <div class="modal-footer">' +
    '                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>' +
    '            </div>' +
    '        </div>' +
    '    </div>' +
    '</div>';
	
	$(el_modal).appendTo('body');
	$('#smartcargo_alert').modal({
		backdrop : true,
		keyboard : true,
		show : false,
	});		
}

function sc_alert(title, msg, type){
	$('#smartcargo_alert').find('.modal_title').html(title);
	$('#smartcargo_alert').find('.modal-body').html(msg);
	
	$('#smartcargo_alert').modal('show');
}