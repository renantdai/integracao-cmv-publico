<?php

namespace App\Repositories;

use App\DTO\CreateCaptureDTO;
use App\Models\Capture;
use App\Repositories\Contracts\IntegrationRepositoryInterface;

use stdClass;

class IntegrationEloquentORM implements IntegrationRepositoryInterface {
    public function __construct(
        protected Capture $model
    ) {
    }

    public function new(CreateCaptureDTO $dto): stdClass {
        $capture = $this->model->create(
            (array) $dto
        );

        return (object) $capture->toArray();
    }

    public function validateStatus(CreateCaptureDTO $dto): bool {
        $plate = '';
        if ($dto->idCam) {
            $plate = $this->model->select('plate')
                ->where([
                    ['idCam', '=', $dto->idCam],
                    ['statusSend', '=', $dto::SENT]
                ])->orderBy('id', 'desc')->first();
        } else {
            $plate = $this->model->select('plate')
                ->where([
                    ['idEquipment', '=', $dto->idEquipment],
                    ['statusSend', '=', $dto::SENT]
                ])->orderBy('id', 'desc')->first();
        }

        if (empty($plate)) {
            return true;
        }

        return ($plate->plate != $dto->plate) ? true : false;
    }

    public function findOne(string $plate): stdClass|null {
        $capture = $this->model->where('plate', '=', $plate)->orderBy('id', 'desc')->first();
        if (!$capture) {
            return null;
        }

        return (object) $capture->toArray();
    }

    public function alterStatusCapture(CreateCaptureDTO $dto, $status): bool {
        return ($this->model->where('id', $dto->id)->update(['statusSend' => $status]));
    }
}
