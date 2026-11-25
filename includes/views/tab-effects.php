<?php
/**
 * Tab 4: Rendering & Interactions (4 focused cards: Code Highlight, Math, Images, Motion/PJAX).
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the Rendering & Interactions tab panel.
 *
 * @param array<string, mixed> $ctx View context from argon_astro_render_settings().
 * @return void
 */
function argon_astro_render_tab_effects(array $ctx) {
    $motion = $ctx['motion'];
    $code_themes = $ctx['code_themes'];
    $theme_list = $code_themes;
    if ($motion['codeTheme'] !== '' && !in_array($motion['codeTheme'], $theme_list, true)) {
        array_unshift($theme_list, $motion['codeTheme']);
    }
    $loader_opts = array();
    for ($i = 1; $i <= 11; $i++) {
        $loader_opts[(string) $i] = '加载动效 ' . $i;
    }
    $loader_opts['none'] = '不使用动效';
    ?>
    <div class="argon-tab-panel" data-tab="effects" id="tab-effects">
        <section class="argon-astro-section" id="sec-code-highlight" data-card-title="代码高亮 (Highlight.js)">
            <div class="argon-card-head">
                <h2>代码高亮 (Highlight.js)</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label>高亮开关与配色主题</label></th>
                        <td>
                            <div class="argon-inline-row">
                                <?php argon_astro_render_bool_select('argon_astro_config[motion][codeHighlight]', (bool) $motion['codeHighlight'], '启用代码高亮', '不启用'); ?>
                                <label>配色主题：
                                    <select name="argon_astro_config[motion][codeTheme]">
                                        <?php foreach ($theme_list as $ct) : ?>
                                            <option value="<?php echo esc_attr($ct); ?>" <?php selected($motion['codeTheme'], $ct); ?>><?php echo esc_html($ct); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>代码块显示选项</label></th>
                        <td>
                            <div class="argon-check-group">
                                <input type="hidden" name="argon_astro_config[motion][codeHideLineNumber]" value="0" />
                                <label class="argon-check-pill"><input type="checkbox" name="argon_astro_config[motion][codeHideLineNumber]" value="1" <?php checked($motion['codeHideLineNumber'], true); ?> /> 隐藏行号</label>
                                <input type="hidden" name="argon_astro_config[motion][codeTransparentLineNumber]" value="0" />
                                <label class="argon-check-pill"><input type="checkbox" name="argon_astro_config[motion][codeTransparentLineNumber]" value="1" <?php checked($motion['codeTransparentLineNumber'], true); ?> /> 行号背景透明</label>
                                <input type="hidden" name="argon_astro_config[motion][codeBreakLine]" value="0" />
                                <label class="argon-check-pill"><input type="checkbox" name="argon_astro_config[motion][codeBreakLine]" value="1" <?php checked($motion['codeBreakLine'], true); ?> /> 自动换行</label>
                                <input type="hidden" name="argon_astro_config[motion][disableCodeblockStyle]" value="0" />
                                <label class="argon-check-pill"><input type="checkbox" name="argon_astro_config[motion][disableCodeblockStyle]" value="1" <?php checked($motion['disableCodeblockStyle'], true); ?> /> 禁用 Argon 代码块样式</label>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="argon-astro-section" id="sec-math" data-card-title="数学公式渲染">
            <div class="argon-card-head">
                <h2>数学公式渲染</h2>
                <p class="argon-card-desc">KaTeX 留空 CDN 时自动使用本地内置资源。</p>
            </div>
            <?php
            $math_engines = array(
                'none' => '不启用',
                'katex' => 'KaTeX（推荐）',
                'mathjax3' => 'MathJax 3',
                'mathjax2' => 'MathJax 2',
            );
            $active_math = array_key_exists((string) $motion['math'], $math_engines) ? (string) $motion['math'] : 'none';
            ?>
            <div class="argon-ci-selector" id="argon_math_selector" role="radiogroup" aria-label="选择数学公式引擎">
                <?php foreach ($math_engines as $mkey => $mlabel) : ?>
                    <label class="argon-ci-choice<?php echo $active_math === $mkey ? ' is-selected' : ''; ?>">
                        <input type="radio" name="argon_astro_config[motion][math]" value="<?php echo esc_attr($mkey); ?>" <?php checked($active_math, $mkey); ?> />
                        <span><?php echo esc_html($mlabel); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="argon-ci-stage" id="argon_math_stage">
                <div class="argon-ci-panel argon-ci-empty<?php echo $active_math === 'none' ? ' is-active' : ''; ?>" data-math-panel="none">
                    未启用数学公式渲染。
                </div>
                <div class="argon-ci-panel<?php echo $active_math === 'katex' ? ' is-active' : ''; ?>" data-math-panel="katex">
                    <div class="argon-ci-field">
                        <label for="argon_math_katex_cdn">KaTeX CDN (HTTPS)</label>
                        <input id="argon_math_katex_cdn" type="text" name="argon_astro_config[motion][katexCdn]" value="<?php echo esc_attr($motion['katexCdn']); ?>" placeholder="留空使用本地内置 KaTeX，或填写 https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/" />
                    </div>
                </div>
                <div class="argon-ci-panel<?php echo $active_math === 'mathjax3' ? ' is-active' : ''; ?>" data-math-panel="mathjax3">
                    <div class="argon-ci-field">
                        <label for="argon_math_mj3_cdn">MathJax 3 CDN (HTTPS)</label>
                        <input id="argon_math_mj3_cdn" type="text" name="argon_astro_config[motion][mathjaxCdn]" value="<?php echo esc_attr($motion['mathjaxCdn']); ?>" placeholder="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml-full.js" />
                    </div>
                </div>
                <div class="argon-ci-panel<?php echo $active_math === 'mathjax2' ? ' is-active' : ''; ?>" data-math-panel="mathjax2">
                    <div class="argon-ci-field">
                        <label for="argon_math_mj2_cdn">MathJax 2 CDN (HTTPS)</label>
                        <input id="argon_math_mj2_cdn" type="text" name="argon_astro_config[motion][mathjaxV2Cdn]" value="<?php echo esc_attr($motion['mathjaxV2Cdn']); ?>" placeholder="https://cdn.jsdelivr.net/npm/mathjax@2.7.5/MathJax.js?config=TeX-AMS_HTML" />
                    </div>
                </div>
            </div>
        </section>

        <section class="argon-astro-section" id="sec-images" data-card-title="图片懒加载与灯箱预览">
            <div class="argon-card-head">
                <h2>图片懒加载与灯箱预览</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label>图片懒加载 (Lazyload)</label></th>
                        <td>
                            <div class="argon-inline-row">
                                <?php argon_astro_render_bool_select('argon_astro_config[motion][lazyload]', (bool) $motion['lazyload'], '启用', '禁用'); ?>
                                <label>提前加载阈值：<input type="number" name="argon_astro_config[motion][lazyloadThreshold]" min="0" max="2500" value="<?php echo esc_attr((string) $motion['lazyloadThreshold']); ?>" style="width:80px;" /> px</label>
                                <?php
                                argon_astro_render_select('argon_astro_config[motion][lazyloadEffect]', $motion['lazyloadEffect'], array(
                                    'fadeIn' => 'fadeIn',
                                    'slideDown' => 'slideDown',
                                    'none' => '无过渡',
                                ));
                                argon_astro_render_select('argon_astro_config[motion][lazyloadLoadingStyle]', (string) $motion['lazyloadLoadingStyle'], $loader_opts);
                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>图片放大预览</label></th>
                        <td>
                            <div class="argon-inline-row" style="margin-bottom:10px;">
                                <label>Fancybox 灯箱：<?php argon_astro_render_bool_select('argon_astro_config[motion][fancybox]', (bool) $motion['fancybox'], '启用', '禁用', 'id="argon_fancybox_select"'); ?></label>
                                <label>旧版 Zoomify：<?php argon_astro_render_bool_select('argon_astro_config[motion][zoomify]', (bool) $motion['zoomify'], '启用', '禁用', 'id="argon_zoomify_select"'); ?></label>
                            </div>
                            <div class="argon-inline-row">
                                <label>Zoomify 时长：<input type="number" name="argon_astro_config[motion][zoomifyDuration]" min="0" max="10000" value="<?php echo esc_attr((string) $motion['zoomifyDuration']); ?>" style="width:80px;" /> ms</label>
                                <label>曲线：<input type="text" name="argon_astro_config[motion][zoomifyEasing]" value="<?php echo esc_attr($motion['zoomifyEasing']); ?>" style="width:210px;" /></label>
                                <label>最大缩放：<input type="number" name="argon_astro_config[motion][zoomifyScale]" min="0.01" max="1" step="0.01" value="<?php echo esc_attr((string) $motion['zoomifyScale']); ?>" style="width:76px;" /></label>
                            </div>
                            <p class="description">Fancybox 与 Zoomify 互斥，启用其中一项时将自动关闭另一项。</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="argon-astro-section" id="sec-motion" data-card-title="滚动、PJAX 与阅读增强">
            <div class="argon-card-head">
                <h2>滚动、PJAX 与阅读增强</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label>PJAX 无刷新跳转</label></th>
                        <td>
                            <div class="argon-inline-row">
                                <label>启用 PJAX：<?php argon_astro_render_bool_select('argon_astro_config[motion][pjax]', (bool) $motion['pjax'], '启用', '禁用'); ?></label>
                                <label>PJAX 加载后滚动动画：<?php argon_astro_render_bool_select('argon_astro_config[motion][pjaxAnimation]', (bool) $motion['pjaxAnimation'], '启用', '禁用'); ?></label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>平滑滚动</label></th>
                        <td>
                            <?php
                            argon_astro_render_select('argon_astro_config[motion][smoothScroll]', (string) $motion['smoothScroll'], array(
                                '1' => '方案 1 (平滑 · 推荐)',
                                '1_pulse' => '方案 1 (脉冲式 · 仿 Edge)',
                                '2' => '方案 2 (较稳)',
                                '3' => '方案 3',
                                'disabled' => '不使用平滑滚动',
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label>Pangu 中英文自动空格</label></th>
                        <td>
                            <?php
                            argon_astro_render_select('argon_astro_config[motion][pangu]', (string) $motion['pangu'], array(
                                '' => '禁用',
                                'article' => '仅格式化文章内容',
                                'shuoshuo' => '仅格式化说说',
                                'comment' => '仅格式化评论区',
                                'article|comment' => '文章 + 评论区',
                                'article|shuoshuo' => '文章 + 说说',
                                'shuoshuo|comment' => '说说 + 评论区',
                                'article|shuoshuo|comment' => '文章 + 说说 + 评论区',
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label>进度条与内容折叠</label></th>
                        <td>
                            <div class="argon-check-group">
                                <input type="hidden" name="argon_astro_config[motion][readingProgress]" value="0" />
                                <label class="argon-check-pill"><input type="checkbox" name="argon_astro_config[motion][readingProgress]" value="1" <?php checked($motion['readingProgress'], true); ?> /> 顶部阅读进度条</label>
                                <input type="hidden" name="argon_astro_config[motion][foldLongComments]" value="0" />
                                <label class="argon-check-pill"><input type="checkbox" name="argon_astro_config[motion][foldLongComments]" value="1" <?php checked($motion['foldLongComments'], true); ?> /> 折叠过长评论</label>
                                <input type="hidden" name="argon_astro_config[motion][foldLongShuoshuo]" value="0" />
                                <label class="argon-check-pill"><input type="checkbox" name="argon_astro_config[motion][foldLongShuoshuo]" value="1" <?php checked($motion['foldLongShuoshuo'], true); ?> /> 折叠过长说说</label>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
    <?php
}
