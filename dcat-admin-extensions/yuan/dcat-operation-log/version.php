<?php

return [
    '1.0.0' => [
        '首次提交',
        'create_admin_operation_log_table.php',
    ],
    '1.0.1' => [
        '修复执行命令 php artisan views:cache 报无 views 目录问题',
    ],
    '2.0.0' => [
        '实现多商户数据隔离,支持使用后台配置控制日志路由',
        'add_app_type_to_admin_operation_log_table.php',
    ],
    '2.0.1' => [
        '添加Input 筛选',
        "添加定时删除日志",
        '添加手动添加日志Method',
    ]
];
