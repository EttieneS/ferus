<?php

namespace App\DTOs;

use App\Models\Mail;
use App\Models\Note;
use App\Enums\ThreadType;

class ThreadDTO {
    public string $type;
    public int $id;
    public string $body;
    public string $created_at;
    public array $sender;

    public static function fromMail(Mail $mail): self
    {
        $dto = new self();
        $dto->type = ThreadType::MAIL;
        $dto->id = $mail->id;
        $dto->body = optional($mail->mailBody)->body ?? '';
        $dto->created_at = $mail->created_at->toDateTimeString();
        $dto->sender = $mail->getSenderDetails();

        return $dto;
    }

    public static function fromNote(Note $note): self
    {
        $dto = new self();
        $dto->type = ThreadType::NOTE;
        $dto->id = $note->id;
        $dto->body = $note->body;
        $dto->created_at = $note->created_at->toDateTimeString();
        $dto->sender = [
            'id' => $note->user_id,
            'type' => Mail::USER,
            'full_name' => optional($note->user)->name . ' ' . optional($note->user)->surname,
            'email' => optional($note->user)->email,
        ];

        return $dto;
    }
}
