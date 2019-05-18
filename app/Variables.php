<?php

namespace App;

class Variables
{
  
    public static function tiposIVARepercutidos() {
    $lista = [
      ['nombre'=>'101 - Ventas locales de bienes y servicios con derecho a crédito al 1%', 'codigo'=>'101', 'porcentaje'=>'1'],
      ['nombre'=>'102 - Ventas locales de bienes y servicios con derecho a crédito al 2%', 'codigo'=>'102', 'porcentaje'=>'2'],
      ['nombre'=>'103 - Ventas locales de bienes y servicios con derecho a crédito al 13%', 'codigo'=>'103', 'porcentaje'=>'13'],
      ['nombre'=>'104 - Ventas locales de bienes y servicios con derecho a crédito al 4%', 'codigo'=>'104', 'porcentaje'=>'4'],
      
      ['nombre'=>'114 - Ventas locales con tarifa transitoria del 4% con derecho a crédito  *vigente del 1-07-2020 al 30-06-2021', 'codigo'=>'114', 'porcentaje'=>'4', 'hide'=>true, 'hideMasiva'=>true],
      ['nombre'=>'118 - Ventas locales con tarifa transitoria del 8% con derecho a crédito  *vigente del 1-07-2021 al 30-06-2021', 'codigo'=>'118', 'porcentaje'=>'8', 'hide'=>true, 'hideMasiva'=>true],
      
      ['nombre'=>'121 - Autoconsumo de bienes y servicios con derecho a crédito al 1%', 'codigo'=>'121', 'porcentaje'=>'1', 'hideMasiva'=>true],
      ['nombre'=>'122 - Autoconsumo de bienes y servicios con derecho a crédito al 2%', 'codigo'=>'122', 'porcentaje'=>'2', 'hideMasiva'=>true],
      ['nombre'=>'123 - Autoconsumo de bienes y servicios con derecho a crédito al 13%', 'codigo'=>'123', 'porcentaje'=>'13', 'hideMasiva'=>true],
      ['nombre'=>'124 - Autoconsumo de bienes y servicios con derecho a crédito al 4%', 'codigo'=>'124', 'porcentaje'=>'4', 'hideMasiva'=>true],
      
      ['nombre'=>'130 - Ventas de bienes y servicios con límites sobrepasados al 13% con derecho a crédito', 'codigo'=>'130', 'porcentaje'=>'13', 'hideMasiva'=>true],
      ['nombre'=>'140 - Inversion del sujeto pasivo', 'codigo'=>'140', 'porcentaje'=>'13', 'hideMasiva'=>true],
      ['nombre'=>'150 - Ventas por exportación con derecho a crédito', 'codigo'=>'150', 'porcentaje'=>'0'],
      ['nombre'=>'160 - Ventas al Estado e Instituciones con derecho a crédito', 'codigo'=>'160', 'porcentaje'=>'0'],
      ['nombre'=>'200 - Ventas sin derecho a crédito por exenciones objetivas', 'codigo'=>'200', 'porcentaje'=>'0'],
      ['nombre'=>'201 - Ventas sin derecho a crédito por exenciones objetivas con límite no sobrepasado', 'codigo'=>'201', 'porcentaje'=>'0'],
      ['nombre'=>'240 - Autoconsumo sin derecho a crédito', 'codigo'=>'240', 'porcentaje'=>'0', 'hideMasiva'=>true],
      ['nombre'=>'245 - Ventas locales con tarifa transitoria del 0% sin derecho a crédito', 'codigo'=>'245', 'porcentaje'=>'0'], //*vigente del 1-07-2019 al 30-06-2020
      ['nombre'=>'250 - Ventas a sujetos exentos', 'codigo'=>'250', 'porcentaje'=>'0'],
      ['nombre'=>'260 - Ventas a no sujetos', 'codigo'=>'260', 'porcentaje'=>'0'],
    ];

    return $lista;
  }
  
