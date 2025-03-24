<?php

namespace Weiaibaicai\OperationLog;

use Dcat\Admin\Extend\Setting as Form;

class Setting extends Form
{
    public function form()
    {
        $this->display("路由")->value('auth/operation-logs');
        $this->radio('enable_remove', '定时删除')->required()
            ->options([
                0 => '关闭',
                1 => '开启'
            ])
            ->when(1, function ($form) {
                $form->number('remove_day', '保留天数')->min(1)->default(30);
            });
        $this->switch('enable_grid_delete', '允许手动删除日志');
    }
}
