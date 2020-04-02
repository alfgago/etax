
    <input type="hidden" class="form-control" name="is_catalogue" id="is_catalogue" value="true" required>
    
    <div class="form-group col-md-4">
      <label for="code">Código *</label>
      <input type="text" class="form-control" name="code" id="code" value="<?php echo e(@$client->code); ?>" required>
    </div>
    
    <div class="form-group col-md-4">
      <label for="tipo_persona">Tipo de persona *</label>
      <select class="form-control" name="tipo_persona" id="tipo_persona" required onclick="toggleApellidos();" onchange="cambiarDireccion();">
        <option value="F" <?php echo e(@$client->tipo_persona == 'F' ? 'selected' : ''); ?> >Física</option>
        <option value="J" <?php echo e(@$client->tipo_persona == 'J' ? 'selected' : ''); ?>>Jurídica</option>
        <option value="D" <?php echo e(@$client->tipo_persona == 'D' ? 'selected' : ''); ?>>DIMEX</option>
        <option value="N" <?php echo e(@$client->tipo_persona == 'N' ? 'selected' : ''); ?>>NITE</option>
        <option value="E" <?php echo e(@$client->tipo_persona == 'E' ? 'selected' : ''); ?>>Extranjero</option>
        <option value="O" <?php echo e(@$client->tipo_persona == 'O' ? 'selected' : ''); ?>>Otro</option>
      </select>
    </div>
    
    <div class="form-group col-md-4">
      <label for="id_number">Número de identificación *</label>
      <input type="text" class="form-control" name="id_number" id="id_number" value="<?php echo e(@$client->id_number); ?>" required onchange="getJSONCedula(this.value);" maxlength="20">
    </div>

    <div class="form-group col-md-4">
      <label for="first_name">Nombre *</label>
      <input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo e(@$client->first_name); ?>" required>
    </div>
    
    <div class="form-group col-md-4">
      <label for="last_name">Apellido</label>
      <input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo e(@$client->last_name); ?>" >
    </div>
    
    <div class="form-group col-md-4">
      <label for="last_name2">Segundo apellido</label>
      <input type="text" class="form-control" name="last_name2" id="last_name2" value="<?php echo e(@$client->last_name2); ?>" >
    </div>
    
    <div class="form-group col-md-4">
      <label for="email">Correo electrónico *</label>
      <input type="text" class="form-control" name="email" id="email" value="<?php echo e(@$client->email); ?>" required onblur="validateEmail();" maxlength="160">
    </div>
    
    <div class="form-group col-md-4">
      <label for="phone">Teléfono</label>
      <input type="number" pattern="/[0-9]|\./$" class="form-control" name="phone" id="phone" value="<?php echo e(@$client->phone); ?>" maxlength="20"  onblur="validatePhoneFormat();">
    </div>
    
    <div class="form-group col-md-4"></div>
    <div class="form-group col-md-4">
      <label for="country">País *</label>
      <select class="form-control" name="country" id="country" value="<?php echo e(@$client->country); ?>" required onchange="cambiarTipoPersona();">
          <option value="CR">CR - Costa Rica</option>
          <?php $__currentLoopData = \App\CodigosPaises::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pais): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($pais['country_code']); ?>" <?php echo e($pais['country_code'] === @$client->country ? 'selected' : ''); ?>><?php echo e($pais['country_code']); ?> - <?php echo e($pais['country_name']); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>

    <div class="form-group col-md-4" id="divState">
      <label for="state">Provincia *</label>
      <select class="form-control" name="state" id="state" value="<?php echo e(@$client->state); ?>" onchange="fillCantones();">
      </select>
    </div>

    <div class="form-group col-md-4" id="divCity">
      <label for="city">Canton *</label>
      <select class="form-control" name="city" id="city" value="<?php echo e(@$client->city); ?>" onchange="fillDistritos();">
      </select>
    </div>

    <div class="form-group col-md-4" id="divDistrict">
      <label for="district">Distrito *</label>
      <select class="form-control" name="district" id="district" value="<?php echo e(@$client->district); ?>" onchange="fillZip();" >
      </select>
    </div>

    <div class="form-group col-md-4" id="divNeighborhood">
      <label for="neighborhood">Barrio</label>
      <input class="form-control" name="neighborhood" id="neighborhood" value="<?php echo e(@$client->neighborhood); ?>" >
      </select>
    </div>

    <div class="form-group col-md-4" id="divZip">
      <label for="zip">Código Postal</label>
      <input type="text" class="form-control" name="zip" id="zip" value="<?php echo e(@$client->zip); ?>" readonly >
    </div>

    <div class="form-group col-md-12" id="divAddress">
      <label for="address">Dirección</label>
      <textarea class="form-control" name="address" id="address" maxlength="250" rows="2" style="resize: none;"><?php echo e(@$client->address); ?></textarea>
    </div>

    <div class="form-group col-md-12" id="extranjero" hidden>
        <label for="address">Otras Señas Extranjero</label>
        <textarea class="form-control" name="foreign_address" id="foreign_address" maxlength="300" rows="2" style="resize: none;"><?php echo e(@$client->foreign_address); ?></textarea>
    </div>
    <div class="form-group col-md-12">
      <label for="billing_emails">Correos electrónicos para facturación</label>
      <div class="form-group">
        <div data-no-duplicate="true" data-pre-tags-separator="," data-no-duplicate-text="Correos duplicados" data-type-zone-class="type-zone" 
          data-tag-box-class="tagging" id="billing_emails" data-tags-input-name="billing_emails"><?php echo e(@$client->billing_emails); ?></div>
        <p class="text-muted"><small>Ingrese los correos separados por coma. Si lo deja en blanco, por defecto se enviarán las facturas al correo electrónico del cliente.</small> </p>
      </div>
    </div>
    
    <div class="form-group col-md-4">
      <label for="emisor_receptor">Emisor / Receptor</label>
      <select class="form-control" name="emisor_receptor" id="emisor_receptor" >
        <option value="ambos" <?php echo e(@$client->emisor_receptor == '1' ? 'selected' : ''); ?>>Emisor y receptor</option>
        <option value="receptor" <?php echo e(@$client->emisor_receptor == '2' ? 'selected' : ''); ?>>Receptor</option>
        <option value="emisor" <?php echo e(@$client->emisor_receptor == '3' ? 'selected' : ''); ?>>Emisor</option>
      </select>
    </div>
    <input hidden value="<?php echo e(@$client->id); ?>" id="id_client">
    <div class="form-group col-md-4">
        <label for="es_exento">Exento de IVA</label>
        <select class="form-control" name="es_exento" id="es_exento" >
          <option value="0" <?php echo e(@$client->es_exento ? '' : 'selected'); ?>>No</option>
          <option value="1" <?php echo e(@$client->es_exento ? 'selected' : ''); ?>>Sí</option>
        </select>
    </div>
