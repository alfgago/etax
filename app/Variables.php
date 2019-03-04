<?php

namespace App;

class Variables
{
  
    public static function tiposIVARepercutidos() {
    $lista = [
      ['nombre'=>'Ventas al 1%', 'codigo'=>'101', 'porcentaje'=>'1'],
      ['nombre'=>'Ventas al 2%', 'codigo'=>'102', 'porcentaje'=>'2'],
      ['nombre'=>'Ventas al 13%', 'codigo'=>'103', 'porcentaje'=>'13'],
      ['nombre'=>'Ventas al 4%', 'codigo'=>'104', 'porcentaje'=>'4'],
      ['nombre'=>'Autoconsumo al 1%', 'codigo'=>'121', 'porcentaje'=>'1'],
      ['nombre'=>'Autoconsumo al 2%', 'codigo'=>'122', 'porcentaje'=>'2'],
      ['nombre'=>'Autoconsumo al 13%', 'codigo'=>'123', 'porcentaje'=>'13'],
      ['nombre'=>'Autoconsumo al 4%', 'codigo'=>'124', 'porcentaje'=>'4'],
      ['nombre'=>'Ventas con límite sobrepasado', 'codigo'=>'130', 'porcentaje'=>'13'],
      ['nombre'=>'Cobros anticipados al 1%', 'codigo'=>'141', 'porcentaje'=>'1'],
      ['nombre'=>'Cobros anticipados al 2%', 'codigo'=>'142', 'porcentaje'=>'2'],
      ['nombre'=>'Cobros anticipados al 13%', 'codigo'=>'143', 'porcentaje'=>'13'],
      ['nombre'=>'Cobros anticipados al 4%', 'codigo'=>'144', 'porcentaje'=>'4'],
      ['nombre'=>'Exportaciones y asimilados', 'codigo'=>'150', 'porcentaje'=>'0'],
      ['nombre'=>'Operaciones al estado y entes institucionales', 'codigo'=>'160', 'porcentaje'=>'0'],
      ['nombre'=>'Rectificaciones no exentas', 'codigo'=>'199', 'porcentaje'=>'0'],
      ['nombre'=>'Exenciones objetivas sin límite', 'codigo'=>'200', 'porcentaje'=>'0'],
      ['nombre'=>'Exenciones objetivas con límite no sobrepasado', 'codigo'=>'201', 'porcentaje'=>'0'],
      ['nombre'=>'Exenciones autoconsumo', 'codigo'=>'240', 'porcentaje'=>'0'],
      ['nombre'=>'Exenciones subjetivas', 'codigo'=>'250', 'porcentaje'=>'0'],
      ['nombre'=>'Ventas a no sujetos', 'codigo'=>'260', 'porcentaje'=>'0'],
      ['nombre'=>'Reclasificaciones exentas', 'codigo'=>'299', 'porcentaje'=>'0'],
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
      ['nombre'=>'IVA soportado al 1%', 'codigo'=>'1', 'porcentaje'=>'1'],
      ['nombre'=>'IVA soportado al 2%', 'codigo'=>'2', 'porcentaje'=>'2'],
      ['nombre'=>'IVA soportado al 13%', 'codigo'=>'3', 'porcentaje'=>'13'],
      ['nombre'=>'IVA soportado al 4%', 'codigo'=>'4', 'porcentaje'=>'4'],
      ['nombre'=>'Importaciones al 1%', 'codigo'=>'51', 'porcentaje'=>'1'],
      ['nombre'=>'Importaciones al 2%', 'codigo'=>'52', 'porcentaje'=>'2'],
      ['nombre'=>'Importaciones al 13%', 'codigo'=>'53', 'porcentaje'=>'13'],
      ['nombre'=>'Importaciones al 4%', 'codigo'=>'54', 'porcentaje'=>'4'],
      ['nombre'=>'IVA soportado 100% deducible al 1% por destino', 'codigo'=>'61', 'porcentaje'=>'1'],
      ['nombre'=>'IVA soportado 100% deducible al 2% por destino', 'codigo'=>'62', 'porcentaje'=>'2'],
      ['nombre'=>'IVA soportado 100% deducible al 13% por destino', 'codigo'=>'63', 'porcentaje'=>'13'],
      ['nombre'=>'IVA soportado 100% deducible al 4% por destino', 'codigo'=>'64', 'porcentaje'=>'4'],
      ['nombre'=>'IVA no deducible por origen', 'codigo'=>'70', 'porcentaje'=>'0'],
      ['nombre'=>'IVA no deducible por destino', 'codigo'=>'77', 'porcentaje'=>'0'],
      ['nombre'=>'IVA de bienes de capital', 'codigo'=>'80', 'porcentaje'=>'13'],
      ['nombre'=>'IVA por pagos anticipados', 'codigo'=>'90', 'porcentaje'=>'13'],
      ['nombre'=>'Reclasificaciones', 'codigo'=>'99', 'porcentaje'=>'13'],
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
