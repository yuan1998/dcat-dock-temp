<?php

namespace Yuan\Admin\GridSortable;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Tools\AbstractTool;

class SaveOrderButton extends AbstractTool
{
    protected $sortColumn;

    public function __construct($column)
    {
        $this->sortColumn = $column;
    }

    protected function script()
    {
        $route = admin_route('yuan-admin-grid-sortable');


        $class = get_class($this->parent->model()->repository()->model());
        $class = str_replace('\\', '\\\\', $class);

        $script = <<<JS
$('.grid-save-order-btn').click(function () {
    $.post({
        url:'{$route}',
        data: {
            _model: '{$class}',
            _sort: $(this).data('sort'),
            _column: '{$this->sortColumn}'
        }
    }).then((response) => {
        Dcat.handleJsonResponse(response);
        console.log('response :', response);
    });
});

JS;
        Admin::script($script);
    }

    public function render()
    {
        $this->script();

        $text = "保存顺序";

        return <<<HTML
<button type="button" class="btn btn-primary btn-custom grid-save-order-btn" style="margin-left:8px;display:none;">
    <i class="fa fa-save"></i><span class="hidden-xs">&nbsp;&nbsp;{$text}</span>
</button>
HTML;
    }
}
