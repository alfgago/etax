<?php

namespace App\Utils;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

trait ParametersValidator {

    private function validatorLogin($request) {
        $validator = Validator::make($request->all(), [
            'client_id'     => 'required',
            'client_secret' => 'required',
            'grant_type'    => 'required',
            'password'      => 'required',
            'username'      => 'required|email',
        ]);
        return $validator;
    }

    private function validatorRefreshToken($request) {
        $validator = Validator::make($request->all(), [
            'client_id'     => 'required',
            'client_secret' => 'required',
            'grant_type'    => 'required',
            'refresh_token' => 'required',
            'scope'         => '',
        ]);
        return $validator;
    }

    private function validatorRegisterUser($request) {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|max:50',
            'email'      => 'required|email|max:50',
            'roles'      => 'required',
        ]);
        return $validator;
    }

    private function validatorResetPassword($request) {
        $validator = Validator::make($request->all(), [
            'password'   => 'required',
            'password_confirmation' => 'required|same:password',
            'token' => 'required',
        ]);
        return $validator;
    }

    private function validatorForgotPassword($request) {
        $validator = Validator::make($request->all(), [
            'email'      => 'required|email',
        ]);
        return $validator;
    }

    private function validatorUserUpdate($request, $object = null) {
        if (empty($object)) {
            return $this->validatorRegisterUser($request);
        }
        $params = $request->all();
        $rulesToValidate = [];
        $rules = [
            'name'  => 'required|max:50',
            'email' => 'required|email|max:50',
            'roles' => 'required',
            'phone' => 'required|max:50',
            'corporate' => 'required',
            'is_active' => 'required'
        ];
        foreach ($params as $field => $value) {
            if ($object->$field != $value && isset($rules[$field])) {
                $rulesToValidate[$field] = $rules[$field];
            }
        }
        return Validator::make($request->all(), $rulesToValidate);
    }

    private function validatorBillStore($request) {
        $validator = Validator::make($request->all(), [
            'resumenFactura.totalComprobante' => 'required',
            'resumenFactura.totalVentaNeta' => 'required',
            'emisor.identificacion.numero' => 'required',
            'receptor.identificacion.numero' => 'required',
            'numeroConsecutivo' => 'required|max:20|min:20',
            'clave' => 'required|max:50|min:50|unique:bills,document_key',
            'detalleServicio' => 'required',
            'fechaEmision' => 'required'
        ]);
        return $validator;
    }

    private function validatorBillAccept($request) {
        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'claveDocumento' => 'required|max:50|min:50',
            'aceptacion' => 'required|integer|min:1|max:2'
        ]);
        return $validator;
    }
    private function validatorBillValidate($request) {
        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'claveDocumento' => 'required|max:50|min:50',
            'actividadComercial' => 'required',
            'lineas' => 'required'
        ]);
        return $validator;
    }

    private function validatorgetBill($request) {
        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
             'claveDocumento' => 'required|max:50|min:50'
        ]);
        return $validator;
    }

    private function validatorCuentasContables($request) {
        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'ano' => 'required|integer',
            'mes' => 'required|max:12|min:0|integer'
        ]);
        return $validator;
    }

    private function validatorReporteLibro($request) {
        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'ano' => 'required|integer',
            'mes' => 'required|max:12|min:1|integer'
        ]);
        return $validator;
    }

    private function validatorDetalleCredito($request) {
        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'ano' => 'required|integer'
        ]);
        return $validator;
    }

    private function validatorRetenciones ($request) {
        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'cierre' => 'required|integer'
        ]);
        return $validator;
    }

    private function validatorActualizarRetenciones ($request) {
        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'cierre' => 'required|integer',
            'montoRetenido' => 'min:0|required|regex:/^\d+(\.\d{1,2})?$/',
        ]);
        return $validator;
    }

     private function validatorCierres ($request) {
        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'cierre' => 'required|integer'
        ]);
        return $validator;
    }

    private function validatorConsultarFactura ($request) {
        $validator = Validator::make($request->all(), [
            'empresa' => 'required',
            'clave' => 'required|numeric'
        ]);
        return $validator;
    }

    private function validatorInvoiceEmitir($request) {
        $validator = Validator::make($request->all(), [
            'codigoActividad' => 'required',
            'medioPago' => 'required',
            'fechaEmision' => 'required|date_format:d-m-Y H:i:s',
            'moneda' => 'required',
            'condicionVenta' => 'required',
            'tipo_documento' => 'required',
            'emisor.identificacion.numero' => 'required',
            'receptor.identificacion.numero' => 'required',
            'detalleServicio.*.lineaDetalle' => 'required',
            'detalleServicio.*.lineaDetalle.numeroLinea' => 'required|integer|min:1|max:999',
            'detalleServicio.*.lineaDetalle.cantidad' => 'required',
            'detalleServicio.*.lineaDetalle.unidadMedida' => 'required',
            'detalleServicio.*.lineaDetalle.precioUnitario' => 'required',
            'detalleServicio.*.lineaDetalle.subTotal' => 'required',
            //'detalleServicio.*.lineaDetalle.impuesto' => 'required|array', No es requerido por hacienda.
            'detalleServicio.*.lineaDetalle.codigoEtax'  => 'required|String|max:4|min:4',
            //'detalleServicio.*.lineaDetalle.impuesto.*.exento' => 'required|boolean',
            //'detalleServicio.*.lineaDetalle.impuesto.*.exoneracion.tipoDocumentoExoneracion' => 'required_if:lineaDetalle.*.impuesto.*.exento,==,1', Estaba fallando un poco
            'resumenFactura.totalVentaNeta' => 'required',
            'resumenFactura.totalComprobante' => 'required'

        ]);
        return $validator;
    }
}
