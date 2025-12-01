<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'generic_name',
        'description',
        'manufacturer',
        'price',
        'quantity_in_stock',
        'minimum_stock_level',
        'unit',
        'expiry_date',
        'batch_number',
        'status'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'price' => 'decimal:2'
    ];

    /**
     * Check if medicine is expired
     */
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if medicine is low in stock
     */
    public function isLowStock()
    {
        return $this->quantity_in_stock <= $this->minimum_stock_level;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass()
    {
        if ($this->isExpired()) {
            return 'status-expired';
        }

        if ($this->isLowStock()) {
            return 'status-low-stock';
        }

        return match ($this->status) {
            'active' => 'status-active',
            'inactive' => 'status-inactive',
            'expired' => 'status-expired',
            default => 'status-active'
        };
    }

    /**
     * Get status text
     */
    public function getStatusText()
    {
        if ($this->isExpired()) {
            return 'منتهي الصلاحية';
        }

        if ($this->isLowStock()) {
            return 'مخزون منخفض';
        }

        return match ($this->status) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'expired' => 'منتهي الصلاحية',
            default => 'نشط'
        };
    }

    /**
     * Scope for active medicines
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for low stock medicines
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity_in_stock <= minimum_stock_level');
    }

    /**
     * Scope for expired medicines
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }
}
