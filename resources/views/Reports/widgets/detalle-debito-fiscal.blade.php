          <?php
          
          $data1 = $e->parsedIvaData();
          $data2 = $f->parsedIvaData();
          $data3 = $m->parsedIvaData();
          $data4 = $a->parsedIvaData();
          $data5 = $y->parsedIvaData();
          $data6 = $j->parsedIvaData();
          $data7 = $l->parsedIvaData();
          $data8 = $g->parsedIvaData();
          $data9 = $s->parsedIvaData();
          $data10 = $c->parsedIvaData();
          $data11 = $n->parsedIvaData();
          $data12 = $d->parsedIvaData();
          $data0 = $acumulado->parsedIvaData();
          
          ?>

<div class="car o-hidden mb-4">
  <div class="car-body">
      <h3 class="card-title">{{ $titulo }}</h3>

      <div class="table-responsive">

          <table id="" class="table table-striped dataTable-collapse text-center ivas-table">
              <thead>
                  <tr class="first-header">
                      <th colspan="1">Concepto</th>
                      <th colspan="2">Enero</th>
                      <th colspan="2">Febrero</th>
                      <th colspan="2">Marzo</th>
                      <th colspan="2">Abrril</th>
                      <th colspan="2">Mayo</th>
                      <th colspan="2">Junio</th>
                      <th colspan="2">Julio</th>
                      <th colspan="2">Agosto</th>
                      <th colspan="2">Setiembre</th>
                      <th colspan="2">Octubre</th>
                      <th colspan="2">Noviembre</th>
                      <th colspan="2">Diciembre</th>
                      <th colspan="2">Acumulado</th>
                  </tr>
                  <tr class="second-header">
                      <th class="concepto">Tipo de IVA</th>
                      <th class="ene">Base</th>
                      <th class="ene">IVA</th>
                      <th class="feb">Base</th>
                      <th class="feb">IVA</th>
                      <th class="mar">Base</th>
                      <th class="mar">IVA</th>
                      <th class="abr">Base</th>
                      <th class="abr">IVA</th>
                      <th class="may">Base</th>
                      <th class="may">IVA</th>
                      <th class="jun">Base</th>
                      <th class="jun">IVA</th>
                      <th class="jul">Base</th>
                      <th class="jul">IVA</th>
                      <th class="ago">Base</th>
                      <th class="ago">IVA</th>
                      <th class="set">Base</th>
                      <th class="set">IVA</th>
                      <th class="oct">Base</th>
                      <th class="oct">IVA</th>
                      <th class="nov">Base</th>
                      <th class="nov">IVA</th>
                      <th class="dic">Base</th>
                      <th class="dic">IVA</th>
                      <th class="acum">Base</th>
                      <th class="acum">IVA</th>
                  </tr>
              </thead>
              <tbody>
                  @foreach ( \App\CodigoIvaRepercutido::get() as $tipo )
                    <?php 
                        $bVar = "b".$tipo->id;
                        $iVar = "i".$tipo->id;
                    ?>
                    <tr class="r-{{ $tipo['codigo'] }}">
                        <th>{{ $tipo->name }}</th>
                        <td>{{ @$data1->$bVar ? number_format( $data1->$bVar, 0 ) : '-' }}</td>
                        <td>{{ @$data1->$iVar ? number_format( $data1->$iVar, 0 ) : '-' }}</td>
                        <td>{{ @$data2->$bVar ? number_format( $data2->$bVar, 0 ) : '-' }}</td>
                        <td>{{ @$data2->$iVar ? number_format( $data2->$iVar, 0 ) : '-' }}</td>
                        <td>{{ @$data3->$bVar ? number_format( $data3->$bVar, 0 ) : '-' }}</td>
                        <td>{{ @$data3->$iVar ? number_format( $data3->$iVar, 0 ) : '-' }}</td>
                        <td>{{ @$data4->$bVar ? number_format( $data4->$bVar, 0 ) : '-' }}</td>
                        <td>{{ @$data4->$iVar ? number_format( $data4->$iVar, 0 ) : '-' }}</td>
                        <td>{{ @$data5->$bVar ? number_format( $data5->$bVar, 0 ) : '-' }}</td>
                        <td>{{ @$data5->$iVar ? number_format( $data5->$iVar, 0 ) : '-' }}</td>
                        <td>{{ @$data6->$bVar ? number_format( $data6->$bVar, 0 ) : '-' }}</td>
                        <td>{{ @$data6->$iVar ? number_format( $data6->$iVar, 0 ) : '-' }}</td>
                        <td>{{ @$data7->$bVar ? number_format( $data7->$bVar, 0 ) : '-' }}</td>
                        <td>{{ @$data7->$iVar ? number_format( $data7->$iVar, 0 ) : '-' }}</td>
                        <td>{{ @$data8->$bVar ? number_format( $data8->$bVar, 0 ) : '-' }}</td>
                        <td>{{ @$data8->$iVar ? number_format( $data8->$iVar, 0 ) : '-' }}</td>
                        <td>{{ @$data9->$bVar ? number_format( $data9->$bVar, 0 ) : '-' }}</td>
                        <td>{{ @$data9->$iVar ? number_format( $data9->$iVar, 0 ) : '-' }}</td>
                        <td>{{ @$data10->$bVar ? number_format( $data10->$bVar, 0 ) : '-' }}</td>
                        <td>{{ @$data10->$iVar ? number_format( $data10->$iVar, 0 ) : '-' }}</td>
                        <td>{{ @$data11->$bVar ? number_format( $data11->$bVar, 0 ) : '-' }}</td>
                        <td>{{ @$data11->$iVar ? number_format( $data11->$iVar, 0 ) : '-' }}</td>
                        <td>{{ @$data12->$bVar ? number_format( $data12->$bVar, 0 ) : '-' }}</td>
                        <td>{{ @$data12->$iVar ? number_format( $data12->$iVar, 0 ) : '-' }}</td>
                        <td>{{ @$data0->$bVar ? number_format( $data0->$bVar, 0 ) : '-' }}</td>
                        <td>{{ @$data0->$iVar ? number_format( $data0->$iVar, 0 ) : '-' }}</td>
                    </tr>
                  @endforeach

              </tbody>
          </table>
      </div>
  </div>
</div>