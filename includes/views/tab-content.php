<?php
/**
 * Tab 3: Site Identity/SEO, Sidebar, Articles, Home/Archives/Search, Friend Links & Footer.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the Content & Sidebar tab panel.
 *
 * @param array<string, mixed> $ctx View context from argon_astro_render_settings().
 * @return void
 */
function argon_astro_render_tab_content(array $ctx) {
    $site = $ctx['site'];
    $app = $ctx['app'];
    $sidebar = $ctx['sidebar'];
    $article = $ctx['article'];
    $home = $ctx['home'];
    $footer = $ctx['footer'];
    $friend_links = $ctx['friend_links'];
    $meta_labels = $ctx['meta_labels'];
    $search_filter_labels = $ctx['search_filter_labels'];
    ?>
    <div class="argon-tab-panel" data-tab="content" id="tab-content">
        <section class="argon-astro-section" id="sec-site-seo" data-card-title="站点信息与 SEO">
            <div class="argon-card-head">
                <h2>站点信息与 SEO</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label>站点标题与语言</label></th>
                        <td>
                            <div class="argon-two-col">
                                <label>站点标题<br /><input type="text" class="regular-text" name="argon_astro_config[site][title]" value="<?php echo esc_attr($site['title']); ?>" placeholder="<?php echo esc_attr((string) get_bloginfo('name')); ?>" /></label>
                                <label>站点语言（如 zh-CN / en-US）<br /><input type="text" class="regular-text" name="argon_astro_config[site][language]" value="<?php echo esc_attr($site['language']); ?>" placeholder="<?php echo esc_attr((string) get_bloginfo('language')); ?>" /></label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>SEO 描述与关键词</label></th>
                        <td>
                            <p><label>SEO 网站描述 (Description)<br /><textarea rows="3" class="large-text" name="argon_astro_config[site][description]" placeholder="<?php echo esc_attr((string) get_bloginfo('description')); ?>"><?php echo esc_textarea($site['description']); ?></textarea></label></p>
                            <p><label>SEO 关键词 (Keywords，逗号分隔)<br /><input type="text" class="large-text" name="argon_astro_config[site][keywords]" value="<?php echo esc_attr($site['keywords']); ?>" /></label></p>
                            <p class="description">站点标题、语言、描述默认留空，留空项将沿用 WordPress 自身设置。</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="argon-astro-section" id="sec-sidebar" data-card-title="左侧栏与公告">
            <div class="argon-card-head">
                <h2>左侧栏与公告</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label>博客公告（支持 Markdown / HTML）</label></th>
                        <td><textarea rows="4" class="large-text" name="argon_astro_config[sidebar][announcement]"><?php echo esc_textarea($sidebar['announcement']); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><label>左侧栏标题与副标题</label></th>
                        <td>
                            <p><label>侧栏标题（留空显示博客名）<br /><input type="text" class="regular-text" name="argon_astro_config[sidebar][bannerTitle]" value="<?php echo esc_attr($sidebar['bannerTitle']); ?>" /></label></p>
                            <p><label>侧栏副标题 / 格言（输入 <code>--hitokoto--</code> 调用一言）<br /><input type="text" class="regular-text" name="argon_astro_config[sidebar][bannerSubtitle]" value="<?php echo esc_attr($sidebar['bannerSubtitle']); ?>" /></label></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>作者资料</label></th>
                        <td>
                            <p><label>作者名称<br /><input type="text" class="regular-text" name="argon_astro_config[sidebar][authorName]" value="<?php echo esc_attr($sidebar['authorName']); ?>" /></label></p>
                            <p><label>作者简介<br /><input type="text" class="large-text" name="argon_astro_config[sidebar][authorDescription]" value="<?php echo esc_attr($sidebar['authorDescription']); ?>" /></label></p>
                            <p><label>作者头像 URL<br /><input type="text" class="large-text" name="argon_astro_config[sidebar][authorImage]" value="<?php echo esc_attr($sidebar['authorImage']); ?>" placeholder="https://..." /></label></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="argon-astro-section" id="sec-articles" data-card-title="文章与阅读体验">
            <div class="argon-card-head">
                <h2>文章与阅读体验</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label>文章 Meta 信息</label></th>
                        <td>
                            <input type="hidden" name="argon_astro_config[article][_meta_present]" value="1" />
                            <div class="argon-check-group">
                                <?php
                                $active_meta = is_array($article['meta']) ? $article['meta'] : array();
                                $ordered_meta = array_values(array_unique(array_merge($active_meta, array_keys($meta_labels))));
                                foreach ($ordered_meta as $mk) :
                                    if (!isset($meta_labels[$mk])) {
                                        continue;
                                    }
                                    ?>
                                    <label class="argon-check-pill">
                                        <input type="checkbox" name="argon_astro_config[article][meta][]" value="<?php echo esc_attr($mk); ?>" <?php checked(in_array($mk, $active_meta, true)); ?> />
                                        <?php echo esc_html($meta_labels[$mk]); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <p style="margin-top:10px;">
                                <input type="hidden" name="argon_astro_config[article][hideCategories]" value="0" />
                                <label><input type="checkbox" name="argon_astro_config[article][hideCategories]" value="1" <?php checked($article['hideCategories'], true); ?> /> 在文章 Meta 中隐藏分类标签</label>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>阅读时间与速度</label></th>
                        <td>
                            <?php argon_astro_render_bool_select('argon_astro_config[article][showReadingTime]', (bool) $article['showReadingTime'], '显示', '不显示'); ?>
                            <div class="argon-inline-row" style="margin-top:10px;">
                                <label>中文 <input type="number" name="argon_astro_config[article][readingSpeed]" min="1" max="5000" value="<?php echo esc_attr((string) $article['readingSpeed']); ?>" style="width:80px;" /> 字/分</label>
                                <label>英文 <input type="number" name="argon_astro_config[article][readingSpeedEn]" min="1" max="5000" value="<?php echo esc_attr((string) $article['readingSpeedEn']); ?>" style="width:80px;" /> 词/分</label>
                                <label>代码 <input type="number" name="argon_astro_config[article][readingSpeedCode]" min="1" max="5000" value="<?php echo esc_attr((string) $article['readingSpeedCode']); ?>" style="width:80px;" /> 行/分</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>摘要与列表行为</label></th>
                        <td>
                            <p><label>摘要截取字数（0 为隐藏预览）：<input type="number" name="argon_astro_config[article][trimWords]" min="0" max="1000" value="<?php echo esc_attr((string) $article['trimWords']); ?>" style="width:90px;" /></label></p>
                            <p>
                                <input type="hidden" name="argon_astro_config[article][hideShortcodeInPreview]" value="0" />
                                <label><input type="checkbox" name="argon_astro_config[article][hideShortcodeInPreview]" value="1" <?php checked($article['hideShortcodeInPreview'], true); ?> /> 在文章列表预览中隐藏短代码</label>
                            </p>
                            <p>
                                <input type="hidden" name="argon_astro_config[article][intoArticleAnimation]" value="0" />
                                <label><input type="checkbox" name="argon_astro_config[article][intoArticleAnimation]" value="1" <?php checked($article['intoArticleAnimation'], true); ?> /> 启用进入文章卡片过渡动画</label>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>头图、目录与分享</label></th>
                        <td>
                            <p>
                                <input type="hidden" name="argon_astro_config[article][thumbnailInBanner]" value="0" />
                                <label><input type="checkbox" name="argon_astro_config[article][thumbnailInBanner]" value="1" <?php checked($article['thumbnailInBanner'], true); ?> /> 文章页将头图显示在顶部 Banner 中</label>
                            </p>
                            <p>
                                <input type="hidden" name="argon_astro_config[article][firstImageAsThumbnail]" value="0" />
                                <label><input type="checkbox" name="argon_astro_config[article][firstImageAsThumbnail]" value="1" <?php checked($article['firstImageAsThumbnail'], true); ?> /> 默认使用文章首图作为缩略图</label>
                            </p>
                            <p>
                                <input type="hidden" name="argon_astro_config[article][showShare]" value="0" />
                                <label><input type="checkbox" name="argon_astro_config[article][showShare]" value="1" <?php checked($article['showShare'], true); ?> /> 显示文章分享按钮</label>
                            </p>
                            <p>
                                <input type="hidden" name="argon_astro_config[article][showCatalogNumbers]" value="0" />
                                <label><input type="checkbox" name="argon_astro_config[article][showCatalogNumbers]" value="1" <?php checked($article['showCatalogNumbers'], true); ?> /> 左侧栏文章目录显示章节编号（如 3.2.5）</label>
                            </p>
                            <p><label>脚注参考列表标题：<input type="text" class="regular-text" name="argon_astro_config[article][referenceTitle]" value="<?php echo esc_attr($article['referenceTitle']); ?>" /></label></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>相似文章推荐</label></th>
                        <td>
                            <div class="argon-inline-row">
                                <?php
                                $rel = ($article['related'] === 'tag,category') ? 'category,tag' : (string) $article['related'];
                                argon_astro_render_select('argon_astro_config[article][related]', $rel, array(
                                    'disabled' => '关闭推荐',
                                    'category' => '按分类推荐',
                                    'tag' => '按标签推荐',
                                    'category,tag' => '按分类和标签推荐',
                                ));
                                argon_astro_render_select('argon_astro_config[article][relatedOrderBy]', $article['relatedOrderBy'], array(
                                    'date' => '按日期排序',
                                    'title' => '按标题排序',
                                ));
                                argon_astro_render_select('argon_astro_config[article][relatedOrder]', $article['relatedOrder'], array(
                                    'desc' => '倒序 (DESC)',
                                    'asc' => '正序 (ASC)',
                                ));
                                ?>
                                <label>篇数：<input type="number" name="argon_astro_config[article][relatedLimit]" min="1" max="100" value="<?php echo esc_attr((string) $article['relatedLimit']); ?>" style="width:70px;" /></label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>文章过时提醒</label></th>
                        <td>
                            <div class="argon-inline-row" style="margin-bottom:8px;">
                                <span>超过</span>
                                <input type="number" name="argon_astro_config[article][outdatedDays]" min="0" max="99999" value="<?php echo esc_attr((string) $article['outdatedDays']); ?>" style="width:84px;" />
                                <span>天（0 为关闭），依据</span>
                                <?php
                                argon_astro_render_select('argon_astro_config[article][outdatedTimeType]', $article['outdatedTimeType'], array(
                                    'absolute' => '发布时间 (absolute)',
                                    'relative' => '最后修改 / 相对天数 (relative)',
                                ));
                                ?>
                                <span>样式</span>
                                <?php
                                argon_astro_render_select('argon_astro_config[article][outdatedTipType]', $article['outdatedTipType'], array(
                                    'warning' => '文内警告条 (inpost / warning)',
                                    'info' => '信息提示条 (toast / info)',
                                ));
                                ?>
                            </div>
                            <textarea rows="2" class="large-text" name="argon_astro_config[article][outdatedTip]"><?php echo esc_textarea($article['outdatedTip']); ?></textarea>
                            <p class="description">支持 <code>%date_delta%</code> 占位符表示距今天数。</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>赞赏与文末附加内容</label></th>
                        <td>
                            <p><label>赞赏二维码图片 URL（留空隐藏赞赏按钮）<br /><input type="text" class="large-text" name="argon_astro_config[article][donateQr]" value="<?php echo esc_attr($article['donateQr']); ?>" /></label></p>
                            <p><label>文末附加内容（支持 Markdown / HTML，可用 <code>%url%</code>、<code>%link%</code>、<code>%title%</code>、<code>%author%</code>）<br /><textarea rows="4" class="large-text" name="argon_astro_config[article][afterPostHtml]"><?php echo esc_textarea($article['afterPostHtml']); ?></textarea></label></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="argon-astro-section" id="sec-home-archive" data-card-title="首页、说说、归档与搜索">
            <div class="argon-card-head">
                <h2>首页、说说、归档与搜索</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label>首页与说说</label></th>
                        <td>
                            <div class="argon-inline-row">
                                <?php argon_astro_render_bool_select('argon_astro_config[home][showShuoshuo]', (bool) $home['showShuoshuo'], '首页穿插显示说说', '首页不显示说说'); ?>
                                <label>首页每页篇数：<input type="number" name="argon_astro_config[home][pageSize]" min="1" max="100" value="<?php echo esc_attr((string) $home['pageSize']); ?>" style="width:76px;" /></label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>归档时间轴</label></th>
                        <td>
                            <div class="argon-inline-row">
                                <?php argon_astro_render_bool_select('argon_astro_config[article][timelineShowMonth]', (bool) $article['timelineShowMonth'], '按月份分节', '仅按年份分节'); ?>
                                <label>归档页面链接：<input type="text" class="regular-text" name="argon_astro_config[article][archivesUrl]" value="<?php echo esc_attr($article['archivesUrl']); ?>" placeholder="/archives/" /></label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th><label>搜索过滤器</label></th>
                        <td>
                            <p><?php argon_astro_render_bool_select('argon_astro_config[article][enableSearchFilters]', (bool) $article['enableSearchFilters'], '启用过滤器', '禁用过滤器'); ?></p>
                            <input type="hidden" name="argon_astro_config[article][_search_filters_present]" value="1" />
                            <div class="argon-check-group" style="margin-top:8px;">
                                <?php
                                $active_filters = is_array($article['searchFilters']) ? $article['searchFilters'] : array();
                                foreach ($search_filter_labels as $fk => $flabel) :
                                    ?>
                                    <label class="argon-check-pill">
                                        <input type="checkbox" name="argon_astro_config[article][searchFilters][]" value="<?php echo esc_attr($fk); ?>" <?php checked(in_array($fk, $active_filters, true)); ?> />
                                        <?php echo esc_html($flabel); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="argon-astro-section" id="sec-friend-links" data-card-title="友情链接">
            <div class="argon-card-head">
                <h2>友情链接</h2>
                <p class="argon-card-desc">同源媒体库头像会在构建时自动同步到本地。</p>
            </div>
            <input type="hidden" name="argon_astro_config[_friend_links_present]" value="1" />
            <table class="argon-links-table" id="argon_friend_links_table">
                <thead>
                    <tr>
                        <th style="width:18%;">名称</th>
                        <th style="width:28%;">链接 URL</th>
                        <th style="width:26%;">简介</th>
                        <th style="width:20%;">头像 URL</th>
                        <th style="width:8%;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($friend_links as $idx => $link) : ?>
                        <tr>
                            <td><input type="text" name="argon_astro_config[friendLinks][<?php echo (int) $idx; ?>][name]" value="<?php echo esc_attr(isset($link['name']) ? (string) $link['name'] : ''); ?>" placeholder="站点名称" /></td>
                            <td><input type="text" name="argon_astro_config[friendLinks][<?php echo (int) $idx; ?>][url]" value="<?php echo esc_attr(isset($link['url']) ? (string) $link['url'] : ''); ?>" placeholder="https://..." /></td>
                            <td><input type="text" name="argon_astro_config[friendLinks][<?php echo (int) $idx; ?>][description]" value="<?php echo esc_attr(isset($link['description']) ? (string) $link['description'] : ''); ?>" placeholder="一句话介绍" /></td>
                            <td><input type="text" name="argon_astro_config[friendLinks][<?php echo (int) $idx; ?>][avatar]" value="<?php echo esc_attr(isset($link['avatar']) ? (string) $link['avatar'] : ''); ?>" placeholder="https://..." /></td>
                            <td><button type="button" class="button button-link-delete argon-remove-link">删除</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="button" class="button button-secondary" id="argon_add_friend_link">+ 添加友链</button>
        </section>

        <section class="argon-astro-section" id="sec-footer-html" data-card-title="页脚与自定义代码">
            <div class="argon-card-head">
                <h2>页脚与自定义代码</h2>
            </div>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th><label>页脚 HTML 与版权</label></th>
                        <td>
                            <textarea rows="5" class="large-text code" name="argon_astro_config[footer][html]"><?php echo esc_textarea($footer['html']); ?></textarea>
                            <p style="margin-top:8px;">
                                <input type="hidden" name="argon_astro_config[footer][hideAuthor]" value="0" />
                                <label><input type="checkbox" name="argon_astro_config[footer][hideAuthor]" value="1" <?php checked($footer['hideAuthor'], true); ?> /> 隐藏页脚主题作者署名</label>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><label>自定义页头 / 页脚 HTML</label></th>
                        <td>
                            <p><label>页头自定义 HTML（插入到 <code>&lt;head&gt;</code> 中）<br /><textarea rows="4" class="large-text code" name="argon_astro_config[appearance][customHeadHtml]"><?php echo esc_textarea($app['customHeadHtml']); ?></textarea></label></p>
                            <p><label>页脚自定义 HTML（插入到 <code>&lt;/body&gt;</code> 之前）<br /><textarea rows="4" class="large-text code" name="argon_astro_config[appearance][customFootHtml]"><?php echo esc_textarea($app['customFootHtml']); ?></textarea></label></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
    <?php
}
