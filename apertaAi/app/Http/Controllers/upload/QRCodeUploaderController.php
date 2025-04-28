<?php

namespace App\Http\Controllers\upload;

use App\Domain\Services\QRCodeService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQRCodeRequest;
use App\Http\Requests\UpdateQRCodeRequest;
use App\Http\Responses\ApiModelErrorResponse;
use App\Models\QRCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QRCodeUploaderController extends Controller
{
    protected $qrCodeService;

    /**
     * Inicializa o controller com o serviço de QR codes
     */
    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function read(StoreQRCodeRequest $request)
    {
        try {
            $validatedData = $request->validated();
            Log::info('Dados validados recebidos', $validatedData);

            // $user = Auth::user();
            
            // if (!$user) {
            //     Log::warning('Tentativa de criar QR code sem autenticação');
            //     return response()->json(['message' => 'User not authenticated'], 401);
            // }

            // if (!Auth::check()) {
            //     Log::warning('Tentativa de criar QR code sem autenticação');
            //     return response()->json(['message' => 'User not authenticated'], 401);
            // }
            
            // Adiciona o ID do usuário aos dados validados
            // $validatedData['user_id'] = $user->id;
            
            $response = $this->qrCodeService->readQrCode($validatedData);

            if($response instanceof ApiModelErrorResponse) {
                return response()->json($response->toArray(), $response->getStatusCode());
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Erro ao criar QR code: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

}
