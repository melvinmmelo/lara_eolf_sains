<?php

if (! function_exists('statusBadge')) {
    function statusBadge($status) {
        $class = ($status === 'Active' or $status === 'Available' or $status == 1) ? 'bg-success' : 'bg-danger';
        $statusDesc = ($status == 1 or $status == 'Active' or $status == 'Available') ? 'Active' : 'Inactive';
        return '<span class="badge ' . $class . '">' . e( ucfirst(strtolower($statusDesc))) . '</span>';
    }
}
