<?php
use \think\facade\Route;

Route::get('member/:id', function () {
    return 'hello,ThinkPHP6!';
});