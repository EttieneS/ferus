<?php

namespace App\Services;

use App\Models\Note;
use Illuminate\Support\Facades\Log;

class NoteService {
    public function create(Note $note) {
        Log::error(json_encode($note) . " :Note noteservice");
        
        try {
            $createdNote = Note::create($note->toArray());
            return $result = [
                "status" => "success"
            ];
        } catch (\Exception $e) {            
            \Log::error('Failed to create note: ' . $e->getMessage());
            return $result = [
                "status" => "false",
                "message" => $e->getMessage()
            ];
        }
    }

    
    public function getAllByTicketId(int $id) {
        return Note::where('ticket_id', $id)
            ->with(['user'])
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'body' => $note->body,
                    'created_at' => $note->created_at,
                    'user' => [
                        'id' => $note->user->id ?? null,
                        'name' => $note->user->name ?? null,
                        'surname' => $note->user->surname ?? null,
                    ],
                ];
            });
    }
}