#!/usr/bin/env sh
# 注意, 仅在测试环境使用！！！
# 项目新建\删除类后, 请运行此脚本

cd "$(dirname "$0")"
composer dumpautoload
php artisan clear-compiled
php artisan optimize
php artisan view:clear
cd -
