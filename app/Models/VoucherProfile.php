<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VoucherProfile extends Model
{
    use BelongsToTenant;
    use HasFactory, HasUlids;

    protected $table = 'voucher_profiles';

    protected $fillable = [
        'tenant_id',
        'name',
        'price',
        'validity',
        'rate_limit',
        'shared_users',
        'is_active',
    ];

    protected $casts = [
        'price' => 'integer',
        'shared_users' => 'integer',
        'is_active' => 'boolean',
    ];

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'profile_id');
    }
}
