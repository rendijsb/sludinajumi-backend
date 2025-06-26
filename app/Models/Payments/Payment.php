<?php

namespace App\Models\Payments;

use App\Models\Advertisements\Advertisement;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const ID = 'id';
    public const USER_ID = 'user_id';
    public const ADVERTISEMENT_ID = 'advertisement_id';
    public const AMOUNT = 'amount';
    public const CURRENCY = 'currency';
    public const STATUS = 'status';
    public const TYPE = 'type';
    public const PAYMENT_METHOD = 'payment_method';
    public const TRANSACTION_ID = 'transaction_id';
    public const PAYMENT_DATA = 'payment_data';
    public const COMPLETED_AT = 'completed_at';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    const TYPE_ADVERTISEMENT = 'advertisement';
    const TYPE_FEATURED = 'featured';

    protected $fillable = [
        self::USER_ID,
        self::ADVERTISEMENT_ID,
        self::AMOUNT,
        self::CURRENCY,
        self::STATUS,
        self::TYPE,
        self::PAYMENT_METHOD,
        self::TRANSACTION_ID,
        self::PAYMENT_DATA,
        self::COMPLETED_AT,
    ];

    protected function casts(): array
    {
        return [
            self::AMOUNT => 'decimal:2',
            self::PAYMENT_DATA => 'array',
            self::COMPLETED_AT => 'datetime',
        ];
    }

    public function userRelation(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advertisementRelation(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function markAsCompleted(): void
    {
        $this->update([
            self::STATUS => self::STATUS_COMPLETED,
            self::COMPLETED_AT => now(),
        ]);
    }
}
