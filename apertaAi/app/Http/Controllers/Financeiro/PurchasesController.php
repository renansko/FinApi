<?php

namespace App\Http\Controllers\Financeiro;

use App\Models\Purchases;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchasesRequest;
use App\Http\Requests\UpdatePurchasesRequest;
use App\Domain\Services\PurchasesService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PurchasesController extends Controller
{
    protected $purchasesService;

    /**
     * Inicializa o controller com o serviço de compras
     */
    public function __construct(PurchasesService $purchasesService)
    {
        $this->purchasesService = $purchasesService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = $this->purchasesService->searchPurchasess();
        return response()->json($response->toArray(), $response->getStatusCode());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchasesRequest $request)
    {
        try {
            $validatedData = $request->validated();
            Log::info('Dados validados recebidos', $validatedData);

            $user = Auth::user();
            
            if (!$user) {
                Log::warning('Tentativa de criar compra sem autenticação');
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            if (!Auth::check()) {
                Log::warning('Tentativa de criar compra sem autenticação');
                return response()->json(['message' => 'User not authenticated'], 401);
            }
            
            // Adiciona o ID do usuário aos dados validados
            $validatedData['user_id'] = $user->id;
            
            $response = $this->purchasesService->createPurchases($validatedData);
            return response()->json($response->toArray(), $response->getStatusCode());
        } catch (\Exception $e) {
            Log::error('Erro ao criar compra: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Erro interno do servidor'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchases $purchase)
    {
        $response = $this->purchasesService->findPurchases($purchase);
        return response()->json($response->toArray(), $response->getStatusCode());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchasesRequest $request, Purchases $purchase)
    {
        $validatedData = $request->validated();
        
        $response = $this->purchasesService->update($purchase, $validatedData);
        return response()->json($response->toArray(), $response->getStatusCode());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchases $purchase)
    {
        $response = $this->purchasesService->destroy($purchase);
        return response()->json($response->toArray(), $response->getStatusCode());
    }
}
