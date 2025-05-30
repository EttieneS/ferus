<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
class Mail extends Model {
    use SoftDeletes;

    const USER = 0;
    const CUSTOMER = 1;

    protected $fillable = [
        'ticket_id',
        'sender_id',
        'sender_type',
        'to_users',
        'cc_users',
        'to_customers',
        'cc_customers',
        'in_reply_to',
    ];

    protected $casts = [
        'to_users' => 'array',
        'cc_users' => 'array',
        'to_customers' => 'array',
        'cc_customers' => 'array',
        'in_reply_to' => 'int'
    ];

    public function ticket() {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function getSenderDetails(): array {
        return match ($this->sender_type) {
            self::USER => $this->formatUser(User::find($this->sender_id)),
            self::CUSTOMER => $this->formatCustomer(Customer::find($this->sender_id)),
            default => null,
        };
    }
    
    private function formatUser(?User $user): ?array {
        if (!$user) {
            return null;
        }

        return [
            'id' => $user->id,
            'type' => self::USER,
            'full_name' => trim($user->name . ' ' . $user->surname),
            'email' => $user->email,
        ];
    }

    private function formatCustomer(?Customer $customer): ?array {
        if (!$customer) {
            return null;
        }

        return [
            'id' => $customer->id,
            'type' => self::CUSTOMER,
            'full_name' => $customer->full_name,
            'email' => $customer->email,
        ];
    }

    public function mailBody() {
        return $this->hasOne(MailBody::class);
    }

    public function ccUsers(): Collection {
        return User::whereIn('id', $this->cc_users ?? [])->get();
    }

    public function toUsers(): Collection {
        return User::whereIn('id', $this->to_users ?? [])->get();
    }

    public function ccCustomers(): Collection {
        return Customer::whereIn('id', $this->cc_customers ?? [])->get();
    }
    
    public function toCustomers(): Collection {
        return Customer::whereIn('id', $this->to_customers ?? [])->get();
    }

    public function inReplyTo(): Collection {
        return Mail::where('in_reply_to', $this->id)->get();
    }
}
