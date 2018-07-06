<?php
    use think\Route;
    // 注册路由到index模块的News控制器的read操作
    Route::rule('new/:id','index/News/read');
    Route::get('new/:id','News/read'); // 定义GET请求路由规则
    Route::post('new/:id','News/update'); // 定义POST请求路由规则
    Route::put('new/:id','News/update'); // 定义PUT请求路由规则
    Route::delete('new/:id','News/delete'); // 定义DELETE请求路由规则
    Route::any('new/:id','News/read'); // 所有请求都支持的路由规则