<style>
    .error {
        border:1px solid red;
    }
</style>
<script>
    function cambiarDireccion() {
        var tipoPersona = $('#tipo_persona').val();
        var idClient = $('#id_client').val();
        if(tipoPersona != undefined){
            if (tipoPersona === 'E') {
                $('#divState').hide('slow');
                $('#state').attr('required', false);
                $('#divCity').hide('slow');
                $('#city').attr('required', false);
                $('#divDistrict').hide('slow');
                $('#district').attr('required', false);
                $('#divNeighborhood').hide('slow');
                $('#divZip').hide('slow');
                $('#divAddress').hide('slow');

                $('#extranjero').removeAttr('hidden');
                if(idClient == ''){
                    $('#country').val('US');
                }
            } else {
                $('#divState').show('slow');
                $('#state').attr('required', true);
                $('#divCity').show('slow');
                $('#city').attr('required', true);
                $('#divDistrict').show('slow');
                $('#district').attr('required', true);
                $('#divNeighborhood').show('slow');
                $('#divZip').show('slow');
                $('#divAddress').show('slow');

                $('#extranjero').attr("hidden", true);
                $('#country').val('CR');
                //setTimeout(fillProvincias, 1000);
            }
        }
    }
    cambiarDireccion();

    $("#id_number").keyup(function() {
      if( jQuery('#tipo_persona').val()!='E' ){
        $("#id_number").val(this.value.match(/[0-9]*/));
      }
    });
    function validateEmail() {
        var email = $('#email').val();
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if(re.test(String(email).toLowerCase()) != true){
            alert('La direccion de correo electronico no coincide con ningun formato de correo');
            $('#email').addClass('error');
        }else{
            $('#email').removeClass('error');
        }
    }

    function cambiarTipoPersona(){
        var country = $('#country').val();
        if(country !== 'CR'){
            $('#divState').hide('slow');
            $('#divCity').hide('slow');
            $('#divDistrict').hide('slow');
            $('#divNeighborhood').hide('slow');
            $('#divZip').hide('slow');
            $('#divAddress').hide('slow');

            $('#extranjero').removeAttr('hidden');
            $('#tipo_persona').val('E');
        }else{
            $('#divState').show('slow');
            $('#divCity').show('slow');
            $('#divDistrict').show('slow');
            $('#divNeighborhood').show('slow');
            $('#divZip').show('slow');
            $('#divAddress').show('slow');

            $('#extranjero').attr("hidden", true);
            $('#tipo_persona').val('F');
            //setTimeout(fillProvincias, 1000);
        }
    }
</script>
<?php /**PATH /home/237808.cloudwaysapps.com/ducfpkkugc/public_html/resources/views/Client/form.blade.php ENDPATH**/ ?>