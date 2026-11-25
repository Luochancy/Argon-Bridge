<?php
/**
 * Tab 5: Headless Bridge & Deployment (AI Agent Discovery, Headless Comments, CI Hooks, Manual Deploy Log).
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the form-bound cards for the Headless Bridge & Deploy tab.
 *
 * @param array<string, mixed> $ctx View context from argon_astro_render_settings().
 * @return void
 */
function argon_astro_render_tab_headless_form(array $ctx) {
    $agent = $ctx['agent'];
    $hooks = $ctx['hooks'];
    $secret = (string) $ctx['secret'];
    $auto_approve = $ctx['auto_approve'];

    $agent_toggles = array(
        'enabled' => '启用智能体发现接口（总开关）',
        'robotsTxt' => '构建时生成 /robots.txt',
        'llmsTxt' => '构建时生成 /llms.txt 与 /llms-full.txt',
        'markdownMirrors' => '启用文章、页面与说说的 Markdown 内容协商（Accept: text/markdown）',
        'jsonIndex' => '构建时生成 /agent-index.json 结构化索引',
        'jsonLd' => '输出 Schema.org JSON-LD 结构化数据',
        'webMcp' => '注册浏览器 WebMCP 工具与 window.__ARGON_AGENT__',
        'allowTrainingCrawlers' => '在自动生成的 robots.txt 中允许 AI 训练爬虫',
    );
    $custom_robots = isset($agent['customRobotsTxt']) ? (string) $agent['customRobotsTxt'] : '';
    $custom_llms = isset($agent['customLlmsTxt']) ? (string) $agent['customLlmsTxt'] : '';
    $has_custom_agent_docs = ($custom_robots !== '' || $custom_llms !== '');

    $gh_repo = isset($hooks['github_repo']) ? (string) $hooks['github_repo'] : '';
    $gh_token = isset($hooks['github_token']) ? (string) $hooks['github_token'] : '';
    $cf_url = isset($hooks['cloudflare']) ? (string) $hooks['cloudflare'] : '';
    $vercel_url = isset($hooks['vercel']) ? (string) $hooks['vercel'] : '';
    $netlify_url = isset($hooks['netlify']) ? (string) $hooks['netlify'] : '';
    $generic_url = isset($hooks['generic']) ? (string) $hooks['generic'] : '';

    $ci_providers = array(
        'none' => '不启用',
        'github' => 'GitHub Actions',
        'cloudflare' => 'Cloudflare (Workers / Pages)',
        'vercel' => 'Vercel',
        'netlify' => 'Netlify',
        'generic' => '自定义 Webhook',
    );
    $active_provider = isset($hooks['provider']) && array_key_exists((string) $hooks['provider'], $ci_providers)
        ? (string) $hooks['provider']
        : '';
    if ($active_provider === '') {
        if ($gh_repo !== '' || $gh_token !== '') {
            $active_provider = 'github';
        } elseif ($cf_url !== '') {
            $active_provider = 'cloudflare';
        } elseif ($vercel_url !== '') {
            $active_provider = 'vercel';
        } elseif ($netlify_url !== '') {
            $active_provider = 'netlify';
        } elseif ($generic_url !== '') {
            $active_provider = 'generic';
        } else {
            $active_provider = 'none';
        }
    }
    ?>
    <div class="argon-tab-panel" data-tab="headless" id="tab-headless">
        <section class="argon-astro-section" id="sec-agent" data-card-title="AI 智能体浏览支持">
            <div class="argon-card-head">
                <h2>AI 智能体浏览支持</h2>
            </div>
            <div class="argon-toggle-stack">
                <?php foreach ($agent_toggles as $akey => $alabel) : ?>
                    <?php $aval = isset($agent[$akey]) ? (bool) $agent[$akey] : true; ?>
                    <label class="argon-toggle-row">
                        <input type="hidden" name="argon_astro_config[agent][<?php echo esc_attr($akey); ?>]" value="0" />
                        <input type="checkbox" name="argon_astro_config[agent][<?php echo esc_attr($akey); ?>]" value="1" <?php checked($aval, true); ?> />
                        <span><?php echo esc_html($alabel); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <details style="margin-top:14px;"<?php echo $has_custom_agent_docs ? ' open' : ''; ?>>
                <summary style="cursor:pointer;font-weight:600;color:#334155;">自定义 robots.txt / llms.txt（留空自动生成）</summary>
                <div class="argon-ci-pair" style="margin-top:12px;">
                    <div class="argon-ci-field">
                        <label for="argon_custom_robots_txt">自定义 robots.txt</label>
                        <textarea id="argon_custom_robots_txt" name="argon_astro_config[agent][customRobotsTxt]" rows="6" class="large-text code" placeholder="User-agent: *&#10;Allow: /"><?php echo esc_textarea($custom_robots); ?></textarea>
                    </div>
                    <div class="argon-ci-field">
                        <label for="argon_custom_llms_txt">自定义 llms.txt</label>
                        <textarea id="argon_custom_llms_txt" name="argon_astro_config[agent][customLlmsTxt]" rows="6" class="large-text code" placeholder="# Site Name&#10;&#10;&gt; Summary..."><?php echo esc_textarea($custom_llms); ?></textarea>
                    </div>
                </div>
            </details>
            <p class="description" style="margin-top:10px;">关闭对应开关后不会在构建产物中输出该文件，可直接在 Serverless 环境变量（<code>ARGON_ROBOTS_TXT</code>、<code>ARGON_LLMS_TXT</code>、<code>ARGON_LLMS_FULL_TXT</code>）中注入；密码保护文章正文不会输出到任何智能体接口。</p>
        </section>

        <section class="argon-astro-section" id="sec-headless-comments" data-card-title="无头评论桥接">
            <div class="argon-card-head">
                <h2>无头评论桥接</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label>评论审核策略</label></th>
                        <td>
                            <input type="hidden" name="argon_astro_auto_approve" value="0" />
                            <label>
                                <input type="checkbox" name="argon_astro_auto_approve" value="1" <?php checked($auto_approve, true); ?> />
                                自动通过来自前台的评论（跳过人工审核队列）
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="argon-astro-section" id="sec-ci-hooks" data-card-title="CI / 构建部署钩子">
            <div class="argon-card-head">
                <h2>CI / 构建部署钩子</h2>
            </div>

            <div class="argon-ci-selector" id="argon_ci_provider_selector" role="radiogroup" aria-label="选择 CI 部署平台">
                <?php foreach ($ci_providers as $pkey => $plabel) : ?>
                    <label class="argon-ci-choice<?php echo $active_provider === $pkey ? ' is-selected' : ''; ?>">
                        <input type="radio" name="argon_astro_hooks[provider]" value="<?php echo esc_attr($pkey); ?>" <?php checked($active_provider, $pkey); ?> />
                        <span><?php echo esc_html($plabel); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="argon-ci-stage" id="argon_ci_stage">
                <div class="argon-ci-panel argon-ci-empty<?php echo $active_provider === 'none' ? ' is-active' : ''; ?>" data-ci-panel="none">
                    未启用自动构建钩子。
                </div>

                <div class="argon-ci-panel<?php echo $active_provider === 'github' ? ' is-active' : ''; ?>" data-ci-panel="github">
                    <div class="argon-ci-panel-sub" style="margin-bottom:12px;">通过 <code>repository_dispatch</code>（事件 <code>argon-config</code>）触发工作流。</div>
                    <div class="argon-ci-pair">
                        <div class="argon-ci-field">
                            <label for="argon_hook_github_repo">Repository (owner/repo)</label>
                            <input id="argon_hook_github_repo" type="text" name="argon_astro_hooks[github_repo]" value="<?php echo esc_attr($gh_repo); ?>" placeholder="your-org/your-blog" />
                        </div>
                        <div class="argon-ci-field">
                            <label for="argon_hook_github_token">Personal Access Token (PAT)</label>
                            <div class="argon-secret-input">
                                <input id="argon_hook_github_token" type="password" name="argon_astro_hooks[github_token]" value="<?php echo esc_attr($gh_token); ?>" placeholder="ghp_... / github_pat_..." autocomplete="new-password" />
                                <button type="button" class="button button-small argon-toggle-secret" data-target="argon_hook_github_token">显示</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="argon-ci-panel<?php echo $active_provider === 'cloudflare' ? ' is-active' : ''; ?>" data-ci-panel="cloudflare">
                    <div class="argon-ci-field">
                        <label for="argon_hook_cloudflare">Cloudflare Workers Builds / Pages Deploy Hook URL</label>
                        <input id="argon_hook_cloudflare" type="text" name="argon_astro_hooks[cloudflare]" value="<?php echo esc_attr($cf_url); ?>" placeholder="https://api.cloudflare.com/client/v4/..." />
                    </div>
                </div>

                <div class="argon-ci-panel<?php echo $active_provider === 'vercel' ? ' is-active' : ''; ?>" data-ci-panel="vercel">
                    <div class="argon-ci-field">
                        <label for="argon_hook_vercel">Vercel Deploy Hook URL</label>
                        <input id="argon_hook_vercel" type="text" name="argon_astro_hooks[vercel]" value="<?php echo esc_attr($vercel_url); ?>" placeholder="https://api.vercel.com/v1/integrations/deploy/..." />
                    </div>
                </div>

                <div class="argon-ci-panel<?php echo $active_provider === 'netlify' ? ' is-active' : ''; ?>" data-ci-panel="netlify">
                    <div class="argon-ci-field">
                        <label for="argon_hook_netlify">Netlify Build Hook URL</label>
                        <input id="argon_hook_netlify" type="text" name="argon_astro_hooks[netlify]" value="<?php echo esc_attr($netlify_url); ?>" placeholder="https://api.netlify.com/build_hooks/..." />
                    </div>
                </div>

                <div class="argon-ci-panel<?php echo $active_provider === 'generic' ? ' is-active' : ''; ?>" data-ci-panel="generic">
                    <div class="argon-ci-panel-sub" style="margin-bottom:12px;">向自定义地址发送 JSON POST，附带 <code>X-Argon-Signature</code>（HMAC-SHA256）请求头。</div>
                    <div class="argon-ci-pair">
                        <div class="argon-ci-field">
                            <label for="argon_hook_generic">自定义 Webhook URL</label>
                            <input id="argon_hook_generic" type="text" name="argon_astro_hooks[generic]" value="<?php echo esc_attr($generic_url); ?>" placeholder="https://ci.example.com/hooks/argon-build" />
                        </div>
                        <div class="argon-ci-field">
                            <label for="argon_hook_secret">HMAC 签名密钥 (<code>X-Argon-Signature</code>)</label>
                            <div class="argon-secret-input">
                                <input id="argon_hook_secret" type="password" name="argon_astro_hook_secret" value="<?php echo esc_attr($secret); ?>" placeholder="留空不附加签名，或点击随机生成" autocomplete="new-password" />
                                <button type="button" class="button button-small argon-toggle-secret" data-target="argon_hook_secret">显示</button>
                                <button type="button" class="button button-small" id="argon_gen_hook_secret">随机生成</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <?php
}

