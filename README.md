# Argon Bridge

WordPress 无头控制端插件，配套 [Argon-Astro](https://github.com/Luochancy/Argon-Astro) 静态博客主题使用，提供主题配置管理、内容导出、私有评论/点赞代理与 CI 构建触发。

## 安装

1. 将插件打包为 `argon-bridge.zip`（包含 `argon-bridge.php`、`readme.txt` 与 `includes/`）并在 WordPress 后台「插件 → 安装插件 → 上传插件」启用。
2. 在左侧菜单 **Argon** 中配置外观、内容与无头部署，支持从原版 Argon 一键导入现有设置。
3. 在「用户 → 个人资料」为具备 `edit_posts` 权限的账号创建应用程序密码，填入前端构建与 Serverless 运行时环境变量（`WP_URL`、`WP_USER`、`WP_APP_PASSWORD`）。

## REST 接口

命名空间：`argon-astro/v1`（全部需要登录鉴权）

- `GET /config`、`/posts`、`/pages`、`/shuoshuo`、`/comments`
- `POST /comments`、`/comments/edit`、`/comments/pin`、`/comment-upvote`、`/shuoshuo-upvote`
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
