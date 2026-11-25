<?php
/**
 * Tab 1: Appearance & Layout cards (Theme Colors & Dark Mode; Cards, Fonts & Layout).
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the Appearance & Layout tab panel.
 *
 * @param array<string, mixed> $ctx View context from argon_astro_render_settings().
 * @return void
 */
function argon_astro_render_tab_appearance(array $ctx) {
    $app = $ctx['app'];
    $article = $ctx['article'];
    $site = $ctx['site'];
    $presets = $ctx['presets'];
    $wf = ($article['waterflow'] === '2,3') ? '2and3' : (string) $article['waterflow'];
    ?>
    <div class="argon-tab-panel is-active" data-tab="appearance" id="tab-appearance">
        <section class="argon-astro-section" id="sec-appearance-color" data-card-title="主题色彩与夜间模式">
            <div class="argon-card-head">
                <h2>主题色彩与夜间模式</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label for="argon_color_picker">主题颜色</label></th>
                        <td>
                            <div class="argon-color-picker-row">
                                <input type="color" id="argon_color_picker" class="argon-color-input" value="<?php echo esc_attr($app['color']); ?>" />
                                <input type="text" id="argon_color_hex" class="argon-color-hex" name="argon_astro_config[appearance][color]" value="<?php echo esc_attr($app['color']); ?>" pattern="^#[0-9a-fA-F]{6}$" />
                                <span class="description">点击色块自定义，或选择下方预设色</span>
                            </div>
                            <div class="argon-preset-grid" id="argon_color_presets">
                                <?php foreach ($presets as $preset) : ?>
                                    <?php $is_active = strtolower($app['color']) === strtolower($preset['hex']); ?>
                                    <div class="argon-preset-box<?php echo $is_active ? ' is-active' : ''; ?>" data-color="<?php echo esc_attr($preset['hex']); ?>">
                                        <div class="argon-preset-swatch" style="background:<?php echo esc_attr($preset['hex']); ?>;"></div>
                                        <div class="argon-preset-name"><?php echo esc_html($preset['label']); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <p style="margin-top:14px;">
                                <input type="hidden" name="argon_astro_config[appearance][allowColorPicker]" value="0" />
                                <label>
                                    <input type="checkbox" name="argon_astro_config[appearance][allowColorPicker]" value="1" <?php checked($app['allowColorPicker'], true); ?> />
                                    允许访客在前台自定义主题色
                                </label>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>沉浸式主题色</label></th>
                        <td>
                            <?php argon_astro_render_bool_select('argon_astro_config[appearance][immersionColor]', (bool) $app['immersionColor'], '开启', '关闭'); ?>
                            <p class="description">页面背景与卡片会融入当前主题色。</p>
                            <div class="argon-immersion-demo">
                                <div class="argon-immersion-box" style="background:#f4f5f7;"><div class="argon-immersion-card" style="background:#fff;"></div></div>
                                <span class="dashicons dashicons-arrow-right-alt" style="color:#64748b;"></span>
                                <div class="argon-immersion-box" style="background:#e8ebfb;"><div class="argon-immersion-card" style="background:#f2f4fd;"></div></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>夜间模式切换方案</label></th>
                        <td>
                            <?php
                            argon_astro_render_select('argon_astro_config[appearance][darkmode]', $app['darkmode'], array(
                                'off' => '日间模式',
                                'system' => '跟随系统',
                                'time' => '按时间自动切换（22:00 ~ 7:00）',
                                'always' => '始终夜间模式',
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label>夜间模式颜色方案</label></th>
                        <td>
                            <?php argon_astro_render_bool_select('argon_astro_config[appearance][amoledDark]', (bool) $app['amoledDark'], '暗黑 (AMOLED Black)', '灰黑 (默认)'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="argon-astro-section" id="sec-appearance-layout" data-card-title="卡片、字体与页面布局">
            <div class="argon-card-head">
                <h2>卡片、字体与页面布局</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label for="argon_card_radius">卡片圆角大小</label></th>
                        <td>
                            <input type="number" id="argon_card_radius" name="argon_astro_config[appearance][cardRadius]" min="0" max="30" step="0.5" value="<?php echo esc_attr((string) $app['cardRadius']); ?>" /> px
                            <p class="description">默认 <code>4px</code>，建议范围 <code>2px</code> - <code>15px</code>。</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>卡片阴影</label></th>
                        <td>
                            <div class="argon-radio-inline">
                                <label><input type="radio" name="argon_astro_config[appearance][cardShadow]" value="small" <?php checked($app['cardShadow'], 'small'); ?> /> 浅阴影</label>
                                <label><input type="radio" name="argon_astro_config[appearance][cardShadow]" value="big" <?php checked($app['cardShadow'], 'big'); ?> /> 深阴影</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>默认字体</label></th>
                        <td>
                            <div class="argon-radio-inline">
                                <label><input type="radio" name="argon_astro_config[appearance][font]" value="sans" <?php checked($app['font'], 'sans'); ?> /> Sans Serif（无衬线）</label>
                                <label><input type="radio" name="argon_astro_config[appearance][font]" value="serif" <?php checked($app['font'], 'serif'); ?> /> Serif（衬线）</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>页面布局</label></th>
                        <td>
                            <div class="argon-radio-cards">
                                <label class="argon-radio-card<?php echo $app['layout'] === 'double' ? ' is-selected' : ''; ?>">
                                    <svg width="150" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 1080"><rect width="1920" height="1080" fill="#e6e6e6"/><rect width="1920" height="381" fill="#5e72e4" opacity=".5"/><rect x="388.5" y="256" width="258" height="179" fill="#5e72e4"/><rect x="388.5" y="470" width="258" height="485" fill="#fff"/><rect x="689.5" y="256.5" width="842" height="250" fill="#fff"/><rect x="689.5" y="536.5" width="842" height="250" fill="#fff"/><rect x="689.5" y="817" width="842" height="250" fill="#fff"/></svg>
                                    <span><input type="radio" name="argon_astro_config[appearance][layout]" value="double" <?php checked($app['layout'], 'double'); ?> /> 双栏</span>
                                </label>
                                <label class="argon-radio-card<?php echo $app['layout'] === 'single' ? ' is-selected' : ''; ?>">
                                    <svg width="150" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 1080"><rect width="1920" height="1080" fill="#e6e6e6"/><rect width="1920" height="381" fill="#5e72e4" opacity=".5"/><rect x="428.25" y="256.5" width="1063.5" height="250" fill="#fff"/><rect x="428.25" y="536.5" width="1063.5" height="250" fill="#fff"/><rect x="428.25" y="817" width="1063.5" height="250" fill="#fff"/></svg>
                                    <span><input type="radio" name="argon_astro_config[appearance][layout]" value="single" <?php checked($app['layout'], 'single'); ?> /> 单栏</span>
                                </label>
                                <label class="argon-radio-card<?php echo $app['layout'] === 'double-reverse' ? ' is-selected' : ''; ?>">
                                    <svg width="150" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 1080"><rect width="1920" height="1080" fill="#e6e6e6"/><rect width="1920" height="381" fill="#5e72e4" opacity=".5"/><rect x="1273.5" y="256" width="258" height="179" fill="#5e72e4"/><rect x="1273.5" y="470" width="258" height="485" fill="#fff"/><rect x="388.5" y="256.5" width="842" height="250" fill="#fff"/><rect x="388.5" y="536.5" width="842" height="250" fill="#fff"/><rect x="388.5" y="817" width="842" height="250" fill="#fff"/></svg>
                                    <span><input type="radio" name="argon_astro_config[appearance][layout]" value="double-reverse" <?php checked($app['layout'], 'double-reverse'); ?> /> 双栏 (反转)</span>
                                </label>
                                <label class="argon-radio-card<?php echo $app['layout'] === 'triple' ? ' is-selected' : ''; ?>">
                                    <svg width="150" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 1080"><rect width="1920" height="1080" fill="#e6e6e6"/><rect width="1920" height="381" fill="#5e72e4" opacity=".5"/><rect x="237.5" y="256" width="258" height="179" fill="#5e72e4"/><rect x="237.5" y="470" width="258" height="485" fill="#fff"/><rect x="538.5" y="256.5" width="842" height="250" fill="#fff"/><rect x="538.5" y="536.5" width="842" height="250" fill="#fff"/><rect x="538.5" y="817" width="842" height="250" fill="#fff"/><rect x="1424" y="256" width="258" height="811" fill="#fff"/></svg>
                                    <span><input type="radio" name="argon_astro_config[appearance][layout]" value="triple" <?php checked($app['layout'], 'triple'); ?> /> 三栏</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>文章列表瀑布流</label></th>
                        <td>
                            <div class="argon-radio-cards">
                                <label class="argon-radio-card<?php echo $wf === '1' ? ' is-selected' : ''; ?>">
                                    <svg width="132" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1880 1340"><rect width="1880" height="1340" fill="#f7f8f8"/><rect x="46" y="46" width="1786" height="412" fill="#abb7ff"/><rect x="46" y="496" width="1786" height="326" fill="#abb7ff"/><rect x="46" y="860" width="1786" height="350" fill="#abb7ff"/></svg>
                                    <span><input type="radio" name="argon_astro_config[article][waterflow]" value="1" <?php checked($wf, '1'); ?> /> 单列</span>
                                </label>
                                <label class="argon-radio-card<?php echo $wf === '2' ? ' is-selected' : ''; ?>">
                                    <svg width="132" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1880 1340"><rect width="1880" height="1340" fill="#f7f8f8"/><rect x="46" y="46" width="874" height="590" fill="#abb7ff"/><rect x="962" y="46" width="874" height="390" fill="#abb7ff"/><rect x="962" y="480" width="874" height="492" fill="#abb7ff"/><rect x="46" y="681" width="874" height="426" fill="#abb7ff"/></svg>
                                    <span><input type="radio" name="argon_astro_config[article][waterflow]" value="2" <?php checked($wf, '2'); ?> /> 瀑布流 (2 列)</span>
                                </label>
                                <label class="argon-radio-card<?php echo $wf === '3' ? ' is-selected' : ''; ?>">
                                    <svg width="132" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1880 1340"><rect width="1880" height="1340" fill="#f7f8f8"/><rect x="46" y="46" width="568" height="531" fill="#abb7ff"/><rect x="656" y="46" width="568" height="400" fill="#abb7ff"/><rect x="1266" y="46" width="568" height="604" fill="#abb7ff"/><rect x="656" y="485" width="568" height="428" fill="#abb7ff"/><rect x="46" y="615" width="568" height="407" fill="#abb7ff"/></svg>
                                    <span><input type="radio" name="argon_astro_config[article][waterflow]" value="3" <?php checked($wf, '3'); ?> /> 瀑布流 (3 列)</span>
                                </label>
                                <label class="argon-radio-card<?php echo $wf === '2and3' ? ' is-selected' : ''; ?>">
                                    <svg width="132" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1880 1340"><rect width="1880" height="1340" fill="#f7f8f8"/><rect x="46" y="46" width="874" height="560" fill="#abb7ff"/><rect x="962" y="46" width="420" height="400" fill="#8b9cff"/><rect x="1416" y="46" width="420" height="540" fill="#8b9cff"/><rect x="46" y="646" width="874" height="480" fill="#abb7ff"/><rect x="962" y="486" width="420" height="480" fill="#8b9cff"/></svg>
                                    <span><input type="radio" name="argon_astro_config[article][waterflow]" value="2and3" <?php checked($wf, '2and3'); ?> /> 瀑布流 (自适应)</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>文章列表卡片布局</label></th>
                        <td>
                            <div class="argon-radio-cards">
                                <label class="argon-radio-card<?php echo (string) $article['listLayout'] === '1' ? ' is-selected' : ''; ?>">
                                    <svg width="150" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 870"><rect width="1920" height="870" fill="#f7f8f8" stroke="#cbd5e1" stroke-width="12"/><rect width="1920" height="520" fill="#abb7ff"/><rect x="48" y="590" width="1820" height="36" rx="6" fill="#e2e8f0"/><rect x="48" y="645" width="1820" height="36" rx="6" fill="#e2e8f0"/><rect x="48" y="700" width="1200" height="36" rx="6" fill="#e2e8f0"/></svg>
                                    <span><input type="radio" name="argon_astro_config[article][listLayout]" value="1" <?php checked((string) $article['listLayout'], '1'); ?> /> 布局 1（头图内标题）</span>
                                </label>
                                <label class="argon-radio-card<?php echo (string) $article['listLayout'] === '2' ? ' is-selected' : ''; ?>">
                                    <svg width="150" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 870"><rect width="1920" height="870" fill="#f7f8f8" stroke="#cbd5e1" stroke-width="12"/><rect width="630" height="870" fill="#abb7ff"/><rect x="690" y="70" width="1060" height="56" rx="6" fill="#5e72e4" opacity=".35"/><rect x="690" y="180" width="1140" height="36" rx="6" fill="#e2e8f0"/><rect x="690" y="244" width="1140" height="36" rx="6" fill="#e2e8f0"/><rect x="690" y="308" width="900" height="36" rx="6" fill="#e2e8f0"/></svg>
                                    <span><input type="radio" name="argon_astro_config[article][listLayout]" value="2" <?php checked((string) $article['listLayout'], '2'); ?> /> 布局 2（左图右文）</span>
                                </label>
                                <label class="argon-radio-card<?php echo (string) $article['listLayout'] === '3' ? ' is-selected' : ''; ?>">
                                    <svg width="150" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 870"><rect width="1920" height="870" fill="#f7f8f8" stroke="#cbd5e1" stroke-width="12"/><rect width="1920" height="360" fill="#abb7ff"/><rect x="48" y="410" width="1060" height="56" rx="6" fill="#5e72e4" opacity=".35"/><rect x="48" y="510" width="1820" height="36" rx="6" fill="#e2e8f0"/><rect x="48" y="570" width="1820" height="36" rx="6" fill="#e2e8f0"/></svg>
                                    <span><input type="radio" name="argon_astro_config[article][listLayout]" value="3" <?php checked((string) $article['listLayout'], '3'); ?> /> 布局 3（上图下文）</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>文章内标题样式</label></th>
                        <td>
                            <?php
                            argon_astro_render_select('argon_astro_config[appearance][articleHeaderStyle]', $app['articleHeaderStyle'], array(
                                'article-header-style-default' => '默认样式',
                                'article-header-style-1' => '样式 1（底部主题色高亮条）',
                                'article-header-style-2' => '样式 2（左侧主题色竖条）',
                            ));
                            ?>
                            <div style="margin-top:8px;">
                                <span class="argon-header-preview">默认样式</span>
                                <span class="argon-header-preview style-1">样式 1</span>
                                <span class="argon-header-preview style-2">样式 2</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>日期格式</label></th>
                        <td>
                            <?php
                            argon_astro_render_select('argon_astro_config[site][dateFormat]', $site['dateFormat'], array(
                                'YMD' => 'Y-M-D',
                                'DMY' => 'D-M-Y',
                                'MDY' => 'M-D-Y',
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label>远程字体与缩放</label></th>
                        <td>
                            <div class="argon-inline-row">
                                <label>禁用 Google 字体：<?php argon_astro_render_bool_select('argon_astro_config[appearance][disableGoogleFont]', (bool) $app['disableGoogleFont'], '禁用', '不禁用'); ?></label>
                                <label>移动端缩放页面：<?php argon_astro_render_bool_select('argon_astro_config[appearance][mobileScale]', (bool) $app['mobileScale'], '允许', '不允许'); ?></label>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
    <?php
}
