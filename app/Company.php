<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model {

    protected $table = 'companies';
    protected $guarded = [];

    //Relación con facturas emitidas
    public function invoices() {
        return $this->hasMany(Invoice::class);
    }

    //Relación con facturas recibidas
    public function bills() {
        return $this->hasMany(Bill::class);
    }

    //Relación con clientes
    public function clients() {
        return $this->hasMany(Client::class);
    }

    //Relación con proveedores
    public function providers() {
        return $this->hasMany(Provider::class);
    }

    //Relación con Usuario
    public function owner() {
        return $this->belongsTo(User::class);
    }

}