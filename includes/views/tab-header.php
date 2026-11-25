<?php
/**
 * Tab 2: Header, Banner, Page Background & Floating Action Buttons (FAB).
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the Header & Cover tab panel.
 *
 * @param array<string, mixed> $ctx View context from argon_astro_render_settings().
 * @return void
 */
function argon_astro_render_tab_header(array $ctx) {
    $toolbar = $ctx['toolbar'];
    $banner = $ctx['banner'];
    $bg = $ctx['bg'];
    $fab = $ctx['fab'];
    $banner_gradients = $ctx['banner_gradients'];
    ?>
    <div class="argon-tab-panel" data-tab="header" id="tab-header">
        <section class="argon-astro-section" id="sec-toolbar" data-card-title="顶部导航栏">
            <div class="argon-card-head">
                <h2>顶部导航栏</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label>顶栏显示状态 (Headroom)</label></th>
                        <td>
                            <?php
                            argon_astro_render_select('argon_astro_config[toolbar][headroom]', $toolbar['headroom'], array(
                                'false' => '始终固定悬浮',
                                'true' => '滚动时自动折叠',
                                'absolute' => '不固定（仅页首显示）',
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label>顶栏标题</label></th>
                        <td>
                            <input type="text" class="regular-text" name="argon_astro_config[toolbar][title]" value="<?php echo esc_attr($toolbar['title']); ?>" />
                            <p class="description">留空则显示博客名称，输入 <code>--hidden--</code> 可隐藏标题。</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>顶栏图标与链接</label></th>
                        <td>
                            <p><label>图标地址<br /><input type="text" class="regular-text" name="argon_astro_config[toolbar][icon]" value="<?php echo esc_attr($toolbar['icon']); ?>" placeholder="https:// 或 /argon/toolbar-icon.svg" /></label></p>
                            <p><label>点击跳转链接<br /><input type="text" class="regular-text" name="argon_astro_config[toolbar][iconLink]" value="<?php echo esc_attr($toolbar['iconLink']); ?>" placeholder="/" /></label></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>顶栏毛玻璃效果</label></th>
                        <td>
                            <?php argon_astro_render_bool_select('argon_astro_config[toolbar][blur]', (bool) $toolbar['blur'], '开启', '关闭'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="argon-astro-section" id="sec-banner" data-card-title="顶部 Banner (封面)">
            <div class="argon-card-head">
                <h2>顶部 Banner (封面)</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label>Banner 尺寸</label></th>
                        <td>
                            <?php
                            argon_astro_render_select('argon_astro_config[banner][size]', $banner['size'], array(
                                'full' => '标准（完整半屏）',
                                'mini' => '迷你（紧凑内边距）',
                                'fullscreen' => '全屏封面（仅首页生效）',
                                'hide' => '隐藏 Banner',
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Banner 标题与副标题</label></th>
                        <td>
                            <p><label>Banner 标题（留空显示博客名）<br /><input type="text" class="regular-text" name="argon_astro_config[banner][title]" value="<?php echo esc_attr($banner['title']); ?>" /></label></p>
                            <p><label>Banner 副标题<br /><input type="text" class="regular-text" name="argon_astro_config[banner][subtitle]" value="<?php echo esc_attr($banner['subtitle']); ?>" /></label></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Banner 打字机动画</label></th>
                        <td>
                            <div class="argon-inline-row">
                                <?php argon_astro_render_bool_select('argon_astro_config[banner][typing]', (bool) $banner['typing'], '启用', '不启用'); ?>
                                <label>间隔：<input type="number" name="argon_astro_config[banner][typingInterval]" min="1" max="10000" value="<?php echo esc_attr((string) $banner['typingInterval']); ?>" style="width:90px;" /> ms/字</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Banner 渐变背景样式</label></th>
                        <td>
                            <?php
                            $grad_opts = array();
                            foreach ($banner_gradients as $gk => $gmeta) {
                                $grad_opts[$gk] = $gmeta['label'] . ' (' . $gk . ')';
                            }
                            argon_astro_render_select('argon_astro_config[banner][colorType]', $banner['colorType'], $grad_opts, 'id="argon_banner_color_type"');
                            ?>
                            <input type="hidden" name="argon_astro_config[banner][hideShapes]" value="0" />
                            <label style="margin-left:14px;">
                                <input type="checkbox" name="argon_astro_config[banner][hideShapes]" value="1" <?php checked($banner['hideShapes'], true); ?> />
                                隐藏背景半透明圆（气泡）
                            </label>
                            <div class="argon-gradient-grid" id="argon_banner_gradients">
                                <?php foreach ($banner_gradients as $gk => $gmeta) : ?>
                                    <div class="argon-gradient-chip<?php echo $banner['colorType'] === $gk ? ' is-active' : ''; ?>" data-gradient="<?php echo esc_attr($gk); ?>" style="background:<?php echo esc_attr($gmeta['css']); ?>;"><?php echo esc_html($gmeta['label']); ?></div>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Banner 背景图地址</label></th>
                        <td>
                            <input type="text" class="large-text" name="argon_astro_config[banner][backgroundUrl]" value="<?php echo esc_attr($banner['backgroundUrl']); ?>" placeholder="https://... 或 --bing--" />
                            <p class="description">留空则使用上方渐变样式；输入 <code>--bing--</code> 可调用必应每日一图。</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Banner 透明化与顶栏遮罩</label></th>
                        <td>
                            <?php
                            argon_astro_render_select('argon_astro_config[background][bannerStyle]', $bg['bannerStyle'], array(
                                'normal' => '关闭（标准 Banner）',
                                'transparent' => '开启（Banner 透明化，适配全局背景图）',
                            ));
                            ?>
                            <p style="margin-top:10px;">
                                <input type="hidden" name="argon_astro_config[banner][toolbarMask]" value="0" />
                                <label>
                                    <input type="checkbox" name="argon_astro_config[banner][toolbarMask]" value="1" <?php checked($banner['toolbarMask'], true); ?> />
                                    在顶栏添加浅色遮罩并为 Banner 标题添加阴影（浅色背景时推荐）
                                </label>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="argon-astro-section" id="sec-background" data-card-title="页面全局背景">
            <div class="argon-card-head">
                <h2>页面全局背景</h2>
                <p class="argon-card-desc">同源媒体库图片会在构建时自动同步到本地。</p>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label>背景图地址与透明度</label></th>
                        <td>
                            <p><label>日间模式背景图 URL<br /><input type="text" class="large-text" name="argon_astro_config[background][url]" value="<?php echo esc_attr($bg['url']); ?>" placeholder="https://..." /></label></p>
                            <p><label>夜间模式背景图 URL（留空则自动压暗日间背景）<br /><input type="text" class="large-text" name="argon_astro_config[background][darkUrl]" value="<?php echo esc_attr($bg['darkUrl']); ?>" placeholder="https://..." /></label></p>
                            <p><label>背景不透明度 (0 ~ 1)：<input type="number" name="argon_astro_config[background][opacity]" min="0" max="1" step="0.01" value="<?php echo esc_attr((string) $bg['opacity']); ?>" style="width:90px;" /></label></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="argon-astro-section" id="sec-fab" data-card-title="浮动操作按钮 (FAB)">
            <div class="argon-card-head">
                <h2>浮动操作按钮 (FAB)</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label>按钮显示开关</label></th>
                        <td>
                            <div class="argon-check-group">
                                <input type="hidden" name="argon_astro_config[fab][settings]" value="0" />
                                <label class="argon-check-pill"><input type="checkbox" name="argon_astro_config[fab][settings]" value="1" <?php checked($fab['settings'], true); ?> /> 外观设置按钮</label>
                                <input type="hidden" name="argon_astro_config[fab][darkmode]" value="0" />
                                <label class="argon-check-pill"><input type="checkbox" name="argon_astro_config[fab][darkmode]" value="1" <?php checked($fab['darkmode'], true); ?> /> 夜间模式切换按钮</label>
                                <input type="hidden" name="argon_astro_config[fab][comment]" value="0" />
                                <label class="argon-check-pill"><input type="checkbox" name="argon_astro_config[fab][comment]" value="1" <?php checked($fab['comment'], true); ?> /> 跳转评论按钮</label>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
    <?php
}
