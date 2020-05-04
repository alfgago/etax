<?php

namespace App;
use Illuminate\Support\Facades\Log;

class Variables
{
  
  public static function tiposIVARepercutidos() {
    $lista = [
      ['nombre'=>'101 - Ventas locales de bienes y servicios con derecho a crédito al 1%', 'codigo'=>'101', 'porcentaje'=>'1', 'codigo_tarifa' => '02'],
      ['nombre'=>'102 - Ventas locales de bienes y servicios con derecho a crédito al 2%', 'codigo'=>'102', 'porcentaje'=>'2', 'codigo_tarifa' => '03'],
      ['nombre'=>'103 - Ventas locales de bienes y servicios con derecho a crédito al 13%', 'codigo'=>'103', 'porcentaje'=>'13', 'codigo_tarifa' => '08'],
      ['nombre'=>'104 - Ventas locales de bienes y servicios con derecho a crédito al 4%', 'codigo'=>'104', 'porcentaje'=>'4', 'codigo_tarifa' => '04'],
      
      ['nombre'=>'114 - Ventas locales con tarifa transitoria del 4% con derecho a crédito  *vigente del 1-07-2020 al 30-06-2021', 'codigo'=>'114', 'porcentaje'=>'4', 'hide'=>true, 'hideMasiva'=>true, 'codigo_tarifa' => '06'],
      ['nombre'=>'118 - Ventas locales con tarifa transitoria del 8% con derecho a crédito  *vigente del 1-07-2021 al 30-06-2022', 'codigo'=>'118', 'porcentaje'=>'8', 'hide'=>true, 'hideMasiva'=>true, 'codigo_tarifa' => '07'],
      
      ['nombre'=>'121 - Autoconsumo de bienes y servicios con derecho a crédito al 1%', 'codigo'=>'121', 'porcentaje'=>'1', 'hideMasiva'=>true, 'codigo_tarifa' => '02'],
      ['nombre'=>'122 - Autoconsumo de bienes y servicios con derecho a crédito al 2%', 'codigo'=>'122', 'porcentaje'=>'2', 'hideMasiva'=>true, 'codigo_tarifa' => '03'],
      ['nombre'=>'123 - Autoconsumo de bienes y servicios con derecho a crédito al 13%', 'codigo'=>'123', 'porcentaje'=>'13', 'hideMasiva'=>true, 'codigo_tarifa' => '08'],
      ['nombre'=>'124 - Autoconsumo de bienes y servicios con derecho a crédito al 4%', 'codigo'=>'124', 'porcentaje'=>'4', 'hideMasiva'=>true, 'codigo_tarifa' => '04'],
      
      ['nombre'=>'130 - Ventas de bienes y servicios con límites sobrepasados al 13% con derecho a crédito', 'codigo'=>'130', 'porcentaje'=>'13', 'hideMasiva'=>true, 'codigo_tarifa' => '08'],
      ['nombre'=>'140 - Inversion del sujeto pasivo', 'codigo'=>'140', 'porcentaje'=>'13', 'hideMasiva'=>true, 'codigo_tarifa' => '08'],
      ['nombre'=>'150 - Ventas por exportación con derecho a crédito', 'codigo'=>'150', 'porcentaje'=>'0', 'codigo_tarifa' => '01'],
      ['nombre'=>'155 - Ventas con derecho a crédito por ventas con IVA recaudado desde aduanas.', 'codigo'=>'155', 'porcentaje'=>'0', 'codigo_tarifa' => '01'],
      ['nombre'=>'160 - Ventas al Estado e Instituciones con derecho a crédito', 'codigo'=>'160', 'porcentaje'=>'0', 'codigo_tarifa' => '05'],
      ['nombre'=>'165 - Ventas de canasta básica con tarifa transitoria de 0% con acreditación plena', 'codigo'=>'165', 'porcentaje'=>'0', 'codigo_tarifa' => '05'],
      ['nombre'=>'170 - Ventas a no sujetos y exentos con derecho a crédito', 'codigo'=>'170', 'porcentaje'=>'0', 'codigo_tarifa' => '01'],
      ['nombre'=>'200 - Ventas sin derecho a crédito por exenciones objetivas', 'codigo'=>'200', 'porcentaje'=>'0', 'codigo_tarifa' => '01'],
      ['nombre'=>'201 - Ventas sin derecho a crédito por exenciones objetivas con límite no sobrepasado', 'codigo'=>'201', 'porcentaje'=>'0', 'codigo_tarifa' => '01'],
      ['nombre'=>'240 - Autoconsumo sin derecho a crédito', 'codigo'=>'240', 'porcentaje'=>'0', 'hideMasiva'=>true, 'codigo_tarifa' => '01'],
      ['nombre'=>'245 - Ventas locales con tarifa transitoria del 0% sin derecho a crédito', 'codigo'=>'245', 'porcentaje'=>'0', 'codigo_tarifa' => '01'], //*vigente del 1-07-2019 al 30-06-2020
      ['nombre'=>'250 - Ventas con IVA incluido en el precio', 'codigo'=>'250', 'porcentaje'=>'0', 'hide'=>true, 'hideMasiva'=>true, 'codigo_tarifa' => '01'],
      ['nombre'=>'260 - Ventas sin derecho a crédito por ventas al Estado', 'codigo'=>'260', 'porcentaje'=>'0', 'codigo_tarifa' => '01']
    ];

    return $lista;
  }
  
   public static function tiposRepercutidos() {
    $lista = [
      ['nombre'=>'Bienes generales', 'codigo_iva'=>'103'],
      ['nombre'=>'Exportaciones/Importaciones', 'codigo_iva'=>'150'],
      ['nombre'=>'Servicios profesionales', 'codigo_iva'=>'103'],
      ['nombre'=>'Autoconsumo de servicios', 'codigo_iva'=>'123'],
      ['nombre'=>'Canasta básica', 'codigo_iva'=>'165'],
      ['nombre'=>'Medicamentos', 'codigo_iva'=>'102'],
      ['nombre'=>'Turismo', 'codigo_iva'=>'200'],
      ['nombre'=>'Libros', 'codigo_iva'=>'200'],
      ['nombre'=>'Arrendamiento de más de 640mil colones', 'codigo_iva'=>'130'],
      ['nombre'=>'Arrendamiento de menos de 640mil colones', 'codigo_iva'=>'201'],
      ['nombre'=>'Energía residencial', 'codigo_iva'=>'200'],
      ['nombre'=>'Agua residencial', 'codigo_iva'=>'200'],
      ['nombre'=>'Sillas de ruedas y similares', 'codigo_iva'=>'200'],
      ['nombre'=>'Cuota de colegio profesional', 'codigo_iva'=>'200'],
      ['nombre'=>'Otros', 'codigo_iva'=>'103'],
    ];

    return $lista;
  }
  
