# Argon Bridge

WordPress 无头控制端插件，配合 [Argon-Astro](https://github.com/Luochancy/Argon-Astro) 静态主题使用。装上后继续用 WordPress 后台写文章、发说说、审评论、配主题，前台交由 Astro 静态站点与边缘函数承载。

## 安装与配置

1. **安装插件**
   - 直接在 WordPress 后台「插件 → 安装插件 → 上传插件」上传 `argon-bridge.zip` 并启用；
   - 或将本仓库整个目录放到 `wp-content/plugins/argon-bridge/` 后在后台启用。
2. **导入原版 Argon 设置（可选）**
   - 如果原站正在用 WordPress 版 Argon，打开左侧菜单 **Argon → 备份与导入**，点击 **从现有 Argon 主题选项一键导入**，即可把配色、Banner、侧栏、摘要、过时提示等选项直接迁移过来。
3. **检查站点地址与菜单/小工具**
   - 在 **Argon → 内容与侧栏 → 站点信息与 SEO** 填写 **前端站点地址 (Site URL)**（也可在构建机用环境变量 `SITE_URL` 覆盖）；
   - WordPress 自身的「外观 → 菜单」（`toolbar_menu` / `leftbar_menu` / `leftbar_author_links` / `leftbar_friend_links`）、「链接管理器（友链）」、「外观 → 小工具」（`leftbar-tools` / `rightbar-tools` / `leftbar-siteinfo-extra-tools`）以及「设置 → 常规 → 时区」会自动导出，无需重复设置。
4. **创建应用程序密码**
   - 进入 **用户 → 个人资料 → 应用程序密码**（账号需具备 `edit_posts` 权限），新建一个密码，与用户名、WordPress 站点地址一起填入前端的 `WP_URL`、`WP_USER`、`WP_APP_PASSWORD`。
5. **配置自动重建钩子（可选）**
   - 打开 **Argon → 无头桥接与部署 → CI / 构建部署钩子**，选择你使用的平台（Cloudflare Workers / Pages、GitHub Actions、Vercel、Netlify 或自定义 Webhook）并填入钩子地址。保存设置或发布/修改文章时会自动触发前端重建，也可以随时点击「立即触发一次构建部署」。

## 提供的 REST 接口

命名空间：`argon-astro/v1`（全部接口强制要求登录且具备相应权限，未登录直接拒绝）

| 接口 | 方法 | 用途 |
| --- | --- | --- |
| `/config` | `GET` | 导出主题配置、菜单、友链、分类/标签 slug、站点时区与侧栏小工具 HTML |
| `/posts`、`/pages`、`/shuoshuo` | `GET` | 分页导出公开文章/页面/说说（含阅读量、修改时间、页面模板、固定链接；加密文章绝不导出正文） |
| `/comments` | `GET` / `POST` | 获取与发表评论（含回复邮件通知 `wp_mail`、悄悄话、Markdown、QQ 头像、重复/频率限制标准状态码） |
| `/comments/history` | `GET` | 获取单条公开评论的编辑记录 |
| `/comments/edit`、`/comments/pin` | `POST` | 评论者校验邮箱编辑自己的评论 / 管理员置顶评论 |
| `/comment-upvote`、`/shuoshuo-upvote` | `POST` | 评论与说说点赞 |
| `/views` | `GET` / `POST` | 单篇或批量查询阅读量（兼容 `views` meta 与 Post Views Counter），以及文章阅读量自增 |
| `/post-unlock` | `POST` | 常量时间校验文章密码并返回解密后的文章 HTML |
| `/deploy` | `POST` | 带时间戳、一次性 nonce 与 HMAC-SHA256 签名的构建触发接口 |

## 打包与测试

运行离线 PHP 测试（无需启动完整 WordPress）：

```bash
php tests/signature.php
php tests/bridge.php
```

本地重新打包可直接上传 WordPress 的安装包时，保留顶层 `argon-bridge/` 目录并排除 `.git`、`.github`、`tests` 和 `.gitignore` 即可。

## 许可证

GPL-3.0-or-later，详见 [LICENSE](LICENSE)。
