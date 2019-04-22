window.abrirPopup = function( id ){
	$('.popup').removeClass('is-active');
	$('#'+id).addClass('is-active');
}

window.cerrarPopup = function( id ){
	$('.popup').removeClass('is-active');
}

$( document ).ready(function() {
	
    $('.select-search').select2({
		  templateResult: function (data, container) {
		    if (data.element) {
		      $(container).addClass($(data.element).attr("class"));
		    }
		    return data.text;
		  }
    });
    
	  $('.select2-tags').select2();
	
});