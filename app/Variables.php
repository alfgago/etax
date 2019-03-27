<?php

namespace App;

class Variables
{
  
    public static function tiposIVARepercutidos() {
    $lista = [
      ['nombre'=>'Ventas locales de bienes y servicios con derecho a crédito al 1%', 'codigo'=>'101', 'porcentaje'=>'1'],
      ['nombre'=>'Ventas locales de bienes y servicios con derecho a crédito al 2%', 'codigo'=>'102', 'porcentaje'=>'2'],
      ['nombre'=>'Ventas locales de bienes y servicios con derecho a crédito al 13%', 'codigo'=>'103', 'porcentaje'=>'13'],
      ['nombre'=>'Ventas locales de bienes y servicios con derecho a crédito al 4%', 'codigo'=>'104', 'porcentaje'=>'4'],
      
      ['nombre'=>'Autoconsumo de bienes y servicios con derecho a crédito al 1%', 'codigo'=>'121', 'porcentaje'=>'1'],
      ['nombre'=>'Autoconsumo de bienes y servicios con derecho a crédito al 2%', 'codigo'=>'122', 'porcentaje'=>'2'],
      ['nombre'=>'Autoconsumo de bienes y servicios con derecho a crédito al 13%', 'codigo'=>'123', 'porcentaje'=>'13'],
      ['nombre'=>'Autoconsumo de bienes y servicios con derecho a crédito al 4%', 'codigo'=>'124', 'porcentaje'=>'4'],
      
      ['nombre'=>'Ventas de bienes y servicios con límites sobrepasados al 13% con derecho a crédito', 'codigo'=>'130', 'porcentaje'=>'13'],
      ['nombre'=>'Ventas por exportación con derecho a crédito', 'codigo'=>'150', 'porcentaje'=>'0'],
      ['nombre'=>'Ventas al Estado e Instituciones con derecho a crédito', 'codigo'=>'160', 'porcentaje'=>'0'],
      ['nombre'=>'Ventas sin derecho a crédito por exenciones objetivas', 'codigo'=>'200', 'porcentaje'=>'0'],
      ['nombre'=>'Ventas sin derecho a crédito por exenciones objetivas con límite no sobrepasado', 'codigo'=>'201', 'porcentaje'=>'0'],
      ['nombre'=>'Autoconsumo sin derecho a crédito', 'codigo'=>'240', 'porcentaje'=>'0'],
      ['nombre'=>'Ventas a sujetos exentos', 'codigo'=>'250', 'porcentaje'=>'0'],
      ['nombre'=>'Ventas a no sujetos', 'codigo'=>'260', 'porcentaje'=>'0'],
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
      ['nombre'=>'Compras locales de bienes y servicios con al IVA 1% sin identificación específica', 'codigo'=>'001', 'porcentaje'=>'1'],
      ['nombre'=>'Compras locales de bienes y servicios con al IVA 2% sin identificación específica', 'codigo'=>'002', 'porcentaje'=>'2'],
      ['nombre'=>'Compras locales de bienes y servicios con al IVA 13% sin identificación específica ', 'codigo'=>'003', 'porcentaje'=>'13'],
      ['nombre'=>'Compras locales de bienes y servicios con al IVA 4% sin identificación específica', 'codigo'=>'004', 'porcentaje'=>'4'],
      
      ['nombre'=>'Compras locales de propiedad planta y equipo con al IVA 1% sin identificación específica', 'codigo'=>'011', 'porcentaje'=>'1'],
      ['nombre'=>'Compras locales de propiedad planta y equipo con al IVA 2% sin identificación específica', 'codigo'=>'012', 'porcentaje'=>'2'],
      ['nombre'=>'Compras locales de propiedad planta y equipo con al IVA 13% sin identificación específica', 'codigo'=>'013', 'porcentaje'=>'13'],
      ['nombre'=>'Compras locales de propiedad planta y equipo con al IVA 4% sin identificación específica', 'codigo'=>'014', 'porcentaje'=>'4'],
      
      ['nombre'=>'Importaciones de bienes y servicios con al IVA 1% sin identificación específica', 'codigo'=>'021', 'porcentaje'=>'1'],
      ['nombre'=>'Importaciones de bienes y servicios con al IVA 2% sin identificación específica', 'codigo'=>'022', 'porcentaje'=>'2'],
      ['nombre'=>'Importaciones de bienes y servicios con al IVA 13% sin identificación específica', 'codigo'=>'023', 'porcentaje'=>'13'],
      ['nombre'=>'Importaciones de bienes y servicios con al IVA 4% sin identificación específica', 'codigo'=>'024', 'porcentaje'=>'4'],
      
      ['nombre'=>'Importaciones de propiedad planta y equipo con al IVA 1% sin identificación específica', 'codigo'=>'031', 'porcentaje'=>'1'],
      ['nombre'=>'Importaciones de propiedad planta y equipo con al IVA 2% sin identificación específica', 'codigo'=>'032', 'porcentaje'=>'2'],
      ['nombre'=>'Importaciones de propiedad planta y equipo con al IVA 13% sin identificación específica', 'codigo'=>'033', 'porcentaje'=>'13'],
      ['nombre'=>'Importaciones de propiedad planta y equipo con al IVA 4% sin identificación específica', 'codigo'=>'034', 'porcentaje'=>'4'],
      
      //Con identificación específica
      ['nombre'=>'Importaciones de bienes y servicios exentos', 'codigo'=>'040', 'porcentaje'=>'0'],
      ['nombre'=>'Importaciones con IVA al 1% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'041', 'porcentaje'=>'1'],
      ['nombre'=>'Importaciones con IVA al 2% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'042', 'porcentaje'=>'2'],
      ['nombre'=>'Importaciones con IVA al 13% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'043', 'porcentaje'=>'13'],
      ['nombre'=>'Importaciones con IVA al 4% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'044', 'porcentaje'=>'4'],
      
      ['nombre'=>'Importaciones de propiedad planta y equipo exentos', 'codigo'=>'050', 'porcentaje'=>'0'],
      ['nombre'=>'Importaciones con IVA al 1% de propiedad planta y equipo de acreditación plena con identificación específica', 'codigo'=>'051', 'porcentaje'=>'1'],
      ['nombre'=>'Importaciones con IVA al 2% de propiedad planta y equipo de acreditación plena con identificación específica', 'codigo'=>'052', 'porcentaje'=>'2'],
      ['nombre'=>'Importaciones con IVA al 13% de propiedad planta y equipo de acreditación plena con identificación específica', 'codigo'=>'053', 'porcentaje'=>'13'],
      ['nombre'=>'Importaciones con IVA al 4% de propiedad planta y equipo de acreditación plena con identificación específica', 'codigo'=>'054', 'porcentaje'=>'4'],
      
      ['nombre'=>'Compras locales de bienes y servicios exentos', 'codigo'=>'060', 'porcentaje'=>'0'],
      ['nombre'=>'Compras locales con IVA al 1% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'061', 'porcentaje'=>'1'],
      ['nombre'=>'Compras locales con IVA al 2% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'062', 'porcentaje'=>'2'],
      ['nombre'=>'Compras locales con IVA al 13% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'063', 'porcentaje'=>'13'],
      ['nombre'=>'Compras locales con IVA al 4% de bienes y servicios de acreditación plena con identificación específica', 'codigo'=>'064', 'porcentaje'=>'4'],
      
      ['nombre'=>'Compras locales de  propiedad planta y equipo exentos.', 'codigo'=>'070', 'porcentaje'=>'0'],
      ['nombre'=>'Compras locales con IVA al 1% de propiedad planta y equipo de acreditación plena con identificación específica.', 'codigo'=>'071', 'porcentaje'=>'1'],
      ['nombre'=>'Compras locales con IVA al 2% de propiedad planta y equipo de acreditación plena con identificación específica.', 'codigo'=>'072', 'porcentaje'=>'2'],
      ['nombre'=>'Compras locales con IVA al 13% de propiedad planta y equipo de acreditación plena con identificación específica.', 'codigo'=>'073', 'porcentaje'=>'13'],
      ['nombre'=>'Compras locales con IVA al 4% de propiedad planta y equipo de acreditación plena con identificación específica.', 'codigo'=>'074', 'porcentaje'=>'4'],
      
      //No acreditables
      ['nombre'=>'Compras de bienes y servicios con IVA no acreditable desde origen', 'codigo'=>'080', 'porcentaje'=>'0'],
      ['nombre'=>'Importaciones de bienes y servicios con IVA no acreditable desde origen', 'codigo'=>'090', 'porcentaje'=>'0'],
      ['nombre'=>'Compras con IVA no acreditable por gastos no deducibles', 'codigo'=>'097', 'porcentaje'=>'0']
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
  
}
