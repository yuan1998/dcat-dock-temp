<?php

use Yuan\Admin\GridSortable\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('grid-sortable', Controllers\GridSortableController::class.'@index');
Route::post('extension/grid-sort', Controllers\GridSortableController::class.'@sort')
    ->name('yuan-admin-grid-sortable');
