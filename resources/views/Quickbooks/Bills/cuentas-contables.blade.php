
<select class="form-control select-search" name="bills_accounts[{{$facturaEtax->id}}]" placeholder="" required>
    <option class="crear-nuevo" value='0'>-- Sin asignar --</option>
    <?php
      $selectedAcc = $facturaEtax->accId;
    ?>
    @foreach ( $cuentasQb as $cuenta )
      <option {{ $selectedAcc == $cuenta->Id ? 'selected' : '' }} value="{{ $cuenta->Id }}" >{{ $cuenta->Classification }}: {{ $cuenta->FullyQualifiedName }}</option>
    @endforeach
</select>