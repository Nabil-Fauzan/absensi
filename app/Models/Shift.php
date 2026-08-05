<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $fillable = ['name', 'start_time', 'end_time'];

    /**
     * Get the users assigned to this shift.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
