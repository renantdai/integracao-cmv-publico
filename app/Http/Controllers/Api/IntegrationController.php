<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\DTO\CreateCaptureDTO;
use App\Services\IntegrationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\CaptureResource;
use Exception;
use Illuminate\Support\Facades\Log;

class IntegrationController extends Controller {
    public function __construct(
        protected IntegrationService $service,
    ) {
    }
    /**
     * Store a newly created resource in storage.
     */
    public function capture(Request $request) {
        Log::info('Recebido a requisicao', ['id' => $request->idRegister, 'idCam' => $request->idCam]);

        try {
            $dto = CreateCaptureDTO::makeFromRequest($request);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'possui erro',
                'msg' => 'Houve um erro na criação do DTO'
            ], Response::HTTP_OK);
        }

        $validate = $this->service->validateStatus($dto);
        if (!$validate) {
            return response()->json([
                'error' => 'possui erro',
                'msg' => 'Placa já se encontrada transmitida para o CMV ou não foi enviado a imagem'
            ], Response::HTTP_OK);
        }

        $capture = $this->service->new($dto);
        $dto->id = $capture->id;

        $sent = $this->service->envioLeituraService($dto);

        if (!$sent) {
            return response()->json([
                'error' => 'possui erro',
                'msg' => 'Não foi possivel transmitir para o CMV, a requisição ficará na fila de transmissão'
            ], Response::HTTP_OK);
        }
        $dto->statusSend = $dto::SENT;

        return (new CaptureResource($capture))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        if (!$plate = $this->service->findOne($id)) {
            return response()->json([
                'error' => 'Not Found'
            ], Response::HTTP_NOT_FOUND);
        }

        return new CaptureResource($plate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        //
    }
}
