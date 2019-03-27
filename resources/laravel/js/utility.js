window.abrirPopup = function( id ){
	$('.popup').removeClass('is-active');
	$('#'+id).addClass('is-active');
}

window.cerrarPopup = function( id ){
	$('.popup').removeClass('is-active');
}