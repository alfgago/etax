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
                  @foreach ( \App\Variables::tiposIVARepercutidos() as $tipo )
                    <?php 
                        $bVar = "b".$tipo['codigo'];
                        $iVar = "i".$tipo['codigo'];
                    ?>
                    <tr class="r-{{ $tipo['codigo'] }}">
                        <th>{{ $tipo['nombre'] }}</th>
                        <td>{{ $e->$bVar ? number_format( $e->$bVar, 0 ) : '-' }}</td>
                        <td>{{ $e->$iVar ? number_format( $e->$iVar, 0 ) : '-' }}</td>
                        <td>{{ $f->$bVar ? number_format( $f->$bVar, 0 ) : '-' }}</td>
                        <td>{{ $f->$iVar ? number_format( $f->$iVar, 0 ) : '-' }}</td>
                        <td>{{ $m->$bVar ? number_format( $m->$bVar, 0 ) : '-' }}</td>
                        <td>{{ $m->$iVar ? number_format( $m->$iVar, 0 ) : '-' }}</td>
                        <td>{{ $a->$bVar ? number_format( $a->$bVar, 0 ) : '-' }}</td>
                        <td>{{ $a->$iVar ? number_format( $a->$iVar, 0 ) : '-' }}</td>
                        <td>{{ $y->$bVar ? number_format( $y->$bVar, 0 ) : '-' }}</td>
                        <td>{{ $y->$iVar ? number_format( $y->$iVar, 0 ) : '-' }}</td>
                        <td>{{ $j->$bVar ? number_format( $j->$bVar, 0 ) : '-' }}</td>
                        <td>{{ $j->$iVar ? number_format( $j->$iVar, 0 ) : '-' }}</td>
                        <td>{{ $l->$bVar ? number_format( $l->$bVar, 0 ) : '-' }}</td>
                        <td>{{ $l->$iVar ? number_format( $l->$iVar, 0 ) : '-' }}</td>
                        <td>{{ $g->$bVar ? number_format( $g->$bVar, 0 ) : '-' }}</td>
                        <td>{{ $g->$iVar ? number_format( $g->$iVar, 0 ) : '-' }}</td>
                        <td>{{ $s->$bVar ? number_format( $s->$bVar, 0 ) : '-' }}</td>
                        <td>{{ $s->$iVar ? number_format( $s->$iVar, 0 ) : '-' }}</td>
                        <td>{{ $c->$bVar ? number_format( $c->$bVar, 0 ) : '-' }}</td>
                        <td>{{ $c->$iVar ? number_format( $c->$iVar, 0 ) : '-' }}</td>
                        <td>{{ $n->$bVar ? number_format( $n->$bVar, 0 ) : '-' }}</td>
                        <td>{{ $n->$iVar ? number_format( $n->$iVar, 0 ) : '-' }}</td>
                        <td>{{ $d->$bVar ? number_format( $d->$bVar, 0 ) : '-' }}</td>
                        <td>{{ $d->$iVar ? number_format( $d->$iVar, 0 ) : '-' }}</td>
                        <td>{{ $acumulado->$bVar ? number_format( $acumulado->$bVar, 0 ) : '-' }}</td>
                        <td>{{ $acumulado->$iVar ? number_format( $acumulado->$iVar, 0 ) : '-' }}</td>
                    </tr>
                  @endforeach

              </tbody>
          </table>
      </div>
  </div>
</div>