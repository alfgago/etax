<div class="popup" id="importar-productos-popup">
    <div class="popup-container item-producto-form form-row">
        <div title="Cerrar ventana" class="close-popup" onclick="cerrarPopup('importar-popup');"> <i class="fa fa-times" aria-hidden="true"></i> </div>
        <form method="POST" action="/productos/importar" enctype="multipart/form-data">
            @csrf
            <div class="form-group col-md-12">
                <h3>
                    Importar productos
                </h3>
            </div>
            <div class="form-group col-md-12">
                <label for="tipo_archivo">Tipo de archivo</label>
                <select class="form-control" name="tipo_archivo" id="tipo_archivo" required>
                    <option value="xlsx" selected>Excel</option>
                </select>
            </div>
            <div class="form-group col-md-12">
                <div class="description">
                    Las columnas requeridas para importación de productos son: <br>

                    <ul class="cols-excel">
                        <li>Codigo</li>
                        <li>Nombre</li>
                        <li>UnidadMedida</li>
                        <li>Precio</li>
                        <li>Descripcion</li>
                        <li>CodigoEtax</li>
                    </ul>
                    * El orden puede variar, mantener nombres de columnas. Debe utilizar una fila por cada producto.
                    <br>
                    <a href="{{asset('assets/files/PlantillaProductos.xlsx')}}" class="btn btn-link" title="Descargar plantilla" download><i class="fa fa-file-excel-o" aria-hidden="true"></i> Descargar plantilla</a>
                </div>
            </div>
            <div class="form-group col-md-12">
                <label for="archivo">Archivo</label>
                <div class="">
                    <div class="fallback">
                        <input name="archivo" type="file" multiple="false">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Importar productos</button>
        </form>
    </div>
</div>
