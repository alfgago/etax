<?php

use Illuminate\Database\Seeder;

class ProductCategorySeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
              
        $listaVentas = [
          //Ventas al 1%
          ['grupo'=>'V1','nombre'=>'Bienes generales al 1%', 'invoice_iva_code'=>'B101', 'open_codes'=>'B101,B161'],
          ['grupo'=>'V1','nombre'=>'Bienes de capital al 1%', 'invoice_iva_code'=>'B171', 'open_codes'=>'B171'],
          ['grupo'=>'V1','nombre'=>'Servicios al 1%', 'invoice_iva_code'=>'S101', 'open_codes'=>'S101,S161'],
          ['grupo'=>'V1','nombre'=>'Uso o consumo personal de mercancias y servicios al 1%', 'invoice_iva_code'=>'B121', 'open_codes'=>'B121,S121'],
          ['grupo'=>'V1','nombre'=>'Transferencias sin contraprestación a terceros 1%', 'invoice_iva_code'=>'B125', 'open_codes'=>'S125,B125'],
          //Ventas al 2%
          ['grupo'=>'V2','nombre'=>'Medicamentos, materias primas, insumos, maquinaria, equipo y reactivos para su producción', 'invoice_iva_code'=>'B102', 'open_codes'=>'B102,S102,B162,S162,B122,S122,B126,S126,B172'],
          ['grupo'=>'V2','nombre'=>'Primas de seguros personales', 'invoice_iva_code'=>'S102', 'open_codes'=>'S102'],
          ['grupo'=>'V2','nombre'=>'Bienes y servicios que hagan las instituciones estatales de educación superior y otras autorizadas', 'invoice_iva_code'=>'S102', 'open_codes'=>'B102,S102,B162,S162,B122,S122,B126,S126,B172'],
          ['grupo'=>'V2','nombre'=>'Otros servicios de educación privada no acreditados por el MEP y/o CONESUP', 'invoice_iva_code'=>'S102', 'open_codes'=>'B102,S102,B162,S162,B122,S122,B126,S126,B172'],
          //Ventas al 4%
          ['grupo'=>'V4','nombre'=>'Boletos o pasajes aéreos nacionales', 'invoice_iva_code'=>'S104', 'open_codes'=>'S104,B104,S128,S124'],
          ['grupo'=>'V4','nombre'=>'Boletos o pasajes aéreos internacionales', 'invoice_iva_code'=>'S104', 'open_codes'=>'S104,B104,S128,S124'],
          ['grupo'=>'V4','nombre'=>'Servicios de salud privados', 'invoice_iva_code'=>'S104', 'open_codes'=>'S104,B104,S128,S124'],
          ['grupo'=>'V4','nombre'=>'Servicios de ingeniería, arquitectura, topografía, y construcción de obra civil', 'invoice_iva_code'=>'S114', 'open_codes'=>'S114,S118'],
          ['grupo'=>'V4','nombre'=>'Servicios de recolección, clasificación y almacenamiento de bienes reciclables y reutilizables, inscritos ante la AT y el Ministerio de Salud', 'invoice_iva_code'=>'S114', 'open_codes'=>'S114,S118'],
          //Ventas al 13%
          ['grupo'=>'V13','nombre'=>'Bienes generales al 13%', 'declaracion_name'=>'Bienes', 'invoice_iva_code'=>'B103', 'open_codes'=>'B103,B130,B163'],
          ['grupo'=>'V13','nombre'=>'Bienes de capital al 13%', 'declaracion_name'=>'Bienes de capital', 'invoice_iva_code'=>'B173', 'open_codes'=>'B173'],
          ['grupo'=>'V13','nombre'=>'Servicios al 13%', 'declaracion_name'=>'Servicios', 'invoice_iva_code'=>'S103', 'open_codes'=>'S103,S130,S163'],
          ['grupo'=>'V13','nombre'=>'Uso o consumo personal de mercancias y servicios al 13%', 'declaracion_name'=>'Uso o consumo personal de mercancias y servicios', 'invoice_iva_code'=>'B123', 'open_codes'=>'B123,S123'],
          ['grupo'=>'V13','nombre'=>'Transferencias sin contraprestación a terceros 13%', 'declaracion_name'=>'Transferencias sin contraprestación a terceros', 'invoice_iva_code'=>'B127', 'open_codes'=>'S127,B127'],
          //Rubros incluidos como gasto en base imponible
          ['grupo'=>'BI','nombre'=>'Incrementos en la base imponible por recaudación a nivel de mayorista', 'invoice_iva_code'=>'S250', 'open_codes'=>'S250,B250'],
          ['grupo'=>'BI','nombre'=>'Servicios adquiridos desde el exterior', 'invoice_iva_code'=>'S140', 'open_codes'=>'S140'],
          //Ventas exentas
          ['grupo'=>'VEX','nombre'=>'Exportaciones de bienes', 'invoice_iva_code'=>'B150', 'open_codes'=>'B150'],
          ['grupo'=>'VEX','nombre'=>'Exportaciones de servicios', 'invoice_iva_code'=>'S150', 'open_codes'=>'S150'],
          ['grupo'=>'VEX','nombre'=>'Venta local de bienes exentos', 'invoice_iva_code'=>'B200', 'open_codes'=>'B200'],
          ['grupo'=>'VEX','nombre'=>'Venta local de servicios exentos', 'invoice_iva_code'=>'S200', 'open_codes'=>'S200'],
          ['grupo'=>'VEX','nombre'=>'Créditos para descuentos de facturas y arrendamientos financieros', 'invoice_iva_code'=>'S200', 'open_codes'=>'S200'],
          ['grupo'=>'VEX','nombre'=>'Arrendamientos destinados a viviendas y accesorios, así como los lugares de culto religioso', 'invoice_iva_code'=>'S201', 'open_codes'=>'S201'],
          ['grupo'=>'VEX','nombre'=>'Arrendamientos utilizados por micro y pequeñas empresas', 'invoice_iva_code'=>'S201', 'open_codes'=>'S201'],
          ['grupo'=>'VEX','nombre'=>'Suministro de energía eléctrica residencial no mayor a 280 KW/H', 'invoice_iva_code'=>'S201', 'open_codes'=>'S201'],
          ['grupo'=>'VEX','nombre'=>'Venta o entrega de agua residencial no mayor a 30 M3', 'invoice_iva_code'=>'S201', 'open_codes'=>'S201'],
          ['grupo'=>'VEX','nombre'=>'Autoconsumo de bienes y servicios sin aplicación de créditos total o parcial', 'invoice_iva_code'=>'B240', 'open_codes'=>'B240,S240'],
          ['grupo'=>'VEX','nombre'=>'Venta de sillas de ruedas y similares, equipo ortopédico, prótesis y equipo', 'invoice_iva_code'=>'B200', 'open_codes'=>'B200,S200'],
          ['grupo'=>'VEX','nombre'=>'Venta de bienes y servicios a instituciones públicas y privadas exentas', 'invoice_iva_code'=>'B260', 'open_codes'=>'B260,S260'],
          ['grupo'=>'VEX','nombre'=>'Aranceles por matrícula y créditos de cursos en universidades públicas y privadas exentas', 'invoice_iva_code'=>'S200', 'open_codes'=>'B200,S200'],
          ['grupo'=>'VEX','nombre'=>'Servicios de transporte terrestre de pasajeros y cabotaje de personas con concesión', 'invoice_iva_code'=>'S170', 'open_codes'=>'S170,B200,S200'],
          ['grupo'=>'VEX','nombre'=>'Venta, arrendamiento y leasing de autobuses y embarcaciones', 'invoice_iva_code'=>'S200', 'open_codes'=>'B200,S200'],
          ['grupo'=>'VEX','nombre'=>'Comisiones por el servicio de subasta ganadera y transacción de animales', 'invoice_iva_code'=>'S200', 'open_codes'=>'B200,S200'],
          ['grupo'=>'VEX','nombre'=>'Venta, comercialización y matanza de animales vivos', 'invoice_iva_code'=>'S200', 'open_codes'=>'B200,S200'],
          //Ventas autorizadas sin impuesto
          ['grupo'=>'VAS','nombre'=>'Ventas sin impuesto a clientes autorizados por la Dirección General de Hacienda', 'invoice_iva_code'=>'B183', 'open_codes'=>'B181,S181,B182,S182,B183,S183,B184,S184,B170,S170'],
          ['grupo'=>'VAS','nombre'=>'Ventas sin impuesto a clientes autorizados por la Dirección General de Tributación', 'invoice_iva_code'=>'B183', 'open_codes'=>'B181,S181,B182,S182,B183,S183,B184,S184,B170,S170'],
          ['grupo'=>'VAS','nombre'=>'Ventas sin impuesto a clientes exonerados por ley especial', 'invoice_iva_code'=>'B183', 'open_codes'=>'B181,S181,B182,S182,B183,S183,B184,S184,B170,S170'],
          ['grupo'=>'VAS','nombre'=>'Ventas de bienes y servicios relacionados a la canasta básica tributaria exentos el 1er año de la ley', 'invoice_iva_code'=>'B165', 'open_codes'=>'B165,S165'],
          ['grupo'=>'VAS','nombre'=>'Servicios de ingeniería, arquitectura, topografía y construcción de obra civil con planos visados antes del 1 de octubre del 2019', 'invoice_iva_code'=>'S245', 'open_codes'=>'S245'],
          ['grupo'=>'VAS','nombre'=>'Servicios turísticos inscritos ante el Instituto Costarricense de Turismo', 'invoice_iva_code'=>'S245', 'open_codes'=>'S245'],
          ['grupo'=>'VAS','nombre'=>'Servicios de recolección, clasificación y almacenamiento de bienes reciclables y reutilizables, inscritos ante la Administración Tributaria y el Ministerio de Salud', 'invoice_iva_code'=>'S245', 'open_codes'=>'S245'],
          //Ventas no sujetas
          ['grupo'=>'VNS','nombre'=>'Bienes y servicios a la Caja Costarricense de Seguro Social', 'invoice_iva_code'=>'B183', 'open_codes'=>'B181,S181,B182,S182,B183,S183,B184,S184,B170,S170'],
          ['grupo'=>'VNS','nombre'=>'Bienes y servicios a las corporaciones municipales', 'invoice_iva_code'=>'B183', 'open_codes'=>'B181,S181,B182,S182,B183,S183,B184,S184,B170,S170'],
          ['grupo'=>'VNS','nombre'=>'Otras ventas no sujetas', 'invoice_iva_code'=>'B173', 'open_codes'=>'B181,S181,B182,S182,B183,S183,B184,S184,S300,B300,B170,S170'],
          //Usado para notas de débito.
          ['grupo'=>'DP','nombre'=>'Devoluciones a proveedores', 'invoice_iva_code'=>'B065', 'open_codes'=>'B065,B066,B067']
        ];
        
        $listaCompras = [
          ['grupo'=>'CL','nombre'=>'Compras locales de bienes utilizados en operaciones sujetas y no exentas', 'declaracion_name'=>'Bienes', 'bill_iva_code'=>'B003', 'open_codes'=>'B003,B063,B008,B068,B004,B064,B002,B062,B001,B011'],
          ['grupo'=>'CL','nombre'=>'Compras locales de servicios utilizados en operaciones sujetas y no exentas', 'declaracion_name'=>'Servicios', 'bill_iva_code'=>'S003', 'open_codes'=>'S003,S063,S008,S068,S004,S064,S002,S062,S001,S011'],
          ['grupo'=>'CL','nombre'=>'Compras locales de bienes de capital utilizados en operaciones sujetas y no exentas', 'declaracion_name'=>'Bienes de capital', 'bill_iva_code'=>'B013', 'open_codes'=>'B013,S013,B016,S016,B073,S073,B018,B078,B014,B012,B072,B015,S015,B011,S011,B071,S071'],
          ['grupo'=>'CI','nombre'=>'Importaciones de bienes utilizados en operaciones sujetas y no exentas', 'declaracion_name'=>'Bienes', 'bill_iva_code'=>'B023', 'open_codes'=>'B023,B043,B028,B048,B024,B044,B022,B042,B021,B041'],
          ['grupo'=>'CI','nombre'=>'Importaciones de servicios utilizados en operaciones sujetas y no exentas', 'declaracion_name'=>'Servicios', 'bill_iva_code'=>'S023', 'open_codes'=>'S023,S043,S028,S048,S024,S044,S022,S042,S021,S041'],
          ['grupo'=>'CI','nombre'=>'Importaciones de bienes de capital utilizados en operaciones sujetas y no exentas', 'declaracion_name'=>'Bienes de capital', 'bill_iva_code'=>'B033', 'open_codes'=>'B033,B036,S033,S036,B053,S053,B038,S038,B058,S058,B034,B054,S034,S054,B032,B052,S052,S032,B031,B035,B051,S031,S035,S051'],
          ['grupo'=>'CE','nombre'=>'Compras locales de bienes y servicios exentos', 'declaracion_name'=>'Locales', 'bill_iva_code'=>'B060', 'open_codes'=>'B060,S060,B070,S070'],
          ['grupo'=>'CE','nombre'=>'Importaciones de bienes y servicios exentos', 'declaracion_name'=>'Importados', 'bill_iva_code'=>'B040', 'open_codes'=>'B040,S040,B050,S050'],
          ['grupo'=>'CNR','nombre'=>'Compras locales de bienes y servicios no relacionados directamente con la actividad', 'declaracion_name'=>'Locales', 'bill_iva_code'=>'B097', 'open_codes'=>'B097,S097'],
          ['grupo'=>'CNR','nombre'=>'Importaciones de bienes y servicios no relacionados directamente con la actividad', 'declaracion_name'=>'Importados', 'bill_iva_code'=>'B090', 'open_codes'=>'B090,S090,099'],
          ['grupo'=>'CNS','nombre'=>'Compras locales de bienes y servicios no sujetos', 'declaracion_name'=>'Locales', 'bill_iva_code'=>'B093', 'open_codes'=>'B080,S080,B091,B092,B093,B094'],
          ['grupo'=>'CNS','nombre'=>'Importaciones de bienes y servicios no sujetos', 'declaracion_name'=>'Importados', 'bill_iva_code'=>'B093', 'open_codes'=>'B080,S080,B091,B092,B093,B094'],
          ['grupo'=>'CLI','nombre'=>'Bienes y servicios del artículo 19 de la LIVA', 'bill_iva_code'=>'B080', 'open_codes'=>'B080,S080'],
          ['grupo'=>'COE','nombre'=>'Autorizadas por la Dirección General de Hacienda', 'bill_iva_code'=>'B080', 'open_codes'=>'B080,S080'],
          ['grupo'=>'COE','nombre'=>'Autorizadas por la Dirección General de Tributación', 'bill_iva_code'=>'B080', 'open_codes'=>'B080,S080'],
          ['grupo'=>'COE','nombre'=>'Autorizadas por Ley especial', 'bill_iva_code'=>'B080', 'open_codes'=>'B080,S080']
        ];
      
        foreach( $listaVentas as $categoria ) {
          try{
            App\ProductCategory::updateOrCreate(
            [ 
              'name' => $categoria['nombre'], 
              'group' => $categoria['grupo'] ],
            [
              'group' => $categoria['grupo'],
              'name' => $categoria['nombre'],
              'declaracion_name' => $categoria['declaracion_name'] ?? $categoria['nombre'],
              'bill_iva_code' => $categoria['bill_iva_code'] ?? null,
              'invoice_iva_code' => $categoria['invoice_iva_code'] ?? null,
              'open_codes' => $categoria['open_codes'],
            ]);
          }catch(\Throwable $e){}
        }
        
        foreach( $listaCompras as $categoria ) {
          try{
            App\ProductCategory::updateOrCreate(
            [ 'name' => $categoria['nombre'], 'group' => $categoria['grupo'] ],
            [
              'group' => $categoria['grupo'],
              'name' => $categoria['nombre'],
              'declaracion_name' => $categoria['nombre_declaracion'] ?? $categoria['nombre'],
              'bill_iva_code' => $categoria['bill_iva_code'] ?? null,
              'invoice_iva_code' => $categoria['invoice_iva_code'] ?? null,
              'open_codes' => $categoria['open_codes'],
            ]);
          }catch(\Throwable $e){}
        }
    }
}
