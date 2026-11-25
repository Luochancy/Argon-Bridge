=== Argon Bridge ===
Contributors: argon
Stable tag: 0.1.0
License: GPL-3.0-or-later

WordPress 无头控制端插件，为 Argon 静态站点提供配置管理、内容导出、私有评论代理与 CI 构建触发。

== 安装 ==
1. 上传 `argon-bridge.zip` 并启用插件。
2. 在左侧菜单「Argon」中管理主题设置，支持一键导入现有 Argon 配置。
3. 在「用户 → 个人资料」为具有 `edit_posts` 权限的账号创建应用程序密码。

== 环境变量 ==
构建机设置 `WP_URL`、`WP_USER`、`WP_APP_PASSWORD`。

== REST 接口 ==
命名空间：`argon-astro/v1`（全部需要登录鉴权）
- `GET /config` `/posts` `/pages` `/shuoshuo` `/comments`
- `POST /comments` `/comments/edit` `/comments/pin` `/comment-upvote` `/shuoshuo-upvote`
- `POST /deploy`（需时间戳、一次性 nonce 与 HMAC-SHA256 签名）

== CI 触发 ==
支持 GitHub Actions（`repository_dispatch`，事件名 `argon-config`）、Cloudflare（Workers / Pages）、Vercel、Netlify 及带 HMAC 签名的自定义 Webhook。
