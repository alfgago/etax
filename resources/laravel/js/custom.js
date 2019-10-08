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
    var sel = $('#state');
    sel.html("");
    sel.append("<option value='0' selected>-- Seleccione una provincia --</option>");
    $.each(provincias, function(i, val) {
      sel.append("<option value='" + i + "'>" + provincias[i]["Nombre"] + "</option>");
    });
}

window.fillCantones = function() {
  var provincia = $('#state').val();
  var sel = $('#city');
  sel.html("");
  sel.append("<option value='0' selected>-- Seleccione un cantón --</option>");
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
  sel.append("<option value='0' selected>-- Seleccione un distrito --</option>");
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

window.checkProporciones100 = function (){
  	var r1 = parseFloat($('#operative_ratio1').val());
  	var r2 = parseFloat($('#operative_ratio2').val());
  	var r3 = parseFloat($('#operative_ratio3').val());
  	var r4 = parseFloat($('#operative_ratio4').val());
  	var sum = r1+r2+r3+r4;

  	if( !(sum > 99.75 && sum < 100.05) ) {
  	  $('.proporciones input').css('border-color', 'red');
      $('#validate-ratios-text').show();
    }else {
      $('.proporciones input').css('border-color', '#999');
      $('#validate-ratios-text').hide();
    }
}


window.companyChange = function($redirect = false) {
    var sel = $('#company_change').val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: "/change-company",
        method: 'post',
        data: {
            companyId: sel
        },
        success: function (result) {
            if ($redirect) {

                if (typeof is_edit === 'undefined') {
                    window.location.href = window.location.href;
                } else {
                    window.location.href = result;
                }
            }else{
              window.location.href = "/";
            }

        }});

}

window.validatePhoneFormat = function () {
    var phone = $('#phone').val();
    var numbers = /^[0-9]+$/;
    if(phone.length > 20 || !phone.value.match(numbers)){
        Swal.fire({
            type: 'error',
            title: 'Información',
            text: 'El número no debe poseer más de 20 dígitos'
        })
        $('#phone').val('');
    }
}

window.validateEmail = function(mail){
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)){
        return (true)
    }
    alert("Debe ingresar una dirección de email válida");
    $("#email").val('');
    return (false)
}
window.fowardFields = function() {
    $('#tipo_persona').addClass('checkEmpty');
    $('#id_number').addClass('checkEmpty');
    $('#first_name').addClass('checkEmpty');
    $('#email').addClass('checkEmpty');
    $('#phone').addClass('checkEmpty');
    $('#country').addClass('checkEmpty');
    $('#state').addClass('checkEmpty');
    $('#city').addClass('checkEmpty');
    $('#district').addClass('checkEmpty');
    $('#zip').addClass('checkEmpty');
    $('#neighborhood').addClass('checkEmpty');
    $('#address').addClass('checkEmpty');
    $('#es_exento').addClass('checkEmpty');
    $('#number').addClass('checkEmpty');
    $('#expiry').addClass('checkEmpty');
    $('#cvc').addClass('checkEmpty');
    $('#first_name_card').addClass('checkEmpty');
    $('#last_name_card').addClass('checkEmpty');
}
window.backFields = function () {
    $('#tipo_persona').removeClass('checkEmpty');
    $('#id_number').removeClass('checkEmpty');
    $('#first_name').removeClass('checkEmpty');
    $('#email').removeClass('checkEmpty');
    $('#phone').removeClass('checkEmpty');
    $('#country').removeClass('checkEmpty');
    $('#state').removeClass('checkEmpty');
    $('#city').removeClass('checkEmpty');
    $('#district').removeClass('checkEmpty');
    $('#zip').removeClass('checkEmpty');
    $('#neighborhood').removeClass('checkEmpty');
    $('#address').removeClass('checkEmpty');
    $('#es_exento').removeClass('checkEmpty');
    $('#number').removeClass('checkEmpty');
    $('#expiry').removeClass('checkEmpty');
    $('#cvc').removeClass('checkEmpty');
    $('#first_name_card').removeClass('checkEmpty');
    $('#last_name_card').removeClass('checkEmpty');
}

window.getCyberData = function(){
    var x = document.createElement("INPUT");
    x.setAttribute("type", "hidden");
    x.setAttribute("id", "deviceFingerPrintID");
    x.setAttribute("name", "deviceFingerPrintID");
    x.setAttribute("value", cybs_dfprofiler("tc_cr_011007172","test"));
    //$("#deviceFingerPrintID").val(cybs_dfprofiler("tc_cr_011007172","test"));
    $('.tarjeta').append(x);

    var state = $( "#state option:selected" ).text();
    if(state){
        if(state.length > 40){
            state = state.substring(0, 40);
        }
        $('#cardState').val(state);
    }

    var city = $( "#city option:selected" ).text();
    if(city){
        if(city.length > 40){
            city = city.substring(0, 40);
        }
        $('#cardCity').val(city);
    }

    var address = $('#address').val();
    if(address.length > 40){
        address = address.substring(0, 40);
    }

    $('#address1').val(address);
    var neighborhood = $( "#neighborhood" ).val();
    if(neighborhood.length > 40){
        neighborhood = neighborhood.substring(0, 40);
    }
    $('#street1').val(neighborhood);

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

  $('.select-search-wizard').select2({
    dropdownParent: $('#wizard-popup')
  });

  $('.select2-tags').select2({
    tags: true,
    tokenSeparators: [',', ' ']
  });

	toggleTiposImportacion();
	initHelpers();

    $('#input_logo').on('change', function() {
        filename = this.files[0].name;
        $('#logo-name').text(filename)
    });

    $('form').submit(function() {
        $('#btn-submit-fe').attr('disabled', true);
        $('#btn-submit-tc').attr('disabled', true);
    });

    $('.proporciones').on('change', function() {
        checkProporciones100();
    });

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
