<?php
namespace App\Services;

use App\Models\Note;
use App\Models\Mail;
use App\DTOs\ThreadDTO;

class ThreadService {
    public function __construct() {}

    public function getMailsAndNotesByTicketId(array $getThreadItemsDTO): array {
        $ticketId = $getThreadItemsDTO['ticketId'];
        $mailId = $getThreadItemsDTO['mailId'];

        $mails = Mail::where('in_reply_to', $mailId)
            ->get()
            ->map(fn($mail) => ThreadDTO::fromMail($mail));

        $notes = Note::where('ticket_id', $ticketId)
            ->get()
            ->map(fn($note) => ThreadDTO::fromNote($note));

        return $mails
            ->concat($notes)
            ->sortBy('created_at')
            ->values();
    }
}
?>