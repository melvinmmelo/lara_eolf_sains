<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadingTicket extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_no', 'branch_code', 'user_name'];

    // generate ticket no
    public static function generateTicketNo($branchCode)
    {
        $lastTicketNo = LoadingTicket::where('branch_code', $branchCode)->count();

        if ($branchCode == 'EFTO-CAG') {
            $prefix = 'C';
        } else {
            $prefix = 'T';
        }

        return "LT-" . $prefix . str_pad($lastTicketNo + 1, 4, '0', STR_PAD_LEFT);
    }
}
