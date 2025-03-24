<?php

namespace Yuan\Admin\GridSortable\Http\Controllers;

use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
use Dcat\Admin\Traits\HasFormResponse;
use Illuminate\Routing\Controller;
use Dcat\Admin\Form;
use Illuminate\Http\Request;

class GridSortableController extends Controller
{
    use HasFormResponse;

    public function index(Content $content)
    {
        return $content
            ->title('Title')
            ->description('Description')
            ->body(Admin::view('yuan.grid-sortable::index'));
    }

    public function sort(Request $request)
    {
        $status = true;
        $column = $request->get('_column','order');
        $message = trans('admin.save_succeeded');
        $repository = $request->get('_model');

        $sorts = $request->get('_sort');
        $sorts = collect($sorts)
            ->pluck('key')
            ->combine(
                collect($sorts)->pluck('sort')->sort()
            )->map(function ($val ,$key) use ($column) {
                return [
                    'id' =>$key,
                    $column => $val
                ];
            })->values()->toArray();

        try {
            \Batch::update(new $repository , $sorts , 'id');

        } catch (\Exception $exception) {
            $status = false;
            $message = $exception->getMessage();
        }

        return $this->response()->errorIf(!$status, $message)->successIf($status, $message)->refreshIf(!$status);
    }
}
