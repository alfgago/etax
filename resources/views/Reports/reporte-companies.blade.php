<div class="my-3 print-page">
  <div class="print-content">
    <div class="container-fluid px-0">      
      <div class="row">  
        <div class="col-12">          
          <h1 class="general card-title">Actividades Económicas y Facturas Emitidas  - Ultimos 3 meses</h1>
        </div>    
        <div class="col-sm-12">
         
          <table id="" class="table table-striped dataTable-collapse text-center ivas-table">
            <thead>
              <tr class="first-header">
                <th>Cédula</th>
                <th>Empresa</th>
                <th>Actividad Económica</th>
                <th>Facturas Emitidas ({{strtoupper($mes1)}}) </th>
                <th>Facturas Emitidas ({{strtoupper($mes2)}}) </th>
                <th>Facturas Emitidas ({{strtoupper($mes3)}}) </th>
              </tr>
            </thead>
            <tbody>
              @foreach( $companies as $c)
                <tr class="col">
                  <td>{{ $c->id_number }}</td>
                  <td>{{ $c->business_name }}</td>
                  <td>{{ $c->commercial_activities }}</td>
                  <td>{{ $c->MES1 }}</td>
                  <td>{{ $c->MES2 }}</td>
                  <td>{{ $c->MES3 }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>

        </div>
      </div>

    </div>
  </div>

  