/**
 * Render the standalone Manual Deploy & Webhook Delivery Log card (outside the main form to avoid nested forms).
 *
 * @param array<string, mixed> $ctx View context from argon_astro_render_settings().
 * @return void
 */
function argon_astro_render_tab_headless_extra(array $ctx) {
    $log = $ctx['log'];
    ?>
    <div class="argon-tab-extra" data-tab-extra="headless">
        <section class="argon-astro-section" id="sec-deploy-log" data-card-title="手动触发部署与投递日志">
            <div class="argon-card-head">
                <h2>手动触发部署与投递日志</h2>
            </div>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-bottom:18px;">
                <input type="hidden" name="action" value="argon_astro_manual_deploy" />
                <?php wp_nonce_field('argon_astro_manual_deploy'); ?>
                <?php submit_button('立即触发一次构建部署', 'secondary', 'argon_astro_trigger_deploy', false); ?>
            </form>
            <?php if (empty($log)) : ?>
                <p class="description">暂无 Webhook 投递记录。</p>
            <?php else : ?>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th>时间 (UTC)</th>
                            <th>触发原因</th>
                            <th>目标</th>
                            <th>HTTP 状态</th>
                            <th>结果</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($log) as $entry) : ?>
                            <tr>
                                <td><?php echo esc_html(isset($entry['timestamp']) ? (string) $entry['timestamp'] : ''); ?></td>
                                <td><?php echo esc_html(isset($entry['reason']) ? (string) $entry['reason'] : ''); ?></td>
                                <td><?php echo esc_html(isset($entry['target']) ? (string) $entry['target'] : ''); ?></td>
                                <td><?php echo esc_html(isset($entry['status']) ? (string) $entry['status'] : ''); ?></td>
                                <td><?php echo !empty($entry['ok']) ? '<strong style="color:#15803d;">OK</strong>' : '<strong style="color:#b91c1c;">Failed</strong>'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </div>
    <?php
}
