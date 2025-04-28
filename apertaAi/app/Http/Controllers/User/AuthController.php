<?php

namespace App\Http\Controllers\User;

use App\Domain\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Responses\ApiModelErrorResponse;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    public function login(UserLoginRequest $userLoginRequest) {

        $validatedDataLogin = $userLoginRequest->validated();
        
        $response = $this->authService->login($validatedDataLogin);

        if($response instanceof ApiModelErrorResponse){
            return response()->json($response->toArray(), $response->getStatusCode());
        }

        return $response;
    }


    /*
     * Remove todos os tokens do usuário autenticado
     */
    public function logout() {
        $response = $this->authService->logout();
        
        return response()->json($response->toArray(), $response->getStatusCode());
    }
}
