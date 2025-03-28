<?php

namespace Weiaibaicai\OperationLog\Http\Controllers;

use Dcat\Admin\Grid;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Layout\Content;
use Weiaibaicai\OperationLog\Models\OperationLog;
use Weiaibaicai\OperationLog\OperationLogServiceProvider;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;
use Dcat\Admin\Admin;


class LogController
{
    public function index(Content $content)
    {
        return $content->title(OperationLogServiceProvider::trans('log.title'))
            ->description(trans('admin.list'))
            ->body($this->grid());
    }

    protected function grid()
    {
        return new Grid(OperationLog::with('user'), function (Grid $grid) {
            $grid->model()->where('app_type', Admin::app()->getName());
            $grid->column('id', 'ID')->sortable();
            $grid->column('user', trans('admin.user'))->display(function ($user) {
                if (!$user) {
                    return;
                }

                $user = Helper::array($user);

                return $user['name'] ?? ($user['username'] ?? $user['id']);
            })->link(function () {
                if ($this->user) {
                    return admin_url('auth/users/' . $this->user['id']);
                }
            });

            $grid->column('method', trans('admin.method'))->label(OperationLog::$methodColors)->filterByValue();

            $grid->column('path', trans('admin.uri'))->display(function ($v) {
                return "<code>$v</code>";
            })->filterByValue();

            $grid->column('ip', 'IP')->filterByValue();

            $grid->column('input')->display(function ($input) {
                $input = json_decode($input, true);

                if (empty($input)) {
                    return;
                }
                $input = Arr::except($input, ['_pjax', '_token', '_method', '_previous_']);

                if (empty($input)) {
                    return;
                }

                return '<pre class="dump" style="max-width: 500px">' . json_encode($input,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            });

            $grid->column('created_at', trans('admin.created_at'));

            $grid->model()->orderBy('id', 'DESC');

            $grid->disableCreateButton();
            $grid->disableQuickEditButton();
            $grid->disableEditButton();
            $grid->disableViewButton();
            if(!OperationLogServiceProvider::setting('enable_grid_delete')) {
                $grid->disableDeleteButton();
                $grid->disableBatchDelete();
            }
            $grid->showColumnSelector();
            $grid->setActionClass(Grid\Displayers\Actions::class);

            $grid->filter(function (Grid\Filter $filter) {
                $userModel = config('admin.database.users_model');

                $filter->in('user_id', trans('admin.user'))->multipleSelect($userModel::pluck('name', 'id'));

                $filter->equal('method', trans('admin.method'))->select(array_combine(OperationLog::$methods,
                    OperationLog::$methods));

                $filter->like('path', trans('admin.uri'));
                $filter->like('input');
                $filter->equal('ip', 'IP');
                $filter->whereBetween('created_at', function ($query) {
                    $start = $this->input['start'] ?? null;
                    $end = $this->input['end'] ?? null;
                    if ($start)
                        $query->where('created_at', ">=", "$start 00:00:00");
                    if ($end)
                        $query->where('created_at', "<=", "$end 23:59:59");
                })->date();
            });
        });
    }

    public function destroy($id)
    {
        $ids = explode(',', $id);

        OperationLog::query()->where('app_type', Admin::app()->getName())->whereIn('id',array_filter($ids))->delete();

        return JsonResponse::make()->success(trans('admin.delete_succeeded'))->refresh()->send();
    }
}
