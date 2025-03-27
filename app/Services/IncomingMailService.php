<?php

namespace App\Services;

use App\Models\IncomingMail;
use App\Models\Ticket;
use App\Models\Customer;
use App\DTOs\TicketViewDTO;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Throwable;

class IncomingMailService {

    public function getAll() {
        $tickets = Ticket::with([
            'id',
            'incomingMail',
            'assignedTo',
            'assignedBy',
            'customer',
            'queue'
        ])->latest()->get();
        return TicketViewDTO::fromCollection($tickets);
    }

    public function getById($id) {
        return IncomingMail::with('customer')->findOrFail($id);
    }

    public function createMail(array $data) {
        try {
            $customer = Customer::findOrFail($data['customer_id']);

            $mail = IncomingMail::create([
                'customer_id' => $customer->id,
                'subject' => $data['subject'],
                'body' => $data['body'],
                'message_id'  => $data['message_id'],
            ]);

            Log::info("✅ Incoming mail created (ID: {$mail->id}, Customer ID: {$customer->id})");
            return $mail;
        } catch (ModelNotFoundException $e) {
            Log::error("❌ Customer not found for incoming mail creation.");
            throw new ModelNotFoundException("Customer not found.");
        } catch (Throwable $e) {
            Log::error("❌ Error creating incoming mail: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteMail($id) {
        $mail = IncomingMail::findOrFail($id);
        $mail->delete();

        Log::info("🗑️ Incoming mail soft deleted (ID: {$id})");
        return response()->json(['message' => 'Mail deleted successfully'], 200);
    }
}
