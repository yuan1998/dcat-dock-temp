<?php

namespace Yuan\Admin\GridSortable;

use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid;

class GridSortableServiceProvider extends ServiceProvider
{
    protected $column = '__sortable__';

    protected $js = [
        'js/sortable.min.js',
    ];
	protected $css = [

	];

	public function register()
	{
		//
	}

	public function init()
	{
		parent::init();

        $column = $this->column;

        Grid::macro('sortable', function ($sortName = 'order') use ($column) {
            /* @var $this Grid */

            $this->tools(new SaveOrderButton($sortName));

            if (!request()->has($sortName)) {
                $this->model()->ordered();
            }

            $this->column($column, '排序')
                ->displayUsing(SortableDisplay::class, [$sortName]);
        });

	}
}
