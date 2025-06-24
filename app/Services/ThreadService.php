<?php
namespace App\Services;

use App\Models\Note;
use App\Models\Mail;
use App\DTOs\ThreadViewDTO;
use Illuminate\Support\Facades\Log;
class ThreadService {
    public function __construct() {}

    public function getByTicketId(array $getThreadItemsDTO): array {
        $ticketId = $getThreadItemsDTO['ticket_id'];
        Log::info("Tickket id " . $ticketId);
        $mailId = $getThreadItemsDTO['mail_id'];

        $mails = Mail::where('in_reply_to', $mailId)
            ->get()
            ->map(fn($mail) => ThreadViewDTO::fromMail($mail));

        $notes = Note::where('ticket_id', $ticketId)
            ->get()
            ->map(fn($note) => ThreadViewDTO::fromNote($note));

        Log::info(json_encode($notes));

        return $mails
            ->concat($notes)
            ->sortBy('created_at')
            ->values()
            ->all();
    }
}
?>