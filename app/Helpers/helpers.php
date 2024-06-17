<?php

if (! function_exists('statusBadge')) {
    function statusBadge($status) {
        $status = strtolower($status);
        $class = ($status === 'active' or $status === 'available' or $status == 1) ? 'bg-success' : 'bg-danger';

        if($status == 'added'){
            $statusDesc = 'Assigned';
        }
        else{
            $statusDesc = ($status == 1 or $status == 'active' or $status == 'available') ? 'Active' : 'Inactive';
        }


        return '<span class="badge ' . $class . '">' . e( ucfirst($statusDesc)) . '</span>';
    }
}
