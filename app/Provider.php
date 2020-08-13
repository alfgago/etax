<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Provider extends Model
{
    use Sortable, SoftDeletes;

    protected $guarded = [];
    public $sortable = ['code', 'first_name', 'email', 'id_number'];

    //Relacion con la empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function quickbooksProvider()
    {
        return $this->hasOne(QuickbooksProvider::class);
    }

    public function getFullName()
    {
        return $this->first_name . " " . $this->last_name . " " . $this->last_name2;
    }

    public function toString()
    {
        return $this->id_number . " - " . $this->getFullName();
    }

    public function getTipoPersona()
    {
        $tipoStr = 'Física';
        if ($this->tipo_persona == 1 || $this->tipo_persona == 'F') {
            $tipoStr = 'Física';
        } else if ($this->tipo_persona == 2 || $this->tipo_persona == 'J') {
            $tipoStr = 'Jurídica';
        } else if ($this->tipo_persona == 3 || $this->tipo_persona == 'D') {
            $tipoStr = 'DIMEX';
        } else if ($this->tipo_persona == 4 || $this->tipo_persona == 'E') {
            $tipoStr = 'Extranjero';
        } else if ($this->tipo_persona == 5 || $this->tipo_persona == 'N') {
            $tipoStr = 'NITE';
        } else if ($this->tipo_persona == 6 || $this->tipo_persona == 'O') {
            $tipoStr = 'Otro';
        }
        return $tipoStr;
    }

    public function getTipoPersonaXML()
    {
        $tipo_persona = '';
        if ($this->tipo_persona == 1 || $this->tipo_persona == 'F' || $this->tipo_persona == '01') {
            $tipo_persona = '01';
        } else if ($this->tipo_persona == 2 || $this->tipo_persona == 'J' || $this->tipo_persona == '02') {
            $tipo_persona = '02';
        } else if ($this->tipo_persona == 3 || $this->tipo_persona == 'D' || $this->tipo_persona == '03') {
            $tipo_persona = '03';
        } else if ($this->tipo_persona == 4 || $this->tipo_persona == 'E' || $this->tipo_persona == '04') {
            $tipo_persona = '04';
        } else if ($this->tipo_persona == 5 || $this->tipo_persona == 'N' || $this->tipo_persona == '05') {
            $tipo_persona = '05';
        } else if ($this->tipo_persona == 6 || $this->tipo_persona == 'O' || $this->tipo_persona == '06') {
            $tipo_persona = '06';
        }
        return $tipo_persona;
    }

}
