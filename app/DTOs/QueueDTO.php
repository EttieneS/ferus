<?php

namespace App\DTOs;


use App\Models\Queue;
use Illuminate\Http\Request;

class QueueDTO {
    public string $name;
    public string $mailer;

    public ?string $host;
    public ?int $port;
    public ?string $encryption;
    public ?string $username;
    public ?string $password;
    public ?string $fromName;
    public ?string $fromEmail;

    public ?int $slaId;

    public static function fromRequest(Request $request): self {
        $dto = new self();
        $dto->name = $request->input('name');
        $dto->mailer = $request->input('mailer');

        $dto->host = $request->input('host');
        $dto->port = $request->input('port');
        $dto->encryption = $request->input('encryption');
        $dto->username = $request->input('username');
        $dto->password = $request->input('password');
        $dto->fromName = $request->input('from_name');
        $dto->fromEmail = $request->input('from_email');
        $dto->slaId = $request->input('sla_id');

        return $dto;
    }

    public function toModel(): Queue {
        return new Queue([
            'name' => $this->name,
            'mailer' => $this->mailer,
            'host' => $this->host,
            'port' => $this->port,
            'encryption' => $this->encryption,
            'username' => $this->username,
            'password' => $this->password,
            'from_name' => $this->fromName,
            'from_email' => $this->fromEmail,
            'sla_id' => $this->slaId,
        ]);
    }
}
