<?php

namespace App\Domain\Services;

use App\Http\Responses\ApiModelErrorResponse;
use App\Http\Responses\ApiModelResponse;
use App\Models\Purchases;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class PurchasesService
{
    public function searchPurchasess(): ApiModelResponse|ApiModelErrorResponse
    {
        Log::info(Purchases::class . ' Searching all purchases : function-searchPurchasess');
        $purchases = Purchases::all();

        if($purchases->isEmpty()){
            Log::info(Purchases::class . ' No purchases found : function-searchPurchasess');
            return new ApiModelErrorResponse('No purchases found', new Exception(), [], 404);
        }

        Log::info(Purchases::class . ' Found ' . $purchases->count() . ' purchases : function-searchPurchasess');
        $response = new ApiModelResponse('Purchasess retrieved successfully', $purchases, 200);
       
        return $response;
    }

    public function findPurchases(Purchases $purchase): ApiModelResponse|ApiModelErrorResponse
    {
        Log::info(Purchases::class . ' Finding purchase : function-findPurchases', ['purchase_id' => $purchase->id]);
        
        if(!$purchase){
            Log::warning(Purchases::class . ' Purchases not found : function-findPurchases', ['purchase_id' => $purchase->id]);
            return new ApiModelErrorResponse('Purchases not found', new Exception(), [], 404);
        }

        Log::info(Purchases::class . ' Purchases found successfully : function-findPurchases', ['purchase_id' => $purchase->id]);
        $response = new ApiModelResponse('Purchases found successfully', $purchase, 200);
       
        return $response;
    }

    public function createPurchases(array $purchaseData){
        DB::beginTransaction();
        try {
            $purchase = new Purchases();

            Log::info(Purchases::class . ' Creating purchase : function-createPurchases', ['purchase data' => $purchaseData]);

            $existingPurchases = Purchases::where('item', $purchaseData['item'])
            ->where('payment_date', $purchaseData['payment_date'])
            ->where('user_id', $purchaseData['user_id'])
            ->first();
            // if ($existingPurchases && $existingPurchases->trashed()) {
            //     $existingPurchases->restore();
    
            //     DB::commit();
            //     $response = new ApiModelResponse('Purchases restored successfully!', $existingPurchases, 200);
            //     return $response;
            // } elseif ($existingPurchases) {
            //     throw new ConflictHttpException('The purchase reference is already in use.');
            // }

            $purchase->fill($purchaseData);

            if ($purchase->save()) {
                Log::info(Purchases::class . ' Created purchase : function-createPurchases', ['purchase created' => $purchase]);
                DB::commit();
                $response = new ApiModelResponse('Purchases created successfully', $purchase, 201);
            } else {
                Log::error(Purchases::class . ' Error creating purchase : function-createPurchases', ['purchase data' => $purchaseData]);
                DB::rollBack();
                $response = new ApiModelErrorResponse('Error creating purchase', new Exception(), [], 500);
            }

            return $response;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(Purchases::class . ' Exception creating purchase : function-createPurchases', ['error' => $e->getMessage()]);
            return new ApiModelErrorResponse('Error creating purchase', $e, [], 500);
        }
    }

    public function update(Purchases $purchase, array $updatedArray){
        DB::beginTransaction();

        $purchase->fill($updatedArray);

        if ($purchase->isDirty()) {
            $statusPurchases = $purchase->update();
            
            if ($statusPurchases) {
                DB::commit();
                Log::info(Purchases::class . ' : Successfully updated : function-updatedPurchases', ['purchase' => $statusPurchases]);

                return new ApiModelResponse(
                    'Purchases updated successfully!',
                    $purchase,
                    200
                );
            } else {
                $e = new Exception('Unexpected error');
                DB::rollBack();
                return new ApiModelErrorResponse(
                    'Unable to edit purchase',
                    $e,
                    [],
                    500
                );
            }
        } else {
            Log::info(Purchases::class . ' : No changes made : function-updatedPurchases', ['purchase' => $purchase]);
            DB::commit();
            return new ApiModelResponse(
                'No changes made',
                $purchase,
                200
            );
        }
    }

    public function destroy(Purchases $purchase): ApiModelResponse|ApiModelErrorResponse
    {
        try {
            Log::info(Purchases::class . ' : Deleting Purchases : function-destroy', ['purchase_id' => $purchase->id]);
            DB::beginTransaction();

            $purchase->delete();
            Log::info(Purchases::class . ' : Purchases deleted : function-destroy', ['purchase' => $purchase]);

            DB::commit();
            return new ApiModelResponse(
                'Purchases deleted successfully!',
                $purchase,
                200
            );

        } catch (Exception $e) {
            DB::rollBack();
            Log::error(Purchases::class . ' : Error deleting purchase : function-destroy', ['error' => $e->getMessage()]);
            return new ApiModelErrorResponse(
                'Unable to find purchase with this id',
                $e,
                [],
                400
            );
        }
    }
}
