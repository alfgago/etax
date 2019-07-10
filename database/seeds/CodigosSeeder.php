<?php

use Illuminate\Database\Seeder;

class CodigosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->runSoportados();
        $this->runRepercutidos(); 
    }
    
    public function runSoportados() {
        $lista = [
          //Sin identificación específica
          ['nombre'=>'B001 - Compras locales de bienes con IVA al 1% sin identificación específica', 'codigo'=>'B001', 'porcentaje'=>'1', 'is_bienes'=>true],
          ['nombre'=>'B002 - Compras locales de bienes con IVA al 2% sin identificación específica', 'codigo'=>'B002', 'porcentaje'=>'2', 'is_bienes'=>true],
          ['nombre'=>'B003 - Compras locales de bienes con IVA al 13% sin identificación específica ', 'codigo'=>'B003', 'porcentaje'=>'13', 'is_bienes'=>true],
          ['nombre'=>'B004 - Compras locales de bienes con IVA al 4% sin identificación específica', 'codigo'=>'B004', 'porcentaje'=>'4', 'is_bienes'=>true],
          
          ['nombre'=>'B011 - Compras locales de propiedad planta y equipo con IVA al 1% sin identificación específica', 'codigo'=>'B011', 'porcentaje'=>'1', 'is_servicio'=>true],
          ['nombre'=>'B012 - Compras locales de propiedad planta y equipo con IVA al 2% sin identificación específica', 'codigo'=>'B012', 'porcentaje'=>'2', 'is_servicio'=>true],
          ['nombre'=>'B013 - Compras locales de propiedad planta y equipo con IVA al 13% sin identificación específica', 'codigo'=>'B013', 'porcentaje'=>'13', 'is_servicio'=>true],
          ['nombre'=>'B014 - Compras locales de propiedad planta y equipo con IVA al 4% sin identificación específica', 'codigo'=>'B014', 'porcentaje'=>'4', 'is_servicio'=>true],
          ['nombre'=>'B015 - Compras locales de propiedad planta y equipo con IVA al 1% sin identificación específica. Con costo superior a 15 salarios base', 'codigo'=>'B015', 'porcentaje'=>'1', 'is_servicio'=>true],
          ['nombre'=>'B016 - Compras locales de propiedad planta y equipo con IVA al 13% sin identificación específica. Con costo superior a 15 salarios base', 'codigo'=>'B016', 'porcentaje'=>'13', 'is_servicio'=>true],
          
          ['nombre'=>'B021 - Importaciones de bienes con IVA al 1% sin identificación específica', 'codigo'=>'B021', 'porcentaje'=>'1', 'is_bienes'=>true, 'is_importacion'=>true],
          ['nombre'=>'B022 - Importaciones de bienes con IVA al 2% sin identificación específica', 'codigo'=>'B022', 'porcentaje'=>'2', 'is_bienes'=>true, 'is_importacion'=>true],
          ['nombre'=>'B023 - Importaciones de bienes con IVA al 13% sin identificación específica', 'codigo'=>'B023', 'porcentaje'=>'13', 'is_bienes'=>true, 'is_importacion'=>true],
          ['nombre'=>'B024 - Importaciones de bienes con IVA al 4% sin identificación específica', 'codigo'=>'B024', 'porcentaje'=>'4', 'is_bienes'=>true, 'is_importacion'=>true],
          
          ['nombre'=>'B031 - Importaciones de propiedad planta y equipo con IVA al 1% sin identificación específica', 'codigo'=>'B031', 'porcentaje'=>'1', 'is_servicio'=>true, 'is_importacion'=>true],
          ['nombre'=>'B032 - Importaciones de propiedad planta y equipo con IVA al 2% sin identificación específica', 'codigo'=>'B032', 'porcentaje'=>'2', 'is_servicio'=>true, 'is_importacion'=>true],
          ['nombre'=>'B033 - Importaciones de propiedad planta y equipo con IVA al 13% sin identificación específica', 'codigo'=>'B033', 'porcentaje'=>'13', 'is_servicio'=>true, 'is_importacion'=>true],
          ['nombre'=>'B034 - Importaciones de propiedad planta y equipo con IVA al 4% sin identificación específica', 'codigo'=>'B034', 'porcentaje'=>'4', 'is_servicio'=>true, 'is_importacion'=>true],
          ['nombre'=>'B035 - Importaciones de propiedad planta y equipo con IVA al 1% sin identificación específica Con costo superior a 15 salarios base', 'codigo'=>'B035', 'porcentaje'=>'1', 'is_servicio'=>true, 'is_importacion'=>true],
          ['nombre'=>'B036 - Importaciones de propiedad planta y equipo con IVA al 13% sin identificación específica Con costo superior a 15 salarios base', 'codigo'=>'B036', 'porcentaje'=>'13', 'is_servicio'=>true, 'is_importacion'=>true],
          
          //Con identificación específica
          ['nombre'=>'B040 - Importaciones de bienes exentos', 'codigo'=>'B040', 'porcentaje'=>'0', 'is_bienes'=>true, 'is_importacion'=>true],
          ['nombre'=>'B041 - Importaciones con IVA al 1% de bienes de acreditación plena con identificación específica', 'codigo'=>'B041', 'porcentaje'=>'1', 'is_bienes'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'B042 - Importaciones con IVA al 2% de bienes de acreditación plena con identificación específica', 'codigo'=>'B042', 'porcentaje'=>'2', 'is_bienes'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'B043 - Importaciones con IVA al 13% de bienes de acreditación plena con identificación específica', 'codigo'=>'B043', 'porcentaje'=>'13', 'is_bienes'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'B044 - Importaciones con IVA al 4% de bienes de acreditación plena con identificación específica', 'codigo'=>'B044', 'porcentaje'=>'4', 'is_bienes'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
          
          ['nombre'=>'B050 - Importaciones de propiedad planta y equipo exentos', 'codigo'=>'B050', 'porcentaje'=>'0', 'is_servicio'=>true, 'is_importacion'=>true],
          ['nombre'=>'B051 - Importaciones con IVA al 1% de propiedad planta y equipo de acreditación plena con identificación específica', 'codigo'=>'B051', 'porcentaje'=>'1', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'B052 - Importaciones con IVA al 2% de propiedad planta y equipo de acreditación plena con identificación específica', 'codigo'=>'B052', 'porcentaje'=>'2', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'B053 - Importaciones con IVA al 13% de propiedad planta y equipo de acreditación plena con identificación específica', 'codigo'=>'B053', 'porcentaje'=>'13', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'B054 - Importaciones con IVA al 4% de propiedad planta y equipo de acreditación plena con identificación específica', 'codigo'=>'B054', 'porcentaje'=>'4', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
          
          ['nombre'=>'B060 - Compras locales de bienes', 'codigo'=>'B060', 'porcentaje'=>'0', 'is_bienes'=>true],
          ['nombre'=>'B061 - Compras locales con IVA al 1% de bienes de acreditación plena con identificación específica', 'codigo'=>'B061', 'porcentaje'=>'1', 'is_bienes'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'B062 - Compras locales con IVA al 2% de bienes de acreditación plena con identificación específica', 'codigo'=>'B062', 'porcentaje'=>'2', 'is_bienes'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'B063 - Compras locales con IVA al 13% de bienes de acreditación plena con identificación específica', 'codigo'=>'B063', 'porcentaje'=>'13', 'is_bienes'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'B064 - Compras locales con IVA al 4% de bienes de acreditación plena con identificación específica', 'codigo'=>'B064', 'porcentaje'=>'4', 'is_bienes'=>true, 'is_identificacion_plena'=>true],
          
          ['nombre'=>'B070 - Compras locales de  propiedad planta y equipo exentos.', 'codigo'=>'B070', 'porcentaje'=>'0', 'is_servicio'=>true, 'is_importacion'=>true],
          ['nombre'=>'B071 - Compras locales con IVA al 1% de propiedad planta y equipo de acreditación plena con identificación específica.', 'codigo'=>'B071', 'porcentaje'=>'1', 'is_servicio'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'B072 - Compras locales con IVA al 2% de propiedad planta y equipo de acreditación plena con identificación específica.', 'codigo'=>'B072', 'porcentaje'=>'2', 'is_servicio'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'B073 - Compras locales con IVA al 13% de propiedad planta y equipo de acreditación plena con identificación específica.', 'codigo'=>'B073', 'porcentaje'=>'13', 'is_servicio'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'B074 - Compras locales con IVA al 4% de propiedad planta y equipo de acreditación plena con identificación específica.', 'codigo'=>'B074', 'porcentaje'=>'4', 'is_servicio'=>true, 'is_identificacion_plena'=>true],
          
          //No acreditables
          ['nombre'=>'B080 - Compras de bienes con IVA no acreditable desde origen', 'codigo'=>'B080', 'porcentaje'=>'0', 'is_bienes'=>true, 'is_gravado'=>false],
          ['nombre'=>'B090 - Importaciones de bienes con IVA no acreditable desde origen', 'codigo'=>'B090', 'porcentaje'=>'0', 'is_bienes'=>true, 'is_gravado'=>false],
          ['nombre'=>'B097 - Compras de bienes con IVA no acreditable por gastos no deducibles', 'codigo'=>'B097', 'porcentaje'=>'0', 'is_bienes'=>true, 'is_gravado'=>false],
          
          //Sin identificación específica
          ['nombre'=>'S001 - Compras locales de servicios con IVA al 1% sin identificación específica', 'codigo'=>'S001', 'porcentaje'=>'1', 'is_servicio'=>true],
          ['nombre'=>'S002 - Compras locales de servicios con IVA al 2% sin identificación específica', 'codigo'=>'S002', 'porcentaje'=>'2', 'is_servicio'=>true],
          ['nombre'=>'S003 - Compras locales de servicios con IVA al 13% sin identificación específica ', 'codigo'=>'S003', 'porcentaje'=>'13', 'is_servicio'=>true],
          ['nombre'=>'S004 - Compras locales de servicios con IVA al 4% sin identificación específica', 'codigo'=>'S004', 'porcentaje'=>'4', 'is_servicio'=>true],
          
          ['nombre'=>'S021 - Importaciones de servicios con IVA al 1% sin identificación específica', 'codigo'=>'S021', 'porcentaje'=>'1', 'is_servicio'=>true, 'is_importacion'=>true],
          ['nombre'=>'S022 - Importaciones de servicios con IVA al 2% sin identificación específica', 'codigo'=>'S022', 'porcentaje'=>'2', 'is_servicio'=>true, 'is_importacion'=>true],
          ['nombre'=>'S023 - Importaciones de servicios con IVA al 13% sin identificación específica', 'codigo'=>'S023', 'porcentaje'=>'13', 'is_servicio'=>true, 'is_importacion'=>true],
          ['nombre'=>'S024 - Importaciones de servicios con IVA al 4% sin identificación específica', 'codigo'=>'S024', 'porcentaje'=>'4', 'is_servicio'=>true, 'is_importacion'=>true],

          //Con identificación específica
          ['nombre'=>'S040 - Importaciones de servicios exentos', 'codigo'=>'S040', 'porcentaje'=>'0', 'is_servicio'=>true, 'is_importacion'=>true],
          ['nombre'=>'S041 - Importaciones con IVA al 1% de servicios de acreditación plena con identificación específica', 'codigo'=>'S041', 'porcentaje'=>'1', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'S042 - Importaciones con IVA al 2% de servicios de acreditación plena con identificación específica', 'codigo'=>'S042', 'porcentaje'=>'2', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'S043 - Importaciones con IVA al 13% de servicios de acreditación plena con identificación específica', 'codigo'=>'S043', 'porcentaje'=>'13', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'S044 - Importaciones con IVA al 4% de servicios de acreditación plena con identificación específica', 'codigo'=>'S044', 'porcentaje'=>'4', 'is_servicio'=>true, 'is_importacion'=>true, 'is_identificacion_plena'=>true],
          
          ['nombre'=>'S060 - Compras locales de servicios exentos', 'codigo'=>'S060', 'porcentaje'=>'0', 'is_servicio'=>true],
          ['nombre'=>'S061 - Compras locales con IVA al 1% de servicios de acreditación plena con identificación específica', 'codigo'=>'S061', 'porcentaje'=>'1', 'is_servicio'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'S062 - Compras locales con IVA al 2% de servicios de acreditación plena con identificación específica', 'codigo'=>'S062', 'porcentaje'=>'2', 'is_servicio'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'S063 - Compras locales con IVA al 13% de servicios de acreditación plena con identificación específica', 'codigo'=>'S063', 'porcentaje'=>'13', 'is_servicio'=>true, 'is_identificacion_plena'=>true],
          ['nombre'=>'S064 - Compras locales con IVA al 4% de servicios de acreditación plena con identificación específica', 'codigo'=>'S064', 'porcentaje'=>'4', 'is_servicio'=>true, 'is_identificacion_plena'=>true],

          //No acreditables
          ['nombre'=>'S080 - Compras de servicios con IVA no acreditable desde origen', 'codigo'=>'S080', 'porcentaje'=>'0', 'is_servicio'=>true, 'is_gravado'=>false],
          ['nombre'=>'S090 - Importaciones de servicios con IVA no acreditable desde origen', 'codigo'=>'S090', 'porcentaje'=>'0', 'is_servicio'=>true, 'is_gravado'=>false],
          ['nombre'=>'S097 - Compras de servicios con IVA no acreditable por gastos no deducibles', 'codigo'=>'S097', 'porcentaje'=>'0', 'is_servicio'=>true, 'is_gravado'=>false],
          
          ['nombre'=>'098 - Inversion del sujeto pasivo base', 'codigo'=>'098', 'porcentaje'=>'0', 'is_gravado'=>false],
          ['nombre'=>'099 - Inversion del sujeto pasivo base no acreditable', 'codigo'=>'099', 'porcentaje'=>'0', 'is_gravado'=>false]
        ];
        
        foreach( $lista as $codigo ) {
          try{
          App\CodigoIvaSoportado::create([
              'id' => $codigo['codigo'],
              'code' => $codigo['codigo'],
              'name' => $codigo['nombre'],
              'bill_code' => 0,
              'percentage' => $codigo['porcentaje'],
              'hidden' => $codigo['hide'] ?? false,
              'is_bienes' => $codigo['is_bienes'] ?? false,
              'is_servicio' => $codigo['is_servicio'] ?? false,
              'is_capital' => $codigo['is_capital'] ?? false,
              'is_identificacion_plena' => $codigo['is_identificacion_plena'] ?? false,
              'is_gravado' => !isset($codigo['is_gravado']) ? true : $codigo['is_gravado']
          ]);
          }catch(\Throwable $e){
              \Illuminate\Support\Facades\Log::error('Error codigo seeder'. $e);
          }
        }
        
    }
    
    public function runRepercutidos() {
        
        $lista = [
          ['nombre'=>'B101 - Ventas locales de bienes con derecho a crédito al 1%', 'codigo'=>'B101', 'porcentaje'=>'1', 'codigo_tarifa' => '02', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B102 - Ventas locales de bienes con derecho a crédito al 2%', 'codigo'=>'B102', 'porcentaje'=>'2', 'codigo_tarifa' => '03', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B103 - Ventas locales de bienes con derecho a crédito al 13%', 'codigo'=>'B103', 'porcentaje'=>'13', 'codigo_tarifa' => '08', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B104 - Ventas locales de bienes con derecho a crédito al 4%', 'codigo'=>'B104', 'porcentaje'=>'4', 'codigo_tarifa' => '04', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B114 - Ventas locales de bienes con tarifa transitoria del 4% con derecho a crédito  *vigente del 1-07-2020 al 30-06-2021', 'codigo'=>'B114', 'porcentaje'=>'4', 'hide'=>true, 'hidden2018'=>true, 'codigo_tarifa' => '06', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B118 - Ventas locales de bienes con tarifa transitoria del 8% con derecho a crédito  *vigente del 1-07-2021 al 30-06-2022', 'codigo'=>'B118', 'porcentaje'=>'8', 'hide'=>true, 'hidden2018'=>true, 'codigo_tarifa' => '07', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B121 - Autoconsumo de bienes con derecho a crédito al 1%', 'codigo'=>'B121', 'porcentaje'=>'1', 'hidden2018'=>true, 'codigo_tarifa' => '02', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B122 - Autoconsumo de bienes con derecho a crédito al 2%', 'codigo'=>'B122', 'porcentaje'=>'2', 'hidden2018'=>true, 'codigo_tarifa' => '03', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B123 - Autoconsumo de bienes con derecho a crédito al 13%', 'codigo'=>'B123', 'porcentaje'=>'13', 'hidden2018'=>true, 'codigo_tarifa' => '08', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B124 - Autoconsumo de bienes con derecho a crédito al 4%', 'codigo'=>'B124', 'porcentaje'=>'4', 'hidden2018'=>true, 'codigo_tarifa' => '04', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B125 - Transferencia de bienes sin contraprestación de terceros al 1%', 'codigo'=>'B125', 'porcentaje'=>'1', 'hidden2018'=>true, 'codigo_tarifa' => '02', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B126 - Transferencia de bienes sin contraprestación de terceros al 2%', 'codigo'=>'B126', 'porcentaje'=>'2', 'hidden2018'=>true, 'codigo_tarifa' => '03', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B127 - Transferencia de bienes sin contraprestación de terceros al 13%', 'codigo'=>'B127', 'porcentaje'=>'13', 'hidden2018'=>true, 'codigo_tarifa' => '08', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B128 - Transferencia de bienes sin contraprestación de terceros al 4%', 'codigo'=>'B128', 'porcentaje'=>'4', 'hidden2018'=>true, 'codigo_tarifa' => '04', 'is_bienes'=>true, 'is_gravado'=>true],
          
          ['nombre'=>'B130 - Ventas de bienes con límites sobrepasados al 13% con derecho a crédito', 'codigo'=>'B130', 'porcentaje'=>'13', 'hidden2018'=>true, 'codigo_tarifa' => '08', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'S140 - Inversión del sujeto pasivo', 'codigo'=>'S140', 'porcentaje'=>'13', 'hidden2018'=>true, 'codigo_tarifa' => '08', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B150 - Ventas por exportación de bienes con derecho a crédito', 'codigo'=>'B150', 'porcentaje'=>'0', 'codigo_tarifa' => '01', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B155 - Ventas de bienes con derecho a crédito por ventas con IVA recaudado desde aduanas.', 'codigo'=>'B155', 'porcentaje'=>'0', 'codigo_tarifa' => '01', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B160 - Ventas de bienes al Estado e Instituciones con derecho a crédito al 0%', 'codigo'=>'B160', 'porcentaje'=>'0', 'codigo_tarifa' => '05', 'is_bienes'=>true, 'is_gravado'=>true, 'is_estado'=>true, 'hide'=>true, 'hidden2018'=>true],
          ['nombre'=>'B161 - Ventas de bienes al Estado e Instituciones con derecho a crédito al 1%', 'codigo'=>'B161', 'porcentaje'=>'0', 'codigo_tarifa' => '02', 'is_bienes'=>true, 'is_gravado'=>true, 'is_estado'=>true, 'hide'=>true, 'hidden2018'=>true],
          ['nombre'=>'B162 - Ventas de bienes al Estado e Instituciones con derecho a crédito al 2%', 'codigo'=>'B162', 'porcentaje'=>'0', 'codigo_tarifa' => '03', 'is_bienes'=>true, 'is_gravado'=>true, 'is_estado'=>true, 'hide'=>true, 'hidden2018'=>true],
          ['nombre'=>'B163 - Ventas de bienes al Estado e Instituciones con derecho a crédito al 13%', 'codigo'=>'B163', 'porcentaje'=>'0', 'codigo_tarifa' => '08', 'is_bienes'=>true, 'is_gravado'=>true, 'is_estado'=>true, 'hide'=>true, 'hidden2018'=>true],
          ['nombre'=>'B164 - Ventas de bienes al Estado e Instituciones con derecho a crédito al 4%', 'codigo'=>'B164', 'porcentaje'=>'0', 'codigo_tarifa' => '04', 'is_bienes'=>true, 'is_gravado'=>true, 'is_estado'=>true, 'hide'=>true, 'hidden2018'=>true],
          ['nombre'=>'B165 - Ventas de bienes de canasta básica con tarifa transitoria de 0% con acreditación plena', 'codigo'=>'B165', 'porcentaje'=>'0', 'codigo_tarifa' => '05', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B170 - Ventas de bienes a no sujetos y exentos con derecho a crédito', 'codigo'=>'B170', 'porcentaje'=>'0', 'codigo_tarifa' => '01', 'is_bienes'=>true, 'is_gravado'=>true],
          ['nombre'=>'B171 - Ventas locales de bienes de capital al 1%', 'codigo'=>'B171', 'porcentaje'=>'1', 'codigo_tarifa' => '02', 'is_bienes'=>true, 'is_gravado'=>true, 'is_servicio'=>true],
          ['nombre'=>'B172 - Ventas locales de bienes de capital al 2%', 'codigo'=>'B172', 'porcentaje'=>'2', 'codigo_tarifa' => '03', 'is_bienes'=>true, 'is_gravado'=>true, 'is_servicio'=>true],
          ['nombre'=>'B173 - Ventas locales de bienes de capital al 13%', 'codigo'=>'B173', 'porcentaje'=>'13', 'codigo_tarifa' => '08', 'is_bienes'=>true, 'is_gravado'=>true, 'is_servicio'=>true],
          ['nombre'=>'B174 - Ventas locales de bienes de capital al 4%', 'codigo'=>'B174', 'porcentaje'=>'4', 'codigo_tarifa' => '04', 'is_bienes'=>true, 'is_gravado'=>true, 'is_servicio'=>true],
          
          ['nombre'=>'B200 - Ventas de bienes sin derecho a crédito por exenciones objetivas', 'codigo'=>'B200', 'porcentaje'=>'0', 'codigo_tarifa' => '01', 'is_bienes'=>true, 'is_gravado'=>false],
          ['nombre'=>'B201 - Ventas de bienes sin derecho a crédito por exenciones objetivas con límite no sobrepasado', 'codigo'=>'B201', 'porcentaje'=>'0', 'codigo_tarifa' => '01', 'is_bienes'=>true, 'is_gravado'=>false],
          ['nombre'=>'B240 - Autoconsumo de bienes sin derecho a crédito', 'codigo'=>'B240', 'porcentaje'=>'0', 'hidden2018'=>true, 'codigo_tarifa' => '01', 'is_bienes'=>true, 'is_gravado'=>false],
          ['nombre'=>'B245 - Ventas locales de bienes con tarifa transitoria del 0% sin derecho a crédito', 'codigo'=>'B245', 'porcentaje'=>'0', 'codigo_tarifa' => '01', 'is_bienes'=>true, 'is_gravado'=>false], //*vigente del 1-07-2019 al 30-06-2020
          ['nombre'=>'B250 - Ventas de bienes con IVA incluido en el precio', 'codigo'=>'B250', 'porcentaje'=>'0', 'hide'=>true, 'hidden2018'=>true, 'codigo_tarifa' => '01', 'is_bienes'=>true, 'is_gravado'=>false],
          ['nombre'=>'B260 - Ventas de bienes sin derecho a crédito por ventas al Estado', 'codigo'=>'B260', 'porcentaje'=>'0', 'codigo_tarifa' => '01', 'is_bienes'=>true, 'is_gravado'=>false],
          
          ['nombre'=>'S101 - Ventas locales de servicios con derecho a crédito al 1%', 'codigo'=>'S101', 'porcentaje'=>'1', 'codigo_tarifa' => '02', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S102 - Ventas locales de servicios con derecho a crédito al 2%', 'codigo'=>'S102', 'porcentaje'=>'2', 'codigo_tarifa' => '03', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S103 - Ventas locales de servicios con derecho a crédito al 13%', 'codigo'=>'S103', 'porcentaje'=>'13', 'codigo_tarifa' => '08', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S104 - Ventas locales de servicios con derecho a crédito al 4%', 'codigo'=>'S104', 'porcentaje'=>'4', 'codigo_tarifa' => '04', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S114 - Ventas locales de servicios con tarifa transitoria del 4% con derecho a crédito  *vigente del 1-07-2020 al 30-06-2021', 'codigo'=>'S114', 'porcentaje'=>'4', 'hide'=>true, 'hidden2018'=>true, 'codigo_tarifa' => '06', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S118 - Ventas locales de servicios con tarifa transitoria del 8% con derecho a crédito  *vigente del 1-07-2021 al 30-06-2022', 'codigo'=>'S118', 'porcentaje'=>'8', 'hide'=>true, 'hidden2018'=>true, 'codigo_tarifa' => '07', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S121 - Autoconsumo de servicios con derecho a crédito al 1%', 'codigo'=>'S121', 'porcentaje'=>'1', 'hidden2018'=>true, 'codigo_tarifa' => '02', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S122 - Autoconsumo de servicios con derecho a crédito al 2%', 'codigo'=>'S122', 'porcentaje'=>'2', 'hidden2018'=>true, 'codigo_tarifa' => '03', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S123 - Autoconsumo de servicios con derecho a crédito al 13%', 'codigo'=>'S123', 'porcentaje'=>'13', 'hidden2018'=>true, 'codigo_tarifa' => '08', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S124 - Autoconsumo de servicios con derecho a crédito al 4%', 'codigo'=>'S124', 'porcentaje'=>'4', 'hidden2018'=>true, 'codigo_tarifa' => '04', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S125 - Transferencia de servicios sin contraprestación de terceros al 1%', 'codigo'=>'S125', 'porcentaje'=>'1', 'hidden2018'=>true, 'codigo_tarifa' => '02', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S126 - Transferencia de servicios sin contraprestación de terceros al 2%', 'codigo'=>'S126', 'porcentaje'=>'2', 'hidden2018'=>true, 'codigo_tarifa' => '03', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S127 - Transferencia de servicios sin contraprestación de terceros al 13%', 'codigo'=>'S127', 'porcentaje'=>'13', 'hidden2018'=>true, 'codigo_tarifa' => '08', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S128 - Transferencia de servicios sin contraprestación de terceros al 4%', 'codigo'=>'S128', 'porcentaje'=>'4', 'hidden2018'=>true, 'codigo_tarifa' => '04', 'is_servicio'=>true, 'is_gravado'=>true],
          
          ['nombre'=>'S130 - Ventas de servicios con límites sobrepasados al 13% con derecho a crédito', 'codigo'=>'S130', 'porcentaje'=>'13', 'hidden2018'=>true, 'codigo_tarifa' => '08', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S150 - Ventas por exportación de servicios con derecho a crédito', 'codigo'=>'S150', 'porcentaje'=>'0', 'codigo_tarifa' => '01', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S155 - Ventas de servicios con derecho a crédito por ventas con IVA recaudado desde aduanas.', 'codigo'=>'S155', 'porcentaje'=>'0', 'codigo_tarifa' => '01', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S160 - Ventas de servicios al Estado e Instituciones con derecho a crédito al 0%', 'codigo'=>'S160', 'porcentaje'=>'0', 'codigo_tarifa' => '05', 'is_servicio'=>true, 'is_gravado'=>true, 'is_estado'=>true, 'hide'=>true, 'hidden2018'=>true],
          ['nombre'=>'S161 - Ventas de servicios al Estado e Instituciones con derecho a crédito al 1%', 'codigo'=>'S161', 'porcentaje'=>'0', 'codigo_tarifa' => '02', 'is_servicio'=>true, 'is_gravado'=>true, 'is_estado'=>true, 'hide'=>true, 'hidden2018'=>true],
          ['nombre'=>'S162 - Ventas de servicios al Estado e Instituciones con derecho a crédito al 2%', 'codigo'=>'S162', 'porcentaje'=>'0', 'codigo_tarifa' => '03', 'is_servicio'=>true, 'is_gravado'=>true, 'is_estado'=>true, 'hide'=>true, 'hidden2018'=>true],
          ['nombre'=>'S163 - Ventas de servicios al Estado e Instituciones con derecho a crédito al 13%', 'codigo'=>'S163', 'porcentaje'=>'0', 'codigo_tarifa' => '08', 'is_servicio'=>true, 'is_gravado'=>true, 'is_estado'=>true, 'hide'=>true, 'hidden2018'=>true],
          ['nombre'=>'S164 - Ventas de servicios al Estado e Instituciones con derecho a crédito al 4%', 'codigo'=>'S164', 'porcentaje'=>'0', 'codigo_tarifa' => '04', 'is_servicio'=>true, 'is_gravado'=>true, 'is_estado'=>true, 'hide'=>true, 'hidden2018'=>true],
          ['nombre'=>'S165 - Ventas de servicios de canasta básica con tarifa transitoria de 0% con acreditación plena', 'codigo'=>'S165', 'porcentaje'=>'0', 'codigo_tarifa' => '05', 'is_servicio'=>true, 'is_gravado'=>true],
          ['nombre'=>'S170 - Ventas de servicios a no sujetos y exentos con derecho a crédito', 'codigo'=>'S170', 'porcentaje'=>'0', 'codigo_tarifa' => '01', 'is_servicio'=>true, 'is_gravado'=>true],
          
          ['nombre'=>'S200 - Ventas de servicios sin derecho a crédito por exenciones objetivas', 'codigo'=>'S200', 'porcentaje'=>'0', 'codigo_tarifa' => '01', 'is_servicio'=>true, 'is_gravado'=>false],
          ['nombre'=>'S201 - Ventas de servicios sin derecho a crédito por exenciones objetivas con límite no sobrepasado', 'codigo'=>'S201', 'porcentaje'=>'0', 'codigo_tarifa' => '01', 'is_servicio'=>true, 'is_gravado'=>false],
          ['nombre'=>'S240 - Autoconsumo de servicios sin derecho a crédito', 'codigo'=>'S240', 'porcentaje'=>'0', 'hidden2018'=>true, 'codigo_tarifa' => '01', 'is_servicio'=>true, 'is_gravado'=>false],
          ['nombre'=>'S245 - Ventas locales de servicios con tarifa transitoria del 0% sin derecho a crédito', 'codigo'=>'S245', 'porcentaje'=>'0', 'codigo_tarifa' => '01', 'is_servicio'=>true, 'is_gravado'=>false], //*vigente del 1-07-2019 al 30-06-2020
          ['nombre'=>'S250 - Ventas de servicios con IVA incluido en el precio', 'codigo'=>'S250', 'porcentaje'=>'0', 'hide'=>true, 'hidden2018'=>true, 'codigo_tarifa' => '01', 'is_servicio'=>true, 'is_gravado'=>false],
          ['nombre'=>'S260 - Ventas de servicios sin derecho a crédito por ventas al Estado', 'codigo'=>'S260', 'porcentaje'=>'0', 'codigo_tarifa' => '01', 'is_servicio'=>true, 'is_gravado'=>false]
        ];
        
        foreach( $lista as $codigo ) {
          try{
          App\CodigoIvaRepercutido::create([
              'id' => $codigo['codigo'],
              'code' => $codigo['codigo'],
              'name' => $codigo['nombre'],
              'invoice_code' => $codigo['codigo_tarifa'],
              'percentage' => $codigo['porcentaje'],
              'hidden' => $codigo['hide'] ?? false,
              'hidden2018' => $codigo['hidden2018'] ?? false,
              'is_estado' => $codigo['is_estado'] ?? false,
              'is_bienes' => $codigo['is_bienes'] ?? false,
              'is_servicio' => $codigo['is_servicio'] ?? false,
              'is_gravado' => $codigo['is_gravado'] ?? false
          ]);
          }catch(\Throwable $e){
              \Illuminate\Support\Facades\Log::error('Error codigo seeder'. $e);
          }
        }
        
    }
}
