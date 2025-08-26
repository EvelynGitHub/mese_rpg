<?php

namespace App\Http\Controllers;

use App\Domain\Auth\LoginDTO;
use App\Domain\Auth\RegisterDTO;
use App\UseCases\Auth\LoginUseCase;
use App\UseCases\Auth\MeUseCase;
use App\UseCases\Auth\RegisterUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    private RegisterUseCase $registerUseCase;
    private LoginUseCase $loginUseCase;
    private MeUseCase $meUseCase;

    public function __construct(
        RegisterUseCase $registerUseCase,
        LoginUseCase $loginUseCase,
        MeUseCase $meUseCase
    ) {
        $this->registerUseCase = $registerUseCase;
        $this->loginUseCase = $loginUseCase;
        $this->meUseCase = $meUseCase;
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'senha' => 'required|string|min:8'
        ]);

        $dto = new RegisterDTO(
            $request->input('nome'),
            $request->input('email'),
            $request->input('senha')
        );

        try {
            $user = $this->registerUseCase->execute($dto);
            return response()->json([
                'id' => $user->getId(),
                'nome' => $user->getNome(),
                'email' => $user->getEmail()
            ], Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'senha' => 'required|string'
        ]);

        $dto = new LoginDTO(
            $request->input('email'),
            $request->input('senha')
        );

        try {
            $result = $this->loginUseCase->execute($dto);
            // return response()->json($result);

            return response()
                ->json([
                    'message' => 'Login realizado com sucesso',
                    'user' => $result['user']
                ])
                ->cookie(
                    'jwt_token',
                    $result['token']['access_token'],
                    60, // 60 minutos
                    '/',
                    null,
                    false, // true = só HTTPS
                    true  // httpOnly
                );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            $result = $this->meUseCase->execute($request->user()->id);
            return response()->json($result);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    public function logout()
    {
        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ])->cookie(
                'jwt_token',
                null,
                -1
            );
    }
}
