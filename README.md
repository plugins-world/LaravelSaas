# LaravelSaas

[![Latest Stable Version](http://poser.pugx.org/plugins-world/laravel-saas/v)](https://packagist.org/packages/plugins-world/laravel-saas)
[![Total Downloads](http://poser.pugx.org/plugins-world/laravel-saas/downloads)](https://packagist.org/packages/plugins-world/laravel-saas)
[![Latest Unstable Version](http://poser.pugx.org/plugins-world/laravel-saas/v/unstable)](https://packagist.org/packages/plugins-world/laravel-saas) [![License](http://poser.pugx.org/plugins-world/laravel-saas/license)](https://packagist.org/packages/plugins-world/laravel-saas)
[![PHP Version Require](http://poser.pugx.org/plugins-world/laravel-saas/require/php)](https://packagist.org/packages/plugins-world/laravel-saas)

在最新的 laravel 框架中使用 saas 功能的项目。

依赖项目：
- [插件管理器 fresns/plugin-manager](https://pm.fresns.org/zh-Hans/)
- [应用市场管理器 fresns/market-manager](https://gitee.com/fresns/market-manager)
- [Tenancy 3.x](https://tenancyforlaravel.com/)
- [Laravel](https://laravel.com/)

## 前置要求

- Laravel 9+
- Tenancy 3+
- fresns/plugin-manager ^2
- fresns/market-manager ^1
- fresns/cmd-word-manager ^1
- 项目已完成 fresns/plugin-manager、fresns/market-manager 的安装。点击查看[如何安装插件管理器与应用市场管理器？](https://discuss.plugins-world.cn/post/hYJORaBi)
- 项目已安装 breeze 入门套件

## 安装

1. 安装入门套件
```bash
composer require laravel/breeze --dev

php artisan breeze:install vue # 安装 vue 的入门套件（包含认证、登录等相关逻辑）
php artisan migrate
npm install
npm run build # 构建静态文件

npm run dev # 开发阶段使用
```

2. 安装租户插件
```bash
php artisan market:require plugins-world/laravel-saas # 通过应用市场管理器安装插件

php artisan plugin:activate DcatSaas # 启用插件，否则插件不会被加载

php artisan saas:install # 需要配置数据库的 root 账号密码
```

## 使用

``` php
php artisan saas # 查看当前可以使用的与 saas 相关的指令
php artisan saas:tenant-add --tenant=foo # 添加租户，默认添加名称为 foo 的租户
php artisan saas:tenant-del --tenant=foo # 删除租户，默认删除名称为 foo 的租户
php artisan saas::tenant-list # 当前 saas 列表
php artisan tenants:migrate --tenants=foo # 执行 foo 租户的迁移，开发阶段建议指定租户，部署阶段可不指定，以批量运行租户迁移
php artisan tenants:migrate-rollback --tenants=foo # 回滚 foo 租户的迁移，开发阶段建议指定租户，部署阶段可不指定，以批量运行租户迁移的回滚操作
php artisan ...
```

### 这个包如何被创建的？

`php artisan new Tenant`

Please see [plugin-manager](https://github.com/fresns/plugin-manager) for more information.