   public static function tiposRepercutidos() {
    $lista = [
      ['nombre'=>'Bienes generales', 'codigo_iva'=>'103'],
      ['nombre'=>'Exportaciones/Importaciones', 'codigo_iva'=>'150'],
      ['nombre'=>'Servicios profesionales', 'codigo_iva'=>'103'],
      ['nombre'=>'Autoconsumo de servicios', 'codigo_iva'=>'123'],
      ['nombre'=>'Canasta básica', 'codigo_iva'=>'101'],
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
      
      ['nombre'=>'021 - Importaciones de bienes y servicios con IVA al 1% sin identificación específica', 'codigo'=>'021', 'porcentaje'=>'1'],
      ['nombre'=>'022 - Importaciones de bienes y servicios con IVA al 2% sin identificación específica', 'codigo'=>'022', 'porcentaje'=>'2'],
      ['nombre'=>'023 - Importaciones de bienes y servicios con IVA al 13% sin identificación específica', 'codigo'=>'023', 'porcentaje'=>'13'],
      ['nombre'=>'024 - Importaciones de bienes y servicios con IVA al 4% sin identificación específica', 'codigo'=>'024', 'porcentaje'=>'4'],
      
      ['nombre'=>'031 - Importaciones de propiedad planta y equipo con IVA al 1% sin identificación específica', 'codigo'=>'031', 'porcentaje'=>'1'],
      ['nombre'=>'032 - Importaciones de propiedad planta y equipo con IVA al 2% sin identificación específica', 'codigo'=>'032', 'porcentaje'=>'2'],
      ['nombre'=>'033 - Importaciones de propiedad planta y equipo con IVA al 13% sin identificación específica', 'codigo'=>'033', 'porcentaje'=>'13'],
      ['nombre'=>'034 - Importaciones de propiedad planta y equipo con IVA al 4% sin identificación específica', 'codigo'=>'034', 'porcentaje'=>'4'],
      
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
      ['nombre'=>'Bienes generales', 'codigo_iva'=>'3'],
      ['nombre'=>'Exportaciones/Importaciones', 'codigo_iva'=>'13'],
      ['nombre'=>'Servicios profesionales', 'codigo_iva'=>'3'],
      ['nombre'=>'Canasta básica', 'codigo_iva'=>'1'],
      ['nombre'=>'Servicios médicos', 'codigo_iva'=>'4'],
      ['nombre'=>'Medicamentos', 'codigo_iva'=>'2'],
      ['nombre'=>'Turismo', 'codigo_iva'=>'77'],
      ['nombre'=>'Libros', 'codigo_iva'=>'70'],
      ['nombre'=>'Arrendamiento de menos de 640mil colones', 'codigo_iva'=>'3'],
      ['nombre'=>'Arrendamiento de más de 640mil colones', 'codigo_iva'=>'70'],
      ['nombre'=>'Energía residencial', 'codigo_iva'=>'70'],
      ['nombre'=>'Agua residencial', 'codigo_iva'=>'70'],
      ['nombre'=>'Sillas de ruedas y similares', 'codigo_iva'=>'70'],
      ['nombre'=>'Cuota de colegio profesional', 'codigo_iva'=>'77'],
      ['nombre'=>'Otros', 'codigo_iva'=>'3']
    ];

    return $lista;
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
    $lista = Variables::unidadesMedicion();
    
    foreach ($lista as $tipo) {
      if( $value == $tipo['codigo'] ){
        return $tipo['nombre'];
      }
    }
    
    return "Otros";
  }
  
  public static function getTipoSoportadoIVAName($codigo){
    $lista = Variables::tiposIVASoportados();
    
    foreach ($lista as $unidades) {
      if( $codigo == $unidades['codigo'] ){
        return $unidades['nombre'];
      }
    }
    
    return "Otros";
  }
  
  public static function getTipoRepercutidoIVAName($codigo){
    $lista = Variables::tiposIVARepercutidos();
    
    foreach ($lista as $tipo) {
      if( $codigo == $tipo['codigo'] ){
        return $tipo['nombre'];
      }
    }
    
    return "Otros";
  }
  
  public static function getTipoSoportadoIVAPorc($codigo){
    $lista = Variables::tiposIVASoportados();
    
    foreach ($lista as $tipo) {
      if( $codigo == $tipo['codigo'] ){
        return $tipo['porcentaje'];
      }
    }
    
    return "Otros";
  }
  
  public static function getTipoRepercutidoIVAPorc($codigo){
    $lista = Variables::tiposIVARepercutidos();
    
    foreach ($lista as $tipo) {
      if( $codigo == $tipo['codigo'] ){
        return $tipo['porcentaje'];
      }
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
  
}
