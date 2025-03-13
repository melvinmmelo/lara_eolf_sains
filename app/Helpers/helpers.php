<?php

if (!function_exists('statusBadge')) {
    function statusBadge($status)
    {
        $status = strtolower($status);
        $class = ($status === 'active' or $status === 'available' or $status === 1) ? 'bg-success' : 'bg-danger';

        if ($status === 'added') {
            $statusDesc = 'Assigned';
        } elseif ($status == 'stop selling') {
            $statusDesc = 'Stop Selling';
        } else {
            $statusDesc = ($status === 1 or $status === 'active' or $status === 'available') ? 'Active' : 'Inactive';
        }

        return '<span class="badge ' . $class . '">' . e(ucfirst($statusDesc)) . '</span>';
    }
}

if (!function_exists('statusEmployeeBadge')) {
    function statusEmployeeBadge($status)
    {
        $status = strtolower($status);
        $class = ($status === 'active') ? 'bg-success' : 'bg-danger';

        $statusDesc = ($status === 'active') ? 'Active' : 'Resigned';

        return '<span class="badge ' . $class . '">' . e(ucfirst($statusDesc)) . '</span>';
    }
}

// format number with comma and decimal
if (!function_exists('formatNumber')) {
    function formatNumber($number)
    {
        return number_format($number, 2, '.', ',');
    }
}

function getTotalOfProducts($products)
{
    $total = 0;

    if ($products) {
        foreach ($products as $product) {
            $total += $product['quantity'] * $product['price'];
        }
    }
    return $total;
}

function getSummaryOfProducts($products)
{

    $summary = [];

    foreach ($products as $product) {
        $ptypeCode = $product['ptype_code'];
        if (isset($summary[$ptypeCode])) {
            $summary[$ptypeCode]['total'] += $product['quantity'];
        } else {
            $summary[$ptypeCode] = ['ptype_code' => $ptypeCode, 'total' => $product['quantity'], 'price' => $product['price'], 'order' => $product['order']];
        }
    }


    return $summary;
}