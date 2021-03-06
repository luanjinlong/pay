<?php

//  支付相关 数据库字段的配置

return [
    'sql_type' => 1, // 在数据库中的数字代码
    'symbol' => ['%', '#', '^', '&', '^'], // 使用什么符号拼接
    'rule' => ['k_sort'],// 请求字段的拼接规则
    'encrypt_rule' => ['k_sort'], // 加密规则的配置
    'encrypt_symbol' => ['%', '#', '^', '&', '^'],  // 加密规则的配置
    'test' => ['name'=>'long']
];