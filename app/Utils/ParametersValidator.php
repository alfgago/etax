<?php

namespace App\Utils;

use App\Models\ObjectTypeStatus;
use App\Models\TourDealership;
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
            'aceptacion' => 'required'
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
}
