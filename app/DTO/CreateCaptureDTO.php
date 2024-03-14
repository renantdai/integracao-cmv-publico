<?php


namespace App\DTO;

class CreateCaptureDTO {
    const RECEBIDO = 1;
    const SENT = 2;
    const ERROR = 3;

    public function __construct(
        public $id,
        public $idRegister,
        public $captureDateTime, //yyyy-MM-dd HH:mm:ss
        public string $plate,
        public string $idEquipment,
        public $idCam,
        public $nameCam,
        public $latitude,
        public $longitude,
        public $image, //Base64
        public $fileName,
        public $statusSend
    ) {
    }

    public static function makeFromRequest($request): self {
        return new self(
            $request->id,
            $request->idRegister,
            str_replace(' ', 'T', $request->captureDateTime),
            $request->plate,
            $request->idEquipment,
            $request->idCam,
            self::getNameCam($request->idCam),
            $request->latitude,
            $request->longitude,
            $request->image,
            str_replace(array('.', '-', '/', ':',' '), "", $request->captureDateTime) . '-' . $request->plate . '.jpg',
            self::RECEBIDO
        );
    }

    /**
     * Função responsavel por retornar o nome do equipamento através do idedentificado da camera cadastrada junto ao CMV
     *
     * @param string $idCam
     * @return string
     */
    public static function getNameCam($idCam) : string {
        $cams = [
            '01' => 'Entrada da cidade pela ponte Tramandai - Imbe utilizando a faixa da direita',
            '13' => 'Entrada da cidade pela ponte Tramandai - Imbe utilizando a faixa da esquerda',
            '14' => 'Saida do municipio de Imbe utilizando a via beira mar no bairro imara',
            '15' => 'Entrada do municipio de Imbe utilizando a via beira mar no bairro imara',
            '16' => 'Saida do municipio de Imbe utilizando a RS-786 no bairro imara',
            '17' => 'Entrada do municipio de Imbe utilizando a RS-786 no bairro imara'
        ];

        return $cams[$idCam];
    }
}
