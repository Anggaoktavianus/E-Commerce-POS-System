<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'setting_key',
        'setting_value',
    ];

    // Relationships
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    // Helper methods
    public static function get($outletId, $key, $default = null)
    {
        $setting = self::where('outlet_id', $outletId)
            ->where('setting_key', $key)
            ->first();

        return $setting ? $setting->setting_value : $default;
    }

    public static function set($outletId, $key, $value)
    {
        return self::updateOrCreate(
            ['outlet_id' => $outletId, 'setting_key' => $key],
            ['setting_value' => $value]
        );
    }
}
