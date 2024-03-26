# LaravelSaas

[![Latest Stable Version](http://poser.pugx.org/plugins-world/laravel-saas/v)](https://packagist.org/packages/plugins-world/laravel-saas)
[![Total Downloads](http://poser.pugx.org/plugins-world/laravel-saas/downloads)](https://packagist.org/packages/plugins-world/laravel-saas)
[![Latest Unstable Version](http://poser.pugx.org/plugins-world/laravel-saas/v/unstable)](https://packagist.org/packages/plugins-world/laravel-saas) [![License](http://poser.pugx.org/plugins-world/laravel-saas/license)](https://packagist.org/packages/plugins-world/laravel-saas)
[![PHP Version Require](http://poser.pugx.org/plugins-world/laravel-saas/require/php)](https://packagist.org/packages/plugins-world/laravel-saas)

在最新的 laravel 框架中使用 saas 功能的项目。

依赖项目：
- [插件管理器 fresns/plugin-manager](https://pm.fresns.org/zh-Hans/)
- [应用市场管理器 plugins-world/market-manager](https://github.com/plugins-world/MarketManager)
- [Tenancy 3.x](https://tenancyforlaravel.com/)
- [Laravel](https://laravel.com/)

## 前置要求

- Laravel 9+
- Tenancy 3+
- fresns/plugin-manager ^2
- fresns/market-manager ^1
- fresns/cmd-word-manager ^1
- 项目已完成 plugins-world/market-manager 的安装。点击查看[Laravel 插件管理器，安装指南](https://discuss.plugins-world.cn/post/9S19kdNL)


## 安装

1. 在插件管理后台安装并启用租户插件
2. 初始化插件

```bash
php artisan saas:install # 需要配置数据库的 root 账号密码
```


## 命令行

``` php
php artisan saas # 查看当前可以使用的与 saas 相关的指令
php artisan saas:tenant-add --tenant=foo # 添加租户，默认添加名称为 foo 的租户
php artisan saas:tenant-del --tenant=foo # 删除租户，默认删除名称为 foo 的租户
php artisan saas:tenant-list # 当前 saas 列表
php artisan tenants:migrate --tenants=foo # 执行 foo 租户的迁移，开发阶段建议指定租户，部署阶段可不指定，以批量运行租户迁移
php artisan tenants:migrate-rollback --tenants=foo # 回滚 foo 租户的迁移，开发阶段建议指定租户，部署阶段可不指定，以批量运行租户迁移的回滚操作
php artisan ...
```


## 使用

参考 [tencentforlaravel](https://doc.wyz.xyz/pages/30ee05/) 翻译文档
