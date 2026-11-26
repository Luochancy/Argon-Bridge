# Argon Bridge

WordPress 无头控制端插件，配套 [Argon-Astro](https://github.com/Luochancy/Argon-Astro) 静态博客主题使用，提供主题配置管理、内容与侧栏小工具导出、站点时区同步、私有评论/点赞/阅读量/加密解锁代理与 CI 构建触发。

## 安装

1. 将插件打包为 `argon-bridge.zip`（包含 `argon-bridge.php`、`readme.txt` 与 `includes/`）并在 WordPress 后台「插件 → 安装插件 → 上传插件」启用。
2. 在左侧菜单 **Argon** 中配置外观、内容（含前端站点地址）与无头部署，支持从原版 Argon 一键导入现有设置。
3. 在「用户 → 个人资料」为具备 `edit_posts` 权限的账号创建应用程序密码，填入前端构建与 Serverless 运行时环境变量（`WP_URL`、`WP_USER`、`WP_APP_PASSWORD`）。

## REST 接口

命名空间：`argon-astro/v1`（全部需要登录鉴权）

- `GET /config`（含菜单、友链、分类/标签 slug、站点时区与 `leftbar-tools`/`rightbar-tools`/`leftbar-siteinfo-extra-tools` 小工具 HTML）
- `GET /posts`、`/pages`、`/shuoshuo`（含阅读量、修改时间、密码提示、页面模板与自定义固定链接）
- `GET /comments`、`/comments/history`；`POST /comments`（含回复邮件通知）、`/comments/edit`、`/comments/pin`
- `POST /comment-upvote`、`/shuoshuo-upvote`
- `GET /views`（支持单篇 `?id=` 与批量 `?ids=`，兼容 `views` meta 与 Post Views Counter）、`POST /views`
- `POST /post-unlock`（常量时间校验密码并解锁受密码保护文章）
- `POST /deploy`（需时间戳、一次性 nonce 与 HMAC-SHA256 签名）

## CI 构建触发

支持 GitHub Actions（`repository_dispatch`，事件名 `argon-config`）、Cloudflare（Workers / Pages）、Vercel、Netlify 及带 `X-Argon-Signature` 签名的自定义 Webhook。

## 测试

```bash
php tests/signature.php
php tests/bridge.php
```

## 许可证

GPL-3.0-or-later，详见 [LICENSE](LICENSE)。
