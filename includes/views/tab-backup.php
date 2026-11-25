<?php
/**
 * Tab 6: Import & Export (standalone forms outside the main settings form).
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the Import / Export tab panel.
 *
 * @param array<string, mixed> $ctx View context from argon_astro_render_settings().
 * @return void
 */
function argon_astro_render_tab_backup(array $ctx) {
    $legacy_count = (int) $ctx['legacy_count'];
    $exported_argon_json = (string) $ctx['exported_argon_json'];
    $exported_astro_json = (string) $ctx['exported_astro_json'];
    ?>
    <div class="argon-tab-extra" data-tab-extra="backup" id="tab-backup">
        <section class="argon-astro-section" id="sec-import" data-card-title="导入配置">
            <div class="argon-card-head">
                <h2>导入配置</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label>从现有 Argon 迁移</label></th>
                        <td>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                <input type="hidden" name="action" value="argon_astro_import_legacy" />
                                <?php wp_nonce_field('argon_astro_import_legacy'); ?>
                                <button type="submit" class="button button-secondary">从现有 Argon 主题选项一键导入</button>
                                <p class="description">
                                    检测到 <strong><?php echo $legacy_count; ?></strong> 项可迁移的原版 <code>argon_*</code> 设置。
                                </p>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="argon_import_json_box">粘贴 JSON 导入</label></th>
                        <td>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                <input type="hidden" name="action" value="argon_astro_import_json" />
                                <?php wp_nonce_field('argon_astro_import_json'); ?>
                                <textarea id="argon_import_json_box" name="argon_astro_import_payload" rows="7" class="large-text code" placeholder='{"argon_theme_color": "#5e72e4", "argon_banner_size": "fullscreen", ...}'></textarea>
                                <p style="margin-top:10px;">
                                    <button type="submit" class="button button-primary">导入 JSON 并保存</button>
                                    <span class="description" style="margin-left:8px;">支持原版扁平 JSON 与嵌套 JSON。</span>
                                </p>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="argon-astro-section" id="sec-export" data-card-title="导出备份 JSON">
            <div class="argon-card-head">
                <h2>导出备份 JSON</h2>
            </div>
            <p>
                <button type="button" class="button button-secondary" id="argon_toggle_export_btn">显示 / 隐藏导出 JSON</button>
                <button type="button" class="button button-secondary" id="argon_copy_export_btn" style="margin-left:6px;">复制 JSON</button>
                <button type="button" class="button button-secondary" id="argon_download_export_btn" style="margin-left:6px;">下载 .json 备份</button>
                <span id="argon_copy_status" class="description" style="margin-left:8px;"></span>
            </p>
            <div id="argon_export_wrap" style="display:none;margin-top:12px;">
                <textarea id="argon_export_json_box" rows="10" class="large-text code" readonly><?php echo esc_textarea($exported_argon_json); ?></textarea>
                <details style="margin-top:10px;">
                    <summary style="cursor:pointer;color:#475569;">查看嵌套配置 JSON</summary>
                    <textarea rows="10" class="large-text code" readonly style="margin-top:8px;"><?php echo esc_textarea($exported_astro_json); ?></textarea>
                </details>
            </div>
        </section>
    </div>
    <?php
}
