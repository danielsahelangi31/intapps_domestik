var toggle = true;
var target = $('#jenis_kapal');
var tpl = null;

function woke(){

if(toggle){
    // Enter Transition
    tpl = $('#overlay_template').clone();
    
    $(tpl).find('.imr-overlay-ct').css({
        width : $(target).outerWidth() - 20
    });
    
    $(tpl).css({
        position : 'absolute',
        overflow : 'hidden',
        top : $(target).offset().top,
        left : $(target).offset().left,
        width : $(target).outerWidth(),
        height : $(target).outerHeight(),
    }).appendTo('body').find('.imr-overlay-ct').css({
		position : 'relative',
		left : 0 - $(target).outerWidth(),
        opacity : 0
	}).animate({
        opacity : 1,
        left : 0
    }, 200, 'swing');
	
	$(tpl).fadeIn(100);
}else{
    // Exit Transition
    $(tpl).fadeOut(200).find('.imr-overlay-ct').animate({
        left : $(target).outerWidth(),
        width : 0
    }, 200, function(){
		$(this).parent().remove();
	});
}

toggle = !toggle;

}