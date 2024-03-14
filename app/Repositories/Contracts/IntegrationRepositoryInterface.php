<?php

namespace App\Repositories\Contracts;

use App\DTO\{
    CreateCaptureDTO
};
use stdClass;

interface IntegrationRepositoryInterface {
    public function new(CreateCaptureDTO $dto): stdClass;
    public function validateStatus(CreateCaptureDTO $dto): bool;
    public function findOne(string $id): stdClass|null;
    public function alterStatusCapture(CreateCaptureDTO $dto, $status);
}
