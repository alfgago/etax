<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\ParametersValidator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    use AuthenticatesUsers, ParametersValidator;
    //
    public function loginSyncGoSocket() {

        //TODO: Esto es referencia para el login, se cambiar el query del usuario enviando el user_name y la compañia.
        $user = User::where('user_name', '=', 'xavierperna@gmail.com.ve')->first();
        if ($user !== null && Auth::loginUsingId($user->id)) {
            return redirect('/');
        } else {
            return redirect('/login');
       }
    }

    /**
     * Login User Api.
     *
     * @OA\Post(
     *     path="/api/v1/login",
     *     tags={"Autenticacion y Usuarios"},
     *     summary="Login user",
     *     operationId="loginUser",
     *     @OA\Response(
     *         response=200,
     *         description="Successful Operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      type="object",
     *                      description="Login",
     *                      title="Login",
     *                      property="data",
     *                      ref="#/components/schemas/Login"
     *                  ),
     *                  @OA\Property(
     *                      type="string",
     *                      description="Message",
     *                      title="Message",
     *                      property="message"
     *                  )
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  ref="#/components/schemas/Error"
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  ref="#/components/schemas/Error"
     *              )
     *          )
     *     ),
     *     requestBody={"$ref": "#/components/requestBodies/Login"}
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request) {
        try {
            $validator = $this->validatorLogin($request);
            if ($validator->fails()) {
                return response()->json(
                    ['error' => 'Bad Request'],
                    400
                );
            }
            //we get the username / email
            $email = $request->username;
            //$this->attemptLogin expects and email and then it overwrites $request
            $request['email'] = $email;
            //check the login params
            if ($this->attemptLogin($request)) {
                //create the oauth request
                $request = Request::create('/oauth/token', 'POST', [
                    'form_params' => [
                        'client_id' => $request->client_id,
                        'client_secret' => $request->client_secret,
                        'grant_type' => $request->grant_type,
                        'password' => $request->password,
                        'username' => strtolower($request->username),
                    ],
                ]);
                $response = Route::dispatch($request);

                //return the auth response
                return $response->getContent();
            } else {
                return response()->json(
                    ['error' => 'Usuario Invalido'],
                    403
                );
            }
        } catch (\Exception $exception) {
            Log::error("Error Login $exception");
            return response()->json(
                ['error' => 'Usuario Invalido'],
                400
            );
        }
    }


    public function refreshToken(Request $request) {
        try {
            $validator = $this->validatorRefreshToken($request);
            if ($validator->fails()) {
                return response()->json(
                    ['error' => 'Bad Request'],
                    400
                );
            }
            $request = Request::create('/oauth/token', 'POST', [
                'form_params' => [
                    'client_id'     => $request->client_id,
                    'client_secret' => $request->client_secret,
                    'grant_type'    => $request->grant_type,
                    'refresh_token' => $request->refresh_token,
                    'scope'         => ''
                ],
            ]);
            $response = Route::dispatch($request);
            return $response->getContent();
        } catch (\Throwable $exception) {
            Log::error("Error Login $exception");
            return response()->json(
                ['error' => 'Usuario Invalido'],
                400
            );
        }
    }

    public function do() {
        return response()->json(
            ['error' => 'No ha iniciado sesion'],
            403
        );
    }
}
