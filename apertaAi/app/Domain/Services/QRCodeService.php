<?php

namespace App\Domain\Services;

use App\Http\Responses\ApiModelErrorResponse;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Decoder\QrReader;
use Illuminate\Container\Attributes\Storage;
use Illuminate\Http\JsonResponse;
use Throwable;

class QRCodeService
{
    public function readQrCode($file)
    {
        try{
            $file = $file['file'];

            $path = $file->getPath();

            $file_path = $file->store('qr-codes', 'public');
            $path = storage_path('app/public/' . $file_path);

            $options = new QROptions([
                // tenta usar Imagick se disponível
                // 'readerUseImagickIfAvailable' => true,
                // melhora contraste / escala de cinza
                'readerGrayscale'            => true,
                'readerIncreaseContrast'     => true,
            ]);

            $qrcode = new QRCode($options);

            $result = $qrcode->readFromFile($path); 

            $conteudo = $result->data;

            return new JsonResponse([
                'message' => 'QR code lido com sucesso',
                'data' => $conteudo
            ], 200);
        }
        catch(Throwable $e){
            return new ApiModelErrorResponse(
                'Erro ao ler o QR code',
                 $e,
                [],
                500
            );
        }
    }

}