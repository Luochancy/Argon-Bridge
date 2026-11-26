=== Argon Bridge ===
Contributors: argon
Stable tag: 0.2.1
Requires at least: 6.0
Requires PHP: 7.4
License: GPL-3.0-or-later

配合 Argon-Astro 静态主题使用的 WordPress 无头控制端插件。负责主题配置管理、内容/菜单/友链/小工具/时区导出、私有评论/点赞/阅读量/密码文章解锁代理以及 CI 自动重建。

== 安装 ==
1. 上传 `argon-bridge.zip` 并启用插件。
2. 打开左侧菜单「Argon」，可直接一键导入原版 Argon 主题设置，并填写「前端站点地址」。
3. 在「用户 → 个人资料」为具备 `edit_posts` 权限的账号创建应用程序密码。
4. 在前端构建与边缘运行时配置 `ARGON_SOURCE=wordpress`、`SITE_URL`、`WP_URL`、`WP_USER`、`WP_APP_PASSWORD` 与 `ARGON_CAPTCHA_SECRET`。

== REST 接口 ==
命名空间：`argon-astro/v1`（全部需要登录鉴权）
- `GET /config` `/posts` `/pages` `/shuoshuo` `/comments` `/comments/history` `/views`
- `POST /comments` `/comments/edit` `/comments/pin` `/comment-upvote` `/shuoshuo-upvote` `/views` `/post-unlock`
- `POST /deploy`（时间戳 + 一次性 nonce + HMAC-SHA256 签名）

== CI 构建触发 ==
支持 Cloudflare（Workers / Pages）、GitHub Actions（`repository_dispatch`，事件名 `argon-config`）、Vercel、Netlify 及带 HMAC 签名的自定义 Webhook。
