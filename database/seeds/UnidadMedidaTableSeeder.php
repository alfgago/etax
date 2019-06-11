<?php

use Illuminate\Database\Seeder;

class UnidadMedidaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $jsonUnidades = [
			"Unidad" => "Unid",
            "Servicios Profesionales" => "Sp",
			"1 por metro" => "1/m",
            "Alquiler de uso comercial" => "Alc",
            "Alquiler de uso habitacional" => "Al",
            "Ampere" => "A",
            "ampere por metro" => "A/m",
            "ampere por metro cuadrado" => "A/m²",
            "Becquerel" => "Bq",
            "bel" => "B",
            "Candela" => "cd",
            "candela por metro cuadrado" => "cd/m²",
            "centímetro" => "cm",
            "Comisiones" => "Cm",
            "coulomb" => "C",
            "coulomb por kilogramo" => "C/kg",
            "coulomb por metro cuadrado" => "C/m²",
            "coulomb por metro cúbico" => "C/m³",
            "día" => "d",
            "electronvolt" => "eV",
            "estereorradión" => "sr",
            "farad" => "F",
            "farad por metro" => "F/m",
            "Galán" => "Gal",
            "grado" => "deg",
            "grado Celsius" => "C",
            "Gramo" => "g",
            "gray" => "Gy",
            "gray por segundo" => "Gy/s",
            "henry" => "H",
            "henry por metro" => "H/m",
            "hertz" => "Hz",
            "hora" => "h",
            "Intereses" => "I",
            "Joule" => "J",
            "joule por kelvin" => "J/K",
            "joule por kilogramo" => "J/kg",
            "joule por metro cúbico" => "J/m³",
            "joule por mol" => "J/mol",
            "katal" => "kat",
            "katal por metro cúbico" => "kat/m³",
            "Kelvin" => "K",
            "Kilogramo" => "kg",
            "kilogramo por metro cúbico" => "kg/m³",
            "Kilometro" => "Km",
            "kilovatios" => "Kw",
            "litro" => "L",
            "lumen" => "lm",
            "lux" => "lx",
            "Metro" => "m",
            "metro cuadrado" => "m²",
            "metro cúbico" => "m³",
            "metro por segundo" => "m/s",
            "mililitro" => "mL",
            "Milímetro" => "mm",
            "minuto" => "min",
            "Mol" => "mol",
            "mol por metro cúbico" => "mol/m³",
            "neper" => "Np",
            "newton" => "N",
            "newton por metro" => "N/m",
            "Onzas" => "Oz",
            "Otro tipo de servicio" => "Os",
            "pascal" => "Pa",
            "pascal segundo" => "Pa s",
            "pulgada" => "ln",
            "radión" => "rad",
            "radión por segundo" => "rad/s",
            "Se debe indicar la descripción de la medida a utilizar" => "Otros",
            "segundo" => "s",
            "Servicios personales" => "Spe",
            "Servicios técnicos" => "St",
            "siemens" => "S",
            "sievert" => "Sv",
            "tesla" => "T",
            "tonelada" => "t",
            "unidad astronómica" => "ua",
            "unidad de masa atómica unificada" => "u",
            "uno (indice de refracción)" => "1",
            "volt" => "V",
            "volt por metro" => "V/m",
            "Watt" => "W",
            "watt por estereorradión" => "W/sr",
            "watt por metro cuadrado" => "W/m²",
            "watt por metro kevin" => "W/(mxK)",
            "weber" => "Wb"
        ];

        foreach ($jsonUnidades as $key => $values) {
            \App\UnidadMedicion::updateOrCreate(['code' => $values], ['name' => strtoupper($key) ]);
        }
    }
}
