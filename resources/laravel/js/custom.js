window.abrirPopup = function(id) {
  $('.popup').removeClass('is-active');
  $('#' + id).addClass('is-active');
}

window.cerrarPopup = function(id) {
  $('.popup').removeClass('is-active');
}

window.toggleApellidos = function() {
  var tipoPersona = $('#tipo_persona').val();
  if (tipoPersona == 'J') {
    $('#last_name, #last_name2').val('');
    $('#last_name, #last_name2').attr('readonly', 'true');
    $("label[for=name], label[for=first_name]").html( "Nombre comercial" );
    $("label[for=business_name], #business_name").show();
  } else {
    $('#last_name, #last_name2').removeAttr('readonly');
    $("label[for=name], label[for=first_name]").html( "Nombre" );
    $("label[for=business_name], #business_name").hide();
  }
}

window.setCedulaJSONResults = function(json) {
	var type = json.results[0].type;
	var fullname = json.results[0].fullname;

	$("#business_name").val( fullname );
	$("#name").val( fullname );
	$("#first_name").val( fullname );
	
	$('#tipo_persona').val( type );

	if( type == "F" ) {
		var firstname1 = json.results[0].firstname1;
		var lastname1 = json.results[0].lastname1;
		var lastname2 = json.results[0].lastname2;
        $("#name").val( firstname1 );
        $("#first_name").val( firstname1 );
        $("#last_name").val( lastname1 );
        $("#last_name2").val( lastname2 );
  }
  
  toggleApellidos();
}

window.getJSONCedula = function( cedula ) {
  
  if( cedula.length >= 9 ){
    
    var xhttp = new XMLHttpRequest();
    var json;
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
           // Typical action to be performed when the document is ready:
           json = JSON.parse( xhttp.responseText );
    	     setCedulaJSONResults(json);
        }
    };
    xhttp.open("GET", "https://apis.gometa.org/cedulas/"+cedula+"&key=1AJTv1VNqFtSMpc", true);
    xhttp.send();
    
  }
  
}

window.fillProvincias = function() {
  if ($('#country').val() == 'CR') {
    var sel = $('#state');
    sel.html("");
    sel.append("<option val='0' selected>-- Seleccione una provincia --</option>");
    $.each(provincias, function(i, val) {
      sel.append("<option value='" + i + "'>" + provincias[i]["Nombre"] + "</option>");
    });
  }
}

window.fillCantones = function() {
  var provincia = $('#state').val();
  var sel = $('#city');
  sel.html("");
  sel.append("<option val='0' selected>-- Seleccione un cantón --</option>");
  $.each(cantones, function(i, val) {
    if (provincia == cantones[i]["Provincia"]) {
      sel.append("<option value='" + i + "'>" + cantones[i]["Nombre"] + "</option>");
    }
  });
}

window.fillDistritos = function() {
  var canton = $('#city').val();
  var sel = $('#district');
  sel.html("");
  sel.append("<option val='0' selected>-- Seleccione un distrito --</option>");
  $.each(distritos, function(i, val) {
    if (canton == distritos[i]["Canton"]) {
      sel.append("<option value='" + i + "'>" + distritos[i]["Nombre"] + "</option>");
    }
  });
}

window.fillZip = function() {
  var distrito = $('#district').val();
  var sel = $('#zip').val(distrito);
}

window.agregarClienteNuevo = function() {
	var allow = true;
	$('.checkEmpty').each( function() {
		if( $(this).val() && $(this).val() != "" ){ 
			$(this).attr('style', 'border-color: ;');
		}else{
			$(this).attr('style', 'border-color:red;');
			allow = false;
    }
	});
	
	if( allow ){
        var cnombre = $('#first_name').val() + " " + $('#last_name').val() + " " + $('#last_name2').val() ;
        if(! $('#nuevo-cliente-opt').length ) {
            $("select#client_id > option:nth-of-type(1)").after("<option id='nuevo-cliente-opt' value='-1'> NUEVO - "+ cnombre +" </option>");
        }else{
            $('#nuevo-cliente-opt').text( "NUEVO - "+ cnombre );
        }
        $('select#client_id').select2();
        $('select#client_id').val('-1');
        $('select#client_id').change();
        cerrarPopup('nuevo-cliente-popup');
	}
}

window.toggleTiposImportacion = function (){
		var tipo = $(".popup.is-active #tipo_archivo").val();
		if(!tipo) {
		  tipo = 'xlsx';
		}
		
		$(".toggle-xml, .toggle-xlsx").hide();
		$(".toggle-"+tipo).show();
		
	}
	
window.initHelpers = function (){
  	$('.helper').each( function() {
  	  
  	  var def = $(this).attr('def');
  	  var helperContent = $('#'+def).html();
  	  tippy( '.'+def, { arrow: true, interactive: true, content: helperContent } );
  	  
  	});
}

$(document).ready(function() {

  $('.select-search').select2({
    templateResult: function(data, container) {
      if (data.element) {
        $(container).addClass($(data.element).attr("class"));
      }
      return data.text;
    }
  });

  $('.select2-tags').select2();
	  
	toggleTiposImportacion();
	initHelpers();

});

toastr.options = {
  "closeButton": false,
  "debug": false,
  "newestOnTop": true,
  "progressBar": false,
  "positionClass": "toast-top-right",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "1000",
  "hideDuration": "1000",
  "timeOut": "60000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}