  public static function tiposIVASoportados() {
    $lista = [
      //Sin identificación específica
      ['nombre'=>'001 - Compras locales de bienes y servicios con IVA al 1% sin identificación específica', 'codigo'=>'001', 'porcentaje'=>'1'],
      ['nombre'=>'002 - Compras locales de bienes y servicios con IVA al 2% sin identificación específica', 'codigo'=>'002', 'porcentaje'=>'2'],
      ['nombre'=>'003 - Compras locales de bienes y servicios con IVA al 13% sin identificación específica ', 'codigo'=>'003', 'porcentaje'=>'13'],
      ['nombre'=>'004 - Compras locales de bienes y servicios con IVA al 4% sin identificación específica', 'codigo'=>'004', 'porcentaje'=>'4'],
      
      ['nombre'=>'011 - Compras locales de propiedad planta y equipo con IVA al 1% sin identificación específica', 'codigo'=>'011', 'porcentaje'=>'1'],
      ['nombre'=>'012 - Compras locales de propiedad planta y equipo con IVA al 2% sin identificación específica', 'codigo'=>'012', 'porcentaje'=>'2'],
      ['nombre'=>'013 - Compras locales de propiedad planta y equipo con IVA al 13% sin identificación específica', 'codigo'=>'013', 'porcentaje'=>'13'],
      ['nombre'=>'014 - Compras locales de propiedad planta y equipo con IVA al 4% sin identificación específica', 'codigo'=>'014', 'porcentaje'=>'4'],
      ['nombre'=>'015 - Compras locales de propiedad planta y equipo con IVA al 1% sin identificación específica. Con costo superior a 15 salarios base', 'codigo'=>'015', 'porcentaje'=>'1'],
      ['nombre'=>'016 - Compras locales de propiedad planta y equipo con IVA al 13% sin identificación específica. Con costo superior a 15 salarios base', 'codigo'=>'016', 'porcentaje'=>'13'],
      
      ['nombre'=>'021 - Importaciones de bienes y servicios con IVA al 1% sin identificación específica', 'codigo'=>'021', 'porcentaje'=>'1'],
      ['nombre'=>'022 - Importaciones de bienes y servicios con IVA al 2% sin identificación específica', 'codigo'=>'022', 'porcentaje'=>'2'],
      ['nombre'=>'023 - Importaciones de bienes y servicios con IVA al 13% sin identificación específica', 'codigo'=>'023', 'porcentaje'=>'13'],
      ['nombre'=>'024 - Importaciones de bienes y servicios con IVA al 4% sin identificación específica', 'codigo'=>'024', 'porcentaje'=>'4'],
      
      ['nombre'=>'031 - Importaciones de propiedad planta y equipo con IVA al 1% sin identificación específica', 'codigo'=>'031', 'porcentaje'=>'1'],
      ['nombre'=>'032 - Importaciones de propiedad planta y equipo con IVA al 2% sin identificación específica', 'codigo'=>'032', 'porcentaje'=>'2'],
      ['nombre'=>'033 - Importaciones de propiedad planta y equipo con IVA al 13% sin identificación específica', 'codigo'=>'033', 'porcentaje'=>'13'],
      ['nombre'=>'034 - Importaciones de propiedad planta y equipo con IVA al 4% sin identificación específica', 'codigo'=>'034', 'porcentaje'=>'4'],
      ['nombre'=>'035 - Importaciones de propiedad planta y equipo con IVA al 1% sin identificación específica Con costo superior a 15 salarios base', 'codigo'=>'035', 'porcentaje'=>'1'],
      ['nombre'=>'036 - Importaciones de propiedad planta y equipo con IVA al 13% sin identificación específica Con costo superior a 15 salarios base', 'codigo'=>'036', 'porcentaje'=>'13'],
      
      //Con identificación específica
      ['nombre'=>'040 - Importaciones de bienes y servicios exentos', 'codigo'=>'040', 'porcentaje'=>'0'],
      ['nombre'=>'041 - Importaciones con IVA al 1% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'041', 'porcentaje'=>'1'],
      ['nombre'=>'042 - Importaciones con IVA al 2% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'042', 'porcentaje'=>'2'],
      ['nombre'=>'043 - Importaciones con IVA al 13% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'043', 'porcentaje'=>'13'],
      ['nombre'=>'044 - Importaciones con IVA al 4% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'044', 'porcentaje'=>'4'],
      
      ['nombre'=>'050 - Importaciones de propiedad planta y equipo exentos', 'codigo'=>'050', 'porcentaje'=>'0'],
      ['nombre'=>'051 - Importaciones con IVA al 1% de propiedad planta y equipo de acreditación plena con identificación específica', 'codigo'=>'051', 'porcentaje'=>'1'],
      ['nombre'=>'052 - Importaciones con IVA al 2% de propiedad planta y equipo de acreditación plena con identificación específica', 'codigo'=>'052', 'porcentaje'=>'2'],
      ['nombre'=>'053 - Importaciones con IVA al 13% de propiedad planta y equipo de acreditación plena con identificación específica', 'codigo'=>'053', 'porcentaje'=>'13'],
      ['nombre'=>'054 - Importaciones con IVA al 4% de propiedad planta y equipo de acreditación plena con identificación específica', 'codigo'=>'054', 'porcentaje'=>'4'],
      
      ['nombre'=>'060 - Compras locales de bienes y servicios exentos', 'codigo'=>'060', 'porcentaje'=>'0'],
      ['nombre'=>'061 - Compras locales con IVA al 1% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'061', 'porcentaje'=>'1'],
      ['nombre'=>'062 - Compras locales con IVA al 2% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'062', 'porcentaje'=>'2'],
      ['nombre'=>'063 - Compras locales con IVA al 13% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'063', 'porcentaje'=>'13'],
      ['nombre'=>'064 - Compras locales con IVA al 4% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'064', 'porcentaje'=>'4'],
      
      ['nombre'=>'070 - Compras locales de  propiedad planta y equipo exentos.', 'codigo'=>'070', 'porcentaje'=>'0'],
      ['nombre'=>'071 - Compras locales con IVA al 1% de propiedad planta y equipo de acreditación plena con identificación específica.', 'codigo'=>'071', 'porcentaje'=>'1'],
      ['nombre'=>'072 - Compras locales con IVA al 2% de propiedad planta y equipo de acreditación plena con identificación específica.', 'codigo'=>'072', 'porcentaje'=>'2'],
      ['nombre'=>'073 - Compras locales con IVA al 13% de propiedad planta y equipo de acreditación plena con identificación específica.', 'codigo'=>'073', 'porcentaje'=>'13'],
      ['nombre'=>'074 - Compras locales con IVA al 4% de propiedad planta y equipo de acreditación plena con identificación específica.', 'codigo'=>'074', 'porcentaje'=>'4'],
      
      //No acreditables
      ['nombre'=>'080 - Compras de bienes y servicios con IVA no acreditable desde origen', 'codigo'=>'080', 'porcentaje'=>'0'],
      ['nombre'=>'090 - Importaciones de bienes y servicios con IVA no acreditable desde origen', 'codigo'=>'090', 'porcentaje'=>'0'],
      ['nombre'=>'097 - Compras con IVA no acreditable por gastos no deducibles', 'codigo'=>'097', 'porcentaje'=>'0'],
      ['nombre'=>'098 - Inversion del sujeto pasivo base', 'codigo'=>'098', 'porcentaje'=>'0'],
      ['nombre'=>'099 - Inversion del sujeto pasivo base no acreditable', 'codigo'=>'099', 'porcentaje'=>'0']
    ];

    return $lista;
  }
  
   public static function tiposSoportados() {
    $lista = [
      ['nombre'=>'Bienes generales', 'codigo_iva'=>'003'],
      ['nombre'=>'Exportaciones/Importaciones', 'codigo_iva'=>'013'],
      ['nombre'=>'Servicios profesionales', 'codigo_iva'=>'003'],
      ['nombre'=>'Canasta básica', 'codigo_iva'=>'001'],
      ['nombre'=>'Servicios médicos', 'codigo_iva'=>'004'],
      ['nombre'=>'Medicamentos', 'codigo_iva'=>'002'],
      ['nombre'=>'Turismo', 'codigo_iva'=>'077'],
      ['nombre'=>'Libros', 'codigo_iva'=>'070'],
      ['nombre'=>'Arrendamiento de menos de 640mil colones', 'codigo_iva'=>'003'],
      ['nombre'=>'Arrendamiento de más de 640mil colones', 'codigo_iva'=>'070'],
      ['nombre'=>'Energía residencial', 'codigo_iva'=>'070'],
      ['nombre'=>'Agua residencial', 'codigo_iva'=>'070'],
      ['nombre'=>'Sillas de ruedas y similares', 'codigo_iva'=>'070'],
      ['nombre'=>'Cuota de colegio profesional', 'codigo_iva'=>'077'],
      ['nombre'=>'Otros', 'codigo_iva'=>'003']
    ];

    return $lista;
  }

  public static function getCodigoTarifaVentas( $codigo ) {
      $lista = Variables::tiposIVARepercutidos();

      foreach ($lista as $tipo) {
          if( $codigo == $tipo['codigo'] ){
              if($codigo > 200) {
                return null;
              }
              return $tipo['codigo_tarifa'];
          }
      }

      return "08";
  }
  
  public static function unidadesMedicion() {
    $lista = [
      ['codigo'=>'1', 'nombre'=>'Unidad'],
      ['codigo'=>'2', 'nombre'=>'Servicios Profesionales'],
      ['codigo'=>'3', 'nombre'=>'Metro'],
      ['codigo'=>'72', 'nombre'=>'Litro'],
      ['codigo'=>'4', 'nombre'=>'Kilogramo'],
      ['codigo'=>'67', 'nombre'=>'Hora'],
      ['codigo'=>'86', 'nombre'=>'Onzas'],
      ['codigo'=>'5', 'nombre'=>'Segundo'],
      ['codigo'=>'6', 'nombre'=>'Ampere'],
      ['codigo'=>'7', 'nombre'=>'Kelvin'],
      ['codigo'=>'8', 'nombre'=>'Mol'],
      ['codigo'=>'9', 'nombre'=>'Candela'],
      ['codigo'=>'10', 'nombre'=>'Metro cuadrado'],
      ['codigo'=>'11', 'nombre'=>'Metro cúbico'],
      ['codigo'=>'12', 'nombre'=>'Metro por segundo'],
      ['codigo'=>'13', 'nombre'=>'Metro por segundo cuadrado'],
      ['codigo'=>'14', 'nombre'=>'1 por metro'],
      ['codigo'=>'15', 'nombre'=>'Kilogramo por metro cúbico'],
      ['codigo'=>'16', 'nombre'=>'Ampere por metro cuadrado'],
      ['codigo'=>'17', 'nombre'=>'Ampere por metro'],
      ['codigo'=>'18', 'nombre'=>'Mol por metro cúbico'],
      ['codigo'=>'19', 'nombre'=>'Candela por metro cuadrado'],
      ['codigo'=>'20', 'nombre'=>'Índice de refracción'],
      ['codigo'=>'21', 'nombre'=>'Radián'],
      ['codigo'=>'22', 'nombre'=>'Estereorradián'],
      ['codigo'=>'23', 'nombre'=>'Hertz'],
      ['codigo'=>'24', 'nombre'=>'Newton'],
      ['codigo'=>'25', 'nombre'=>'Pascal'],
      ['codigo'=>'26', 'nombre'=>'Joule'],
      ['codigo'=>'27', 'nombre'=>'Watt'],
      ['codigo'=>'28', 'nombre'=>'Coulomb'],
      ['codigo'=>'29', 'nombre'=>'Volt'],
      ['codigo'=>'30', 'nombre'=>'Farad'],
      ['codigo'=>'31', 'nombre'=>'Ohm'],
      ['codigo'=>'32', 'nombre'=>'Siemens'],
      ['codigo'=>'33', 'nombre'=>'Weber'],
      ['codigo'=>'34', 'nombre'=>'Tesla'],
      ['codigo'=>'35', 'nombre'=>'Henry'],
      ['codigo'=>'36', 'nombre'=>'Grado Celsius'],
      ['codigo'=>'37', 'nombre'=>'Lumen'],
      ['codigo'=>'38', 'nombre'=>'Lux'],
      ['codigo'=>'39', 'nombre'=>'Becquerel'],
      ['codigo'=>'40', 'nombre'=>'Gray'],
      ['codigo'=>'41', 'nombre'=>'Sievert'],
      ['codigo'=>'42', 'nombre'=>'Katal'],
      ['codigo'=>'43', 'nombre'=>'Pascal segundo'],
      ['codigo'=>'44', 'nombre'=>'Newton metro'],
      ['codigo'=>'45', 'nombre'=>'Newton por metro'],
      ['codigo'=>'46', 'nombre'=>'Radián por segundo'],
      ['codigo'=>'47', 'nombre'=>'Radián por segundo cuadrado'],
      ['codigo'=>'48', 'nombre'=>'Watt por metro cuadrado'],
      ['codigo'=>'49', 'nombre'=>'Joule por kelvin'],
      ['codigo'=>'50', 'nombre'=>'Joule por kilogramo kelvin'],
      ['codigo'=>'51', 'nombre'=>'Joule por kilogramo'],
      ['codigo'=>'52', 'nombre'=>'Watt por metro kevin'],
      ['codigo'=>'53', 'nombre'=>'Joule por metro cúbico'],
      ['codigo'=>'54', 'nombre'=>'Volt por metro'],
      ['codigo'=>'55', 'nombre'=>'Coulomb por metro cúbico'],
      ['codigo'=>'56', 'nombre'=>'Coulomb por metro cuadrado'],
      ['codigo'=>'57', 'nombre'=>'Farad por metro'],
      ['codigo'=>'58', 'nombre'=>'Henry por metro'],
      ['codigo'=>'59', 'nombre'=>'Joule por mol'],
      ['codigo'=>'60', 'nombre'=>'Joule por mol kelvin'],
      ['codigo'=>'61', 'nombre'=>'Coulomb por kilogramo'],
      ['codigo'=>'62', 'nombre'=>'Gray por segundo'],
      ['codigo'=>'63', 'nombre'=>'Watt por estereorradián'],
      ['codigo'=>'64', 'nombre'=>'Watt por metro cuadrado estereorradián'],
      ['codigo'=>'65', 'nombre'=>'Katal por metro cúbico'],
      ['codigo'=>'66', 'nombre'=>'Minuto'],
      ['codigo'=>'68', 'nombre'=>'Día'],
      ['codigo'=>'69', 'nombre'=>'Grado'],
      ['codigo'=>'70', 'nombre'=>'-Minuto'],
      ['codigo'=>'71', 'nombre'=>'-Segundo'],
      ['codigo'=>'73', 'nombre'=>'Tonelada'],
      ['codigo'=>'74', 'nombre'=>'Neper'],
      ['codigo'=>'75', 'nombre'=>'Bel'],
      ['codigo'=>'76', 'nombre'=>'Electronvolt'],
      ['codigo'=>'77', 'nombre'=>'Unidad de masa atómica unificada'],
      ['codigo'=>'78', 'nombre'=>'Unidad astronómica'],
      ['codigo'=>'79', 'nombre'=>'Galón'],
      ['codigo'=>'80', 'nombre'=>'Gramo'],
      ['codigo'=>'81', 'nombre'=>'Kilometro'],
      ['codigo'=>'82', 'nombre'=>'Pulgada'],
      ['codigo'=>'83', 'nombre'=>'Centímetro'],
      ['codigo'=>'84', 'nombre'=>'Mililitro'],
      ['codigo'=>'85', 'nombre'=>'Milímetro'],
      ['codigo'=>'87', 'nombre'=>'Otros']
    ];
    return $lista;
  }
  
  public static function getUnidadMedicionName($value){
    /*$lista = Variables::unidadesMedicion();
    
    foreach ($lista as $tipo) {
      if( $value == $tipo['codigo'] ){
        return $tipo['nombre'];
      }
    }*/
    
    $value = $value != '1' ? $value : 'Unid';
    $value = $value != '2' ? $value : 'Sp';
    
    if($value == 'Otros'){
      return $value;
    }

    $unid = UnidadMedicion::where('code', $value)->first();
    
    if( isset($unid) ){
      return $unid['name'];
    }
    
    return "Otros";
  }
  
  public static function getTipoSoportadoIVAName($codigo){
    $codigo = CodigoIvaSoportado::find($codigo);
    if($codigo) {
      return $codigo->name;
    }
    
    return "Otros";
  }
  
  public static function getTipoRepercutidoIVAName($codigo){
    $codigo = CodigoIvaRepercutido::find($codigo);
    if($codigo) {
      return $codigo->name;
    }
    
    return "Otros";
  }
  
  public static function getTipoSoportadoIVAPorc($codigo){
    $codigo = CodigoIvaSoportado::find($codigo);
    if($codigo) {
      return $codigo->percentage;
    }
    
    return "Otros";
  }
  
  public static function getTipoRepercutidoIVAPorc($codigo){
    $codigo = CodigoIvaRepercutido::find($codigo);
    if($codigo) {
      return $codigo->percentage;
    }
    
    return "Otros";
  }
  
  public static function getMonthName( $month ) {
      if( $month == 1 ){
        return "Enero";
      }else if( $month == 2 ){
        return "Febrero";
      }else if( $month == 3 ){
        return "Marzo";
      }else if( $month == 4 ){
        return "Abril";
      }else if( $month == 5 ){
        return "Mayo";
      }else if( $month == 6 ){
        return "Junio";
      }else if( $month == 7 ){
        return "Julio";
      }else if( $month == 8 ){
        return "Agosto";
      }else if( $month == 9 ){
        return "Setiembre";
      }else if( $month == 10 ){
        return "Octubre";
      }else if( $month == 11 ){
        return "Noviembre";
      }else if( $month == 12 ){
        return "Diciembre";
      }else if( $month == 0 ){
        return "Acumulado anual";
      }
  }
  
  public static function getMonthCode( $month ) {
      if( $month == 1 ){
        return "e";
      }else if( $month == 2 ){
        return "f";
      }else if( $month == 3 ){
        return "m";
      }else if( $month == 4 ){
        return "a";
      }else if( $month == 5 ){
        return "y";
      }else if( $month == 6 ){
        return "j";
      }else if( $month == 7 ){
        return "l";
      }else if( $month == 8 ){
        return "g";
      }else if( $month == 9 ){
        return "s";
      }else if( $month == 10 ){
        return "c";
      }else if( $month == 11 ){
        return "n";
      }else if( $month == 12 ){
        return "d";
      }else {
        return "e";
      }
  }  
  
  public static function getProvinciaFromID($provinciaId){
    $provincias='{"1":{"Nombre":"San José"},"2":{"Nombre":"Alajuela"},"3":{"Nombre":"Cartago"},"4":{"Nombre":"Heredia"},"5":{"Nombre":"Guancaste"},"6":{"Nombre":"Puntarenas"},"7":{"Nombre":"Limón"}}';
    $provincias = json_decode($provincias, true);
    return $provincias[$provinciaId]["Nombre"];
  }
  
  public static function getCantonFromID($cantonId){
    $cantones='{"101":{"Provincia":"1","Nombre":"San José","Área (km2)":"44.62","Pop. (2008)":"352366"},"102":{"Provincia":"1","Nombre":"Escazú","Área (km2)":"34.49","Pop. (2008)":"60201"},"103":{"Provincia":"1","Nombre":"Desamparados","Área (km2)":"118.26","Pop. (2008)":"221346"},"104":{"Provincia":"1","Nombre":"Puriscal","Área (km2)":"553.66","Pop. (2008)":"32767"},"105":{"Provincia":"1","Nombre":"Tarrazú","Área (km2)":"297.5","Pop. (2008)":"16419"},"106":{"Provincia":"1","Nombre":"Aserrí","Área (km2)":"167.1","Pop. (2008)":"56422"},"107":{"Provincia":"1","Nombre":"Mora","Área (km2)":"162.04","Pop. (2008)":"24333"},"108":{"Provincia":"1","Nombre":"Goicoechea","Área (km2)":"31.5","Pop. (2008)":"131529"},"109":{"Provincia":"1","Nombre":"Santa Ana","Área (km2)":"61.42","Pop. (2008)":"39905"},"110":{"Provincia":"1","Nombre":"Alajuelita","Área (km2)":"21.17","Pop. (2008)":"81721"},"111":{"Provincia":"1","Nombre":"Vázquez de Coronado","Área (km2)":"222.2","Pop. (2008)":"63098"},"112":{"Provincia":"1","Nombre":"Acosta","Área (km2)":"342.24","Pop. (2008)":"20906"},"113":{"Provincia":"1","Nombre":"Tibás","Área (km2)":"8.15","Pop. (2008)":"81478"},"114":{"Provincia":"1","Nombre":"Moravia","Área (km2)":"28.62","Pop. (2008)":"55895"},"115":{"Provincia":"1","Nombre":"Montes de Oca","Área (km2)":"15.16","Pop. (2008)":"55814"},"116":{"Provincia":"1","Nombre":"Turrubares","Área (km2)":"415.29","Pop. (2008)":"5482"},"117":{"Provincia":"1","Nombre":"Dota","Área (km2)":"400.22","Pop. (2008)":"7465"},"118":{"Provincia":"1","Nombre":"Curridabat","Área (km2)":"15.95","Pop. (2008)":"69474"},"119":{"Provincia":"1","Nombre":"Pérez Zeledón","Área (km2)":"1905.51","Pop. (2008)":"140872"},"120":{"Provincia":"1","Nombre":"León Cortés Castro","Área (km2)":"120.8","Pop. (2008)":"13288"},"201":{"Provincia":"2","Nombre":"Alajuela","Área (km2)":"388.43","Pop. (2008)":"255598"},"202":{"Provincia":"2","Nombre":"San Ramón","Área (km2)":"1018.64","Pop. (2008)":"77380"},"203":{"Provincia":"2","Nombre":"Grecia","Área (km2)":"395.72","Pop. (2008)":"74860"},"204":{"Provincia":"2","Nombre":"San Mateo","Área (km2)":"125.9","Pop. (2008)":"5904"},"205":{"Provincia":"2","Nombre":"Atenas","Área (km2)":"127.19","Pop. (2008)":"25033"},"206":{"Provincia":"2","Nombre":"Naranjo","Área (km2)":"126.62","Pop. (2008)":"42637"},"207":{"Provincia":"2","Nombre":"Palmares","Área (km2)":"38.06","Pop. (2008)":"33401"},"208":{"Provincia":"2","Nombre":"Poás","Área (km2)":"73.84","Pop. (2008)":"28469"},"209":{"Provincia":"2","Nombre":"Orotina","Área (km2)":"141.92","Pop. (2008)":"17866"},"210":{"Provincia":"2","Nombre":"San Carlos","Área (km2)":"3347.98","Pop. (2008)":"151322"},"211":{"Provincia":"2","Nombre":"Zarcero","Área (km2)":"155.13","Pop. (2008)":"12368"},"212":{"Provincia":"2","Nombre":"Valverde Vega","Área (km2)":"120.25","Pop. (2008)":"18407"},"213":{"Provincia":"2","Nombre":"Upala","Área (km2)":"1580.67","Pop. (2008)":"44556"},"214":{"Provincia":"2","Nombre":"Los Chiles","Área (km2)":"1358.86","Pop. (2008)":"23902"},"215":{"Provincia":"2","Nombre":"Guatuso","Área (km2)":"758.32","Pop. (2008)":"15068"},"301":{"Provincia":"3","Nombre":"Cartago","Área (km2)":"287.77","Pop. (2008)":"149657"},"302":{"Provincia":"3","Nombre":"Paraíso","Área (km2)":"411.91","Pop. (2008)":"60005"},"303":{"Provincia":"3","Nombre":"La Unión","Área (km2)":"44.83","Pop. (2008)":"91090"},"304":{"Provincia":"3","Nombre":"Jiménez","Área (km2)":"286.43","Pop. (2008)":"15859"},"305":{"Provincia":"3","Nombre":"Turrialba","Área (km2)":"1642.67","Pop. (2008)":"78217"},"306":{"Provincia":"3","Nombre":"Alvarado","Área (km2)":"81.06","Pop. (2008)":"13862"},"307":{"Provincia":"3","Nombre":"Oreamuno","Área (km2)":"202.31","Pop. (2008)":"44403"},"308":{"Provincia":"3","Nombre":"El Guarco","Área (km2)":"167.69","Pop. (2008)":"39223"},"401":{"Provincia":"4","Nombre":"Heredia","Área (km2)":"282.6","Pop. (2008)":"118872"},"402":{"Provincia":"4","Nombre":"Barva","Área (km2)":"53.8","Pop. (2008)":"37041"},"403":{"Provincia":"4","Nombre":"Santo Domingo","Área (km2)":"24.84","Pop. (2008)":"38959"},"404":{"Provincia":"4","Nombre":"Santa Bárbara","Área (km2)":"53.21","Pop. (2008)":"33334"},"405":{"Provincia":"4","Nombre":"San Rafael","Área (km2)":"48.39","Pop. (2008)":"42398"},"406":{"Provincia":"4","Nombre":"San Isidro","Área (km2)":"26.96","Pop. (2008)":"18028"},"407":{"Provincia":"4","Nombre":"Belén","Área (km2)":"12.15","Pop. (2008)":"22400"},"408":{"Provincia":"4","Nombre":"Flores","Área (km2)":"6.96","Pop. (2008)":"17298"},"409":{"Provincia":"4","Nombre":"San Pablo","Área (km2)":"7.53","Pop. (2008)":"23370"},"410":{"Provincia":"4","Nombre":"Sarapiquí","Área (km2)":"2140.54","Pop. (2008)":"54537"},"501":{"Provincia":"5","Nombre":"Liberia","Área (km2)":"1436.47","Pop. (2008)":"55921"},"502":{"Provincia":"5","Nombre":"Nicoya","Área (km2)":"1333.68","Pop. (2008)":"47823"},"503":{"Provincia":"5","Nombre":"Santa Cruz","Área (km2)":"1312.27","Pop. (2008)":"46460"},"504":{"Provincia":"5","Nombre":"Bagaces","Área (km2)":"1273.49","Pop. (2008)":"18368"},"505":{"Provincia":"5","Nombre":"Carrillo","Área (km2)":"577.54","Pop. (2008)":"32168"},"506":{"Provincia":"5","Nombre":"Cañas","Área (km2)":"682.2","Pop. (2008)":"27970"},"507":{"Provincia":"5","Nombre":"Abangares","Área (km2)":"675.76","Pop. (2008)":"18319"},"508":{"Provincia":"5","Nombre":"Tilarán","Área (km2)":"638.39","Pop. (2008)":"20337"},"509":{"Provincia":"5","Nombre":"Nandayure","Área (km2)":"565.59","Pop. (2008)":"11185"},"510":{"Provincia":"5","Nombre":"La Cruz","Área (km2)":"1383.9","Pop. (2008)":"19978"},"511":{"Provincia":"5","Nombre":"Hojancha","Área (km2)":"261.42","Pop. (2008)":"7289"},"601":{"Provincia":"6","Nombre":"Puntarenas","Área (km2)":"1842.33","Pop. (2008)":"118928"},"602":{"Provincia":"6","Nombre":"Esparza","Área (km2)":"216.8","Pop. (2008)":"27199"},"603":{"Provincia":"6","Nombre":"Buenos Aires","Área (km2)":"2384.22","Pop. (2008)":"47576"},"604":{"Provincia":"6","Nombre":"Montes de Oro","Área (km2)":"244.76","Pop. (2008)":"12495"},"605":{"Provincia":"6","Nombre":"Osa","Área (km2)":"1930.24","Pop. (2008)":"29547"},"606":{"Provincia":"6","Nombre":"Quepos","Área (km2)":"543.77","Pop. (2008)":"23915"},"607":{"Provincia":"6","Nombre":"Golfito","Área (km2)":"1753.96","Pop. (2008)":"39699"},"608":{"Provincia":"6","Nombre":"Coto Brus","Área (km2)":"933.91","Pop. (2008)":"47247"},"609":{"Provincia":"6","Nombre":"Parrita","Área (km2)":"478.79","Pop. (2008)":"13940"},"610":{"Provincia":"6","Nombre":"Corredores","Área (km2)":"620.6","Pop. (2008)":"44180"},"611":{"Provincia":"6","Nombre":"Garabito","Área (km2)":"316.31","Pop. (2008)":"13165"},"701":{"Provincia":"7","Nombre":"Limón","Área (km2)":"1765.79","Pop. (2008)":"106356"},"702":{"Provincia":"7","Nombre":"Pococí","Área (km2)":"2403.49","Pop. (2008)":"121735"},"703":{"Provincia":"7","Nombre":"Siquirres","Área (km2)":"860.19","Pop. (2008)":"60881"},"704":{"Provincia":"7","Nombre":"Talamanca","Área (km2)":"2809.93","Pop. (2008)":"32158"},"705":{"Provincia":"7","Nombre":"Matina","Área (km2)":"772.64","Pop. (2008)":"39961"},"706":{"Provincia":"7","Nombre":"Guácimo","Área (km2)":"576.48","Pop. (2008)":"41082"}}';
    $cantones = json_decode($cantones, true);
    return $cantones[$cantonId]["Nombre"];
  }
  
  public static function getDistritoFromID($distritoId){
    $distritos = '{"10101":{"Canton":"101","Nombre":"Carmen"},"10102":{"Canton":"101","Nombre":"Merced"},"10103":{"Canton":"101","Nombre":"Hospital"},"10104":{"Canton":"101","Nombre":"Catedral"},"10105":{"Canton":"101","Nombre":"Zapote"},"10106":{"Canton":"101","Nombre":"San Francisco de Dos Ríos"},"10107":{"Canton":"101","Nombre":"Uruca"},"10108":{"Canton":"101","Nombre":"Mata Redonda"},"10109":{"Canton":"101","Nombre":"Pavas"},"10110":{"Canton":"101","Nombre":"Hatillo"},"10111":{"Canton":"101","Nombre":"San Sebastián"},"10201":{"Canton":"102","Nombre":"Escazú"},"10202":{"Canton":"102","Nombre":"San Antonio"},"10203":{"Canton":"102","Nombre":"San Rafael"},"10301":{"Canton":"103","Nombre":"Desamparados"},"10302":{"Canton":"103","Nombre":"San Miguel"},"10303":{"Canton":"103","Nombre":"San Juan de Dios"},"10304":{"Canton":"103","Nombre":"San Rafael Arriba"},"10305":{"Canton":"103","Nombre":"San Antonio"},"10306":{"Canton":"103","Nombre":"Frailes"},"10307":{"Canton":"103","Nombre":"Patarrá"},"10308":{"Canton":"103","Nombre":"San Cristóbal"},"10309":{"Canton":"103","Nombre":"Rosario"},"10310":{"Canton":"103","Nombre":"Damas"},"10311":{"Canton":"103","Nombre":"San Rafael Abajo"},"10312":{"Canton":"103","Nombre":"Gravilias"},"10313":{"Canton":"103","Nombre":"Los Guido"},"10401":{"Canton":"104","Nombre":"Santiago"},"10402":{"Canton":"104","Nombre":"Mercedes Sur"},"10403":{"Canton":"104","Nombre":"Barbacoas"},"10404":{"Canton":"104","Nombre":"Grifo Alto"},"10405":{"Canton":"104","Nombre":"San Rafael"},"10406":{"Canton":"104","Nombre":"Candelarita"},"10407":{"Canton":"104","Nombre":"Desamparaditos"},"10408":{"Canton":"104","Nombre":"San Antonio"},"10409":{"Canton":"104","Nombre":"Chires"},"10501":{"Canton":"105","Nombre":"San Marcos"},"10502":{"Canton":"105","Nombre":"San Lorenzo"},"10503":{"Canton":"105","Nombre":"San Carlos"},"10601":{"Canton":"106","Nombre":"Aserrí"},"10602":{"Canton":"106","Nombre":"Tarbaca"},"10603":{"Canton":"106","Nombre":"Vuelta de Jorco"},"10604":{"Canton":"106","Nombre":"San Gabriel"},"10605":{"Canton":"106","Nombre":"Legua"},"10606":{"Canton":"106","Nombre":"Monterrey"},"10607":{"Canton":"106","Nombre":"Salitrillos"},"10701":{"Canton":"107","Nombre":"Colón"},"10702":{"Canton":"107","Nombre":"Guayabo"},"10703":{"Canton":"107","Nombre":"Tabarcia"},"10704":{"Canton":"107","Nombre":"Piedras Negras"},"10705":{"Canton":"107","Nombre":"Picagres"},"10706":{"Canton":"107","Nombre":"Jaris"},"10801":{"Canton":"108","Nombre":"Guadalupe"},"10802":{"Canton":"108","Nombre":"San Francisco"},"10803":{"Canton":"108","Nombre":"Calle Blancos"},"10804":{"Canton":"108","Nombre":"Mata de Plátano"},"10805":{"Canton":"108","Nombre":"Ipís"},"10806":{"Canton":"108","Nombre":"Rancho Redondo"},"10807":{"Canton":"108","Nombre":"Purral"},"10901":{"Canton":"109","Nombre":"Santa Ana"},"10902":{"Canton":"109","Nombre":"Salitral"},"10903":{"Canton":"109","Nombre":"Pozos"},"10904":{"Canton":"109","Nombre":"Uruca"},"10905":{"Canton":"109","Nombre":"Piedades"},"10906":{"Canton":"109","Nombre":"Brasil"},"11001":{"Canton":"110","Nombre":"Alajuelita"},"11002":{"Canton":"110","Nombre":"San Josecito"},"11003":{"Canton":"110","Nombre":"San Antonio"},"11004":{"Canton":"110","Nombre":"Concepción"},"11005":{"Canton":"110","Nombre":"San Felipe"},"11101":{"Canton":"111","Nombre":"San Isidro"},"11102":{"Canton":"111","Nombre":"San Rafael"},"11103":{"Canton":"111","Nombre":"Dulce Nombre de Jesús"},"11104":{"Canton":"111","Nombre":"Patalillo"},"11105":{"Canton":"111","Nombre":"Cascajal"},"11201":{"Canton":"112","Nombre":"San Ignacio"},"11202":{"Canton":"112","Nombre":"Guaitil"},"11203":{"Canton":"112","Nombre":"Palmichal"},"11204":{"Canton":"112","Nombre":"Cangrejal"},"11205":{"Canton":"112","Nombre":"Sabanillas"},"11301":{"Canton":"113","Nombre":"San Juan"},"11302":{"Canton":"113","Nombre":"Cinco Esquinas"},"11303":{"Canton":"113","Nombre":"Anselmo Llorente"},"11304":{"Canton":"113","Nombre":"León XIII"},"11305":{"Canton":"113","Nombre":"Colima"},"11401":{"Canton":"114","Nombre":"San Vicente"},"11402":{"Canton":"114","Nombre":"San Jerónimo"},"11403":{"Canton":"114","Nombre":"La Trinidad"},"11501":{"Canton":"115","Nombre":"San Pedro"},"11502":{"Canton":"115","Nombre":"Sabanilla"},"11503":{"Canton":"115","Nombre":"Mercedes"},"11504":{"Canton":"115","Nombre":"San Rafael"},"11601":{"Canton":"116","Nombre":"San Pablo"},"11602":{"Canton":"116","Nombre":"San Pedro"},"11603":{"Canton":"116","Nombre":"San Juan de Mata"},"11604":{"Canton":"116","Nombre":"San Luis"},"11605":{"Canton":"116","Nombre":"Carara"},"11701":{"Canton":"117","Nombre":"Santa María"},"11702":{"Canton":"117","Nombre":"Jardín"},"11703":{"Canton":"117","Nombre":"Copey"},"11801":{"Canton":"118","Nombre":"Curridabat"},"11802":{"Canton":"118","Nombre":"Granadilla"},"11803":{"Canton":"118","Nombre":"Sánchez"},"11804":{"Canton":"118","Nombre":"Tirrases"},"11901":{"Canton":"119","Nombre":"San Isidro de El General"},"11902":{"Canton":"119","Nombre":"General"},"11903":{"Canton":"119","Nombre":"Daniel Flores"},"11904":{"Canton":"119","Nombre":"Rivas"},"11905":{"Canton":"119","Nombre":"San Pedro"},"11906":{"Canton":"119","Nombre":"Platanares"},"11907":{"Canton":"119","Nombre":"Pejibaye"},"11908":{"Canton":"119","Nombre":"Cajón"},"11909":{"Canton":"119","Nombre":"Barú"},"11910":{"Canton":"119","Nombre":"Río Nuevo"},"11911":{"Canton":"119","Nombre":"Páramo"},"12001":{"Canton":"120","Nombre":"San Pablo"},"12002":{"Canton":"120","Nombre":"San Andrés"},"12003":{"Canton":"120","Nombre":"Llano Bonito"},"12004":{"Canton":"120","Nombre":"San Isidro"},"12005":{"Canton":"120","Nombre":"Santa Cruz"},"12006":{"Canton":"120","Nombre":"San Antonio"},"20101":{"Canton":"201","Nombre":"Alajuela"},"20102":{"Canton":"201","Nombre":"San José"},"20103":{"Canton":"201","Nombre":"Carrizal"},"20104":{"Canton":"201","Nombre":"San Antonio"},"20105":{"Canton":"201","Nombre":"Guácima"},"20106":{"Canton":"201","Nombre":"San Isidro"},"20107":{"Canton":"201","Nombre":"Sabanilla"},"20108":{"Canton":"201","Nombre":"San Rafael"},"20109":{"Canton":"201","Nombre":"Río Segundo"},"20110":{"Canton":"201","Nombre":"Desamparados"},"20111":{"Canton":"201","Nombre":"Turrúcares"},"20112":{"Canton":"201","Nombre":"Tambor"},"20113":{"Canton":"201","Nombre":"Garita"},"20114":{"Canton":"201","Nombre":"Sarapiquí"},"20201":{"Canton":"202","Nombre":"San Ramón"},"20202":{"Canton":"202","Nombre":"Santiago"},"20203":{"Canton":"202","Nombre":"San Juan"},"20204":{"Canton":"202","Nombre":"Piedades Norte"},"20205":{"Canton":"202","Nombre":"Piedades Sur"},"20206":{"Canton":"202","Nombre":"San Rafael"},"20207":{"Canton":"202","Nombre":"San Isidro"},"20208":{"Canton":"202","Nombre":"Los Ángeles"},"20209":{"Canton":"202","Nombre":"Alfaro"},"20210":{"Canton":"202","Nombre":"Volio"},"20211":{"Canton":"202","Nombre":"Concepción"},"20212":{"Canton":"202","Nombre":"Zapotal"},"20213":{"Canton":"202","Nombre":"Peñas Blancas"},"20301":{"Canton":"203","Nombre":"Grecia"},"20302":{"Canton":"203","Nombre":"San Isidro"},"20303":{"Canton":"203","Nombre":"San José"},"20304":{"Canton":"203","Nombre":"San Roque"},"20305":{"Canton":"203","Nombre":"Tacares"},"20306":{"Canton":"203","Nombre":"Río Cuarto"},"20307":{"Canton":"203","Nombre":"Puente de Piedra"},"20308":{"Canton":"203","Nombre":"Bolívar"},"20401":{"Canton":"204","Nombre":"San Mateo"},"20402":{"Canton":"204","Nombre":"Desmonte"},"20403":{"Canton":"204","Nombre":"Jesús María"},"20501":{"Canton":"205","Nombre":"Atenas"},"20502":{"Canton":"205","Nombre":"Jesús"},"20503":{"Canton":"205","Nombre":"Mercedes"},"20504":{"Canton":"205","Nombre":"San Isidro"},"20505":{"Canton":"205","Nombre":"Concepción"},"20506":{"Canton":"205","Nombre":"San José"},"20507":{"Canton":"205","Nombre":"Santa Eulalia"},"20508":{"Canton":"205","Nombre":"Escobal"},"20601":{"Canton":"206","Nombre":"Naranjo"},"20602":{"Canton":"206","Nombre":"San Miguel"},"20603":{"Canton":"206","Nombre":"San José"},"20604":{"Canton":"206","Nombre":"Cirrí Sur"},"20605":{"Canton":"206","Nombre":"San Jerónimo"},"20606":{"Canton":"206","Nombre":"San Juan"},"20607":{"Canton":"206","Nombre":"El Rosario"},"20608":{"Canton":"206","Nombre":"Palmitos"},"20701":{"Canton":"207","Nombre":"Palmares"},"20702":{"Canton":"207","Nombre":"Zaragoza"},"20703":{"Canton":"207","Nombre":"Buenos Aires"},"20704":{"Canton":"207","Nombre":"Santiago"},"20705":{"Canton":"207","Nombre":"Candelaria"},"20706":{"Canton":"207","Nombre":"Esquipulas"},"20707":{"Canton":"207","Nombre":"La Granja"},"20801":{"Canton":"208","Nombre":"San Pedro"},"20802":{"Canton":"208","Nombre":"San Juan"},"20803":{"Canton":"208","Nombre":"San Rafael"},"20804":{"Canton":"208","Nombre":"Carrillos"},"20805":{"Canton":"208","Nombre":"Sabana Redonda"},"20901":{"Canton":"209","Nombre":"Orotina"},"20902":{"Canton":"209","Nombre":"El Mastate"},"20903":{"Canton":"209","Nombre":"Hacienda Vieja"},"20904":{"Canton":"209","Nombre":"Coyolar"},"20905":{"Canton":"209","Nombre":"La Ceiba"},"21001":{"Canton":"210","Nombre":"Quesada"},"21002":{"Canton":"210","Nombre":"Florencia"},"21003":{"Canton":"210","Nombre":"Buenavista"},"21004":{"Canton":"210","Nombre":"Aguas Zarcas"},"21005":{"Canton":"210","Nombre":"Venecia"},"21006":{"Canton":"210","Nombre":"Pital"},"21007":{"Canton":"210","Nombre":"La Fortuna"},"21008":{"Canton":"210","Nombre":"La Tigra"},"21009":{"Canton":"210","Nombre":"La Palmera"},"21010":{"Canton":"210","Nombre":"Venado"},"21011":{"Canton":"210","Nombre":"Cutris"},"21012":{"Canton":"210","Nombre":"Monterrey"},"21013":{"Canton":"210","Nombre":"Pocosol"},"21101":{"Canton":"211","Nombre":"Zarcero"},"21102":{"Canton":"211","Nombre":"Laguna"},"21103":{"Canton":"211","Nombre":"Tapesco"},"21104":{"Canton":"211","Nombre":"Guadalupe"},"21105":{"Canton":"211","Nombre":"Palmira"},"21106":{"Canton":"211","Nombre":"Zapote"},"21107":{"Canton":"211","Nombre":"Brisas"},"21201":{"Canton":"212","Nombre":"Sarchí Norte"},"21202":{"Canton":"212","Nombre":"Sarchí Sur"},"21203":{"Canton":"212","Nombre":"Toro Amarillo"},"21204":{"Canton":"212","Nombre":"San Pedro"},"21205":{"Canton":"212","Nombre":"Rodríguez"},"21301":{"Canton":"213","Nombre":"Upala"},"21302":{"Canton":"213","Nombre":"Aguas Claras"},"21303":{"Canton":"213","Nombre":"San José (Pizote)"},"21304":{"Canton":"213","Nombre":"Bijagua"},"21305":{"Canton":"213","Nombre":"Delicias"},"21306":{"Canton":"213","Nombre":"Dos Ríos"},"21307":{"Canton":"213","Nombre":"Yoliyllal"},"21401":{"Canton":"214","Nombre":"Los Chiles"},"21402":{"Canton":"214","Nombre":"Caño Negro"},"21403":{"Canton":"214","Nombre":"El Amparo"},"21404":{"Canton":"214","Nombre":"San Jorge"},"21501":{"Canton":"215","Nombre":"San Rafael"},"21502":{"Canton":"215","Nombre":"Buenavista"},"21503":{"Canton":"215","Nombre":"Cote"},"21504":{"Canton":"215","Nombre":"Katira"},"30101":{"Canton":"301","Nombre":"Oriental"},"30102":{"Canton":"301","Nombre":"Occidental"},"30103":{"Canton":"301","Nombre":"Carmen"},"30104":{"Canton":"301","Nombre":"San Nicolás"},"30105":{"Canton":"301","Nombre":"Aguacaliente (San Francisco)"},"30106":{"Canton":"301","Nombre":"Guadalupe (Arenilla)"},"30107":{"Canton":"301","Nombre":"Corralillo"},"30108":{"Canton":"301","Nombre":"Tierra Blanca"},"30109":{"Canton":"301","Nombre":"Dulce Nombre"},"30110":{"Canton":"301","Nombre":"Llano Grande"},"30111":{"Canton":"301","Nombre":"Quebradilla"},"30201":{"Canton":"302","Nombre":"Paraíso"},"30202":{"Canton":"302","Nombre":"Santiago"},"30203":{"Canton":"302","Nombre":"Orosi"},"30204":{"Canton":"302","Nombre":"Cachí"},"30205":{"Canton":"302","Nombre":"Llanos de Santa Lucía"},"30301":{"Canton":"303","Nombre":"Tres Ríos"},"30302":{"Canton":"303","Nombre":"San Diego"},"30303":{"Canton":"303","Nombre":"San Juan"},"30304":{"Canton":"303","Nombre":"San Rafael"},"30305":{"Canton":"303","Nombre":"Concepción"},"30306":{"Canton":"303","Nombre":"Dulce Nombre"},"30307":{"Canton":"303","Nombre":"San Ramón"},"30308":{"Canton":"303","Nombre":"Río Azul"},"30401":{"Canton":"304","Nombre":"Juan Viñas"},"30402":{"Canton":"304","Nombre":"Tucurrique"},"30403":{"Canton":"304","Nombre":"Pejibaye"},"30501":{"Canton":"305","Nombre":"Turrialba"},"30502":{"Canton":"305","Nombre":"La Suiza"},"30503":{"Canton":"305","Nombre":"Peralta"},"30504":{"Canton":"305","Nombre":"Santa Cruz"},"30505":{"Canton":"305","Nombre":"Santa Teresita"},"30506":{"Canton":"305","Nombre":"Pavones"},"30507":{"Canton":"305","Nombre":"Tuis"},"30508":{"Canton":"305","Nombre":"Tayutic"},"30509":{"Canton":"305","Nombre":"Santa Rosa"},"30510":{"Canton":"305","Nombre":"Tres Equis"},"30511":{"Canton":"305","Nombre":"La Isabel"},"30512":{"Canton":"305","Nombre":"Chirripó"},"30601":{"Canton":"306","Nombre":"Pacayas"},"30602":{"Canton":"306","Nombre":"Cervantes"},"30603":{"Canton":"306","Nombre":"Capellades"},"30701":{"Canton":"307","Nombre":"San Rafael"},"30702":{"Canton":"307","Nombre":"Cot"},"30703":{"Canton":"307","Nombre":"Potrero Cerrado"},"30704":{"Canton":"307","Nombre":"Cipreses"},"30705":{"Canton":"307","Nombre":"Santa Rosa"},"30801":{"Canton":"308","Nombre":"El Tejar"},"30802":{"Canton":"308","Nombre":"San Isidro"},"30803":{"Canton":"308","Nombre":"Tobosi"},"30804":{"Canton":"308","Nombre":"Patio de Agua"},"40101":{"Canton":"401","Nombre":"Heredia"},"40102":{"Canton":"401","Nombre":"Mercedes"},"40103":{"Canton":"401","Nombre":"San Francisco"},"40104":{"Canton":"401","Nombre":"Ulloa"},"40105":{"Canton":"401","Nombre":"Varablanca"},"40201":{"Canton":"402","Nombre":"Barva"},"40202":{"Canton":"402","Nombre":"San Pedro"},"40203":{"Canton":"402","Nombre":"San Pablo"},"40204":{"Canton":"402","Nombre":"San Roque"},"40205":{"Canton":"402","Nombre":"Santa Lucía"},"40206":{"Canton":"402","Nombre":"San José de la Montaña"},"40301":{"Canton":"403","Nombre":"Santo Domingo"},"40302":{"Canton":"403","Nombre":"San Vicente"},"40303":{"Canton":"403","Nombre":"San Miguel"},"40304":{"Canton":"403","Nombre":"Paracito"},"40305":{"Canton":"403","Nombre":"Santo Tomás"},"40306":{"Canton":"403","Nombre":"Santa Rosa"},"40307":{"Canton":"403","Nombre":"Tures"},"40308":{"Canton":"403","Nombre":"Para"},"40401":{"Canton":"404","Nombre":"Santa Bárbara"},"40402":{"Canton":"404","Nombre":"San Pedro"},"40403":{"Canton":"404","Nombre":"San Juan"},"40404":{"Canton":"404","Nombre":"Jesús"},"40405":{"Canton":"404","Nombre":"Santo Domingo"},"40406":{"Canton":"404","Nombre":"Puraba"},"40501":{"Canton":"405","Nombre":"San Rafael"},"40502":{"Canton":"405","Nombre":"San Josécito"},"40503":{"Canton":"405","Nombre":"Santiago"},"40504":{"Canton":"405","Nombre":"Los Ángeles"},"40505":{"Canton":"405","Nombre":"Concepción"},"40601":{"Canton":"406","Nombre":"San Isidro"},"40602":{"Canton":"406","Nombre":"San José"},"40603":{"Canton":"406","Nombre":"Concepción"},"40604":{"Canton":"406","Nombre":"San Francisco"},"40701":{"Canton":"407","Nombre":"San Antonio"},"40702":{"Canton":"407","Nombre":"La Ribera"},"40703":{"Canton":"407","Nombre":"La Asunción"},"40801":{"Canton":"408","Nombre":"San Joaquín de Flores"},"40802":{"Canton":"408","Nombre":"Barrantes"},"40803":{"Canton":"408","Nombre":"Llorente"},"40901":{"Canton":"409","Nombre":"San Pablo"},"40902":{"Canton":"409","Nombre":"Rincón de Sabanilla"},"41001":{"Canton":"410","Nombre":"Puerto Viejo"},"41002":{"Canton":"410","Nombre":"La Virgen"},"41003":{"Canton":"410","Nombre":"Horquetas"},"41004":{"Canton":"410","Nombre":"Llanuras del Gaspar"},"41005":{"Canton":"410","Nombre":"Cureña"},"50101":{"Canton":"501","Nombre":"Liberia"},"50102":{"Canton":"501","Nombre":"Cañas Dulces"},"50103":{"Canton":"501","Nombre":"Mayorga"},"50104":{"Canton":"501","Nombre":"Nacascolo"},"50105":{"Canton":"501","Nombre":"Curubande"},"50201":{"Canton":"502","Nombre":"Nicoya"},"50202":{"Canton":"502","Nombre":"Mansion"},"50203":{"Canton":"502","Nombre":"San Antonio"},"50204":{"Canton":"502","Nombre":"Quebrada Honda"},"50205":{"Canton":"502","Nombre":"Samara"},"50206":{"Canton":"502","Nombre":"Nosara"},"50207":{"Canton":"502","Nombre":"Belén de Nosarita"},"50301":{"Canton":"503","Nombre":"Santa Cruz"},"50302":{"Canton":"503","Nombre":"Bolson"},"50303":{"Canton":"503","Nombre":"Veintisiete de Abril"},"50304":{"Canton":"503","Nombre":"Tempate"},"50305":{"Canton":"503","Nombre":"Cartagena"},"50306":{"Canton":"503","Nombre":"Cuajiniquil"},"50307":{"Canton":"503","Nombre":"Diria"},"50308":{"Canton":"503","Nombre":"Cabo Velas"},"50309":{"Canton":"503","Nombre":"Tamarindo"},"50401":{"Canton":"504","Nombre":"Bagaces"},"50402":{"Canton":"504","Nombre":"Fortuna"},"50403":{"Canton":"504","Nombre":"Mogote"},"50404":{"Canton":"504","Nombre":"Río Naranjo"},"50501":{"Canton":"505","Nombre":"Filadelfia"},"50502":{"Canton":"505","Nombre":"Palmira"},"50503":{"Canton":"505","Nombre":"Sardinal"},"50504":{"Canton":"505","Nombre":"Belén"},"50601":{"Canton":"506","Nombre":"Cañas"},"50602":{"Canton":"506","Nombre":"Palmira"},"50603":{"Canton":"506","Nombre":"San Miguel"},"50604":{"Canton":"506","Nombre":"Bebedero"},"50605":{"Canton":"506","Nombre":"Porozal"},"50701":{"Canton":"507","Nombre":"Juntas"},"50702":{"Canton":"507","Nombre":"Sierra"},"50703":{"Canton":"507","Nombre":"San Juan"},"50704":{"Canton":"507","Nombre":"Colorado"},"50801":{"Canton":"508","Nombre":"Tilarán"},"50802":{"Canton":"508","Nombre":"Quebrada Grande"},"50803":{"Canton":"508","Nombre":"Tronadora"},"50804":{"Canton":"508","Nombre":"Santa Rosa"},"50805":{"Canton":"508","Nombre":"Líbano"},"50806":{"Canton":"508","Nombre":"Tierras Morenas"},"50807":{"Canton":"508","Nombre":"Arenal"},"50901":{"Canton":"509","Nombre":"Carmona"},"50902":{"Canton":"509","Nombre":"Santa Rita"},"50903":{"Canton":"509","Nombre":"Zapotal"},"50904":{"Canton":"509","Nombre":"San Pablo"},"50905":{"Canton":"509","Nombre":"Porvenir"},"50906":{"Canton":"509","Nombre":"Bejuco"},"51001":{"Canton":"510","Nombre":"La Cruz"},"51002":{"Canton":"510","Nombre":"Santa Cecilia"},"51003":{"Canton":"510","Nombre":"Garita"},"51004":{"Canton":"510","Nombre":"Santa Elena"},"51101":{"Canton":"511","Nombre":"Hojancha"},"51102":{"Canton":"511","Nombre":"Monte Romo"},"51103":{"Canton":"511","Nombre":"Puerto Carrillo"},"51104":{"Canton":"511","Nombre":"Huacas"},"60101":{"Canton":"601","Nombre":"Puntarenas"},"60102":{"Canton":"601","Nombre":"Pitahaya"},"60103":{"Canton":"601","Nombre":"Chomes"},"60104":{"Canton":"601","Nombre":"Lepanto"},"60105":{"Canton":"601","Nombre":"Paquera"},"60106":{"Canton":"601","Nombre":"Manzanillo"},"60107":{"Canton":"601","Nombre":"Guacimal"},"60108":{"Canton":"601","Nombre":"Barranca"},"60109":{"Canton":"601","Nombre":"Monte Verde"},"60110":{"Canton":"601","Nombre":"Isla del Coco"},"60111":{"Canton":"601","Nombre":"Cobano"},"60112":{"Canton":"601","Nombre":"Chacarita"},"60113":{"Canton":"601","Nombre":"Chira"},"60114":{"Canton":"601","Nombre":"Acapulco"},"60115":{"Canton":"601","Nombre":"El Roble"},"60116":{"Canton":"601","Nombre":"Arancibia"},"60201":{"Canton":"602","Nombre":"Espiritu Santo"},"60202":{"Canton":"602","Nombre":"San Juan Grande"},"60203":{"Canton":"602","Nombre":"Macacona"},"60204":{"Canton":"602","Nombre":"San Rafael"},"60205":{"Canton":"602","Nombre":"San Jerónimo"},"60301":{"Canton":"603","Nombre":"Buenos Aires"},"60302":{"Canton":"603","Nombre":"Volcan"},"60303":{"Canton":"603","Nombre":"Potrero Grande"},"60304":{"Canton":"603","Nombre":"Boruca"},"60305":{"Canton":"603","Nombre":"Pilas"},"60306":{"Canton":"603","Nombre":"Colinas"},"60307":{"Canton":"603","Nombre":"Changena"},"60308":{"Canton":"603","Nombre":"Briolley"},"60309":{"Canton":"603","Nombre":"Brunka"},"60401":{"Canton":"604","Nombre":"Miramar"},"60402":{"Canton":"604","Nombre":"La Unión"},"60403":{"Canton":"604","Nombre":"San Isidro"},"60501":{"Canton":"605","Nombre":"Puerto Cortes"},"60502":{"Canton":"605","Nombre":"Palmar"},"60503":{"Canton":"605","Nombre":"Sierpe"},"60504":{"Canton":"605","Nombre":"Bahia Ballena"},"60505":{"Canton":"605","Nombre":"Piedras Blancas"},"60601":{"Canton":"606","Nombre":"Quepos"},"60602":{"Canton":"606","Nombre":"Savegre"},"60603":{"Canton":"606","Nombre":"Naranjito"},"60701":{"Canton":"607","Nombre":"Golfito"},"60702":{"Canton":"607","Nombre":"Puerto Jiménez"},"60703":{"Canton":"607","Nombre":"Guaycara"},"60704":{"Canton":"607","Nombre":"Pavon"},"60801":{"Canton":"608","Nombre":"San Vito"},"60802":{"Canton":"608","Nombre":"Sabalito"},"60803":{"Canton":"608","Nombre":"Aguabuena"},"60804":{"Canton":"608","Nombre":"Limóncito"},"60805":{"Canton":"608","Nombre":"Pittier"},"60901":{"Canton":"609","Nombre":"Parrita"},"61001":{"Canton":"610","Nombre":"Corredor"},"61002":{"Canton":"610","Nombre":"La Cuesta"},"61003":{"Canton":"610","Nombre":"Canoas"},"61004":{"Canton":"610","Nombre":"Laurel"},"61101":{"Canton":"611","Nombre":"Jacó"},"61102":{"Canton":"611","Nombre":"Tarcoles"},"70101":{"Canton":"701","Nombre":"Limón"},"70102":{"Canton":"701","Nombre":"Valle La Estrella"},"70103":{"Canton":"701","Nombre":"Río Blanco"},"70104":{"Canton":"701","Nombre":"Matama"},"70201":{"Canton":"702","Nombre":"Guapiles"},"70202":{"Canton":"702","Nombre":"Jiménez"},"70203":{"Canton":"702","Nombre":"Rita"},"70204":{"Canton":"702","Nombre":"Roxana"},"70205":{"Canton":"702","Nombre":"Cariari"},"70206":{"Canton":"702","Nombre":"Colorado"},"70301":{"Canton":"703","Nombre":"Siquirres"},"70302":{"Canton":"703","Nombre":"Pacuarito"},"70303":{"Canton":"703","Nombre":"Florida"},"70304":{"Canton":"703","Nombre":"Germania"},"70305":{"Canton":"703","Nombre":"Cairo"},"70306":{"Canton":"703","Nombre":"Alegria"},"70401":{"Canton":"704","Nombre":"Bratsi"},"70402":{"Canton":"704","Nombre":"Sixaola"},"70403":{"Canton":"704","Nombre":"Cahuita"},"70404":{"Canton":"704","Nombre":"Telire"},"70501":{"Canton":"705","Nombre":"Matina"},"70502":{"Canton":"705","Nombre":"Battan"},"70503":{"Canton":"705","Nombre":"Carrandi"},"70601":{"Canton":"706","Nombre":"Guácimo"},"70602":{"Cantonjson_decode(":"706","Nombre":"Mercedes"},"70603":{"Canton":"706","Nombre":"Pocora"},"70604":{"Canton":"706","Nombre":"Río Jiménez"},"70605":{"Canton":"706","Nombre":"Duacari"}}';

    $distritos = json_decode($distritos, true);
    return $distritos[$distritoId]["Nombre"];
  }
  
}
