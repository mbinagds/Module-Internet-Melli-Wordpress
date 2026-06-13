<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
                    <div class="im-card im-card-main">
                        <div class="im-card-header">
                            <h2><span class="dashicons dashicons-admin-settings"></span> <?php echo esc_html__('تنظیمات افزونه', 'internet-melli'); ?></h2>
                        </div>
                        <div class="im-card-body">
                            <form method="post" id="internet-melli-form">
                                <?php wp_nonce_field('internet_melli_nonce', 'internet_melli_nonce'); ?>

                                <div class="im-form-group">
                                    <div class="im-form-row">
                                        <div class="im-form-label">
                                            <label for="internet_melli_backend_enabled">
                                                <?php echo esc_html__('فعالسازی مسدودکننده بک اند', 'internet-melli'); ?>
                                            </label>
                                            <p class="im-description">
                                                <?php echo esc_html__('این گزینه بسیار کاربردی است و پیشنهاد میشود در اکثر سایت ها فعال باشد.', 'internet-melli'); ?>
                                            </p>
                                        </div>
                                        <div class="im-form-control">
                                            <label class="im-switch">
                                                <input type="checkbox"
                                                    id="internet_melli_backend_enabled"
                                                    name="internet_melli_backend_enabled"
                                                    value="1"
                                                    <?php checked($backend_enabled, 1); ?>>
                                                <span class="im-slider im-round"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="im-form-group">
                                    <div class="im-form-row">
                                        <div class="im-form-label">
                                            <label for="internet_melli_enabled">
                                                <?php echo esc_html__('فعال سازی مسدودکننده فرانت اند (ریکوئستر)', 'internet-melli'); ?>
                                            </label>
                                            <p class="im-description">
                                                <?php echo esc_html__('این گزینه اختیاری است. این گزینه در شرایط اختلال شدید اینترنت کاربرد دارد و برای سایت‌هایی که به‌دلیل لود نشدن فونت گوگل یا سایر منابع خارجی کاربران با صفحه سفید مواجه می‌شوند، راهکار مناسبی محسوب می‌شود.', 'internet-melli'); ?>
                                            </p>
                                        </div>
                                        <div class="im-form-control">
                                            <label class="im-switch">
                                                <input type="checkbox"
                                                    id="internet_melli_enabled"
                                                    name="internet_melli_enabled"
                                                    value="1"
                                                    <?php checked($enabled, 1); ?>>
                                                <span class="im-slider im-round"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="im-form-group">
                                    <div class="im-form-row">
                                        <div class="im-form-label">
                                            <label for="internet_melli_sw_guarantee">
                                                <?php echo esc_html__('تضمین ریکوئستر', 'internet-melli'); ?>
                                            </label>
                                            <p class="im-description">
                                                <?php echo esc_html__('این گزینه اختیاری است. در برخی سایت‌ها ممکن است آدرس sw.js به درستی مسیردهی نشود و خطای 404 بدهد. با فعال کردن این گزینه یک فایل sw.js در روت وردپرس ساخته می‌شود تا همیشه در دسترس باشد.', 'internet-melli'); ?>
                                            </p>
                                        </div>
                                        <div class="im-form-control">
                                            <label class="im-switch">
                                                <input type="checkbox"
                                                    id="internet_melli_sw_guarantee"
                                                    name="internet_melli_sw_guarantee"
                                                    value="1"
                                                    <?php checked($sw_guarantee, 1); ?>>
                                                <span class="im-slider im-round"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>


                                <div class="im-form-group">
                                    <div class="im-form-row">
                                        <div class="im-form-label">
                                            <label>
                                                <?php echo esc_html__('لیست دامنه‌های مسدود شده', 'internet-melli'); ?>
                                            </label>
                                            <p class="im-description">
                                                <?php echo esc_html__('دامنه‌های خارج ایران را به صورت جداگانه برای فرانت‌اند و بک‌اند تنظیم کنید. تنظیم صحیح دامنه های بک اند خارجی باعث بهبود سرعت پیشخوان شما و کاربر بازدید کننده سایت میشوند. کنترل دامنه های فرانت اند باعث بهبود سرعت نمایش برای کاربر بازدید کننده سایت میشود.', 'internet-melli'); ?>
                                            </p>

                                            <div class="im-domain-columns" style="display: flex; gap: 20px; margin-top: 15px;">

                                                <!-- ستون چپ: لیست دامنه‌های مسدود شده بک‌اند -->
                                                <div class="im-domain-column" style="flex: 1;">
                                                    <h4 style="margin-top:0;"><?php echo esc_html__('لیست دامنه‌های شناسایی شده بک‌اند', 'internet-melli'); ?></h4>

                                                    <div class="im-domain-input-wrapper">
                                                        <div class="im-domain-input-group">
                                                            <input type="text"
                                                                id="domain_input_backend"
                                                                class="im-text-input"
                                                                placeholder="example.com">
                                                            <button type="button"
                                                                id="add_domain_btn_backend"
                                                                class="im-btn im-btn-primary">
                                                                <?php echo esc_html__('افزودن', 'internet-melli'); ?>
                                                            </button>
                                                        </div>

                                                        <div id="domains_list_backend"
                                                            class="im-domains-box"
                                                            style="margin-top: 10px;"></div>
                                                    </div>

                                                    <?php
                                                    $backend_domains = get_option('internet_melli_blocked_domains_backend', '');
                                                    ?>
                                                    <input type="hidden"
                                                        id="internet_melli_blocked_domains_backend"
                                                        name="internet_melli_blocked_domains_backend"
                                                        value="<?php echo esc_attr($backend_domains); ?>">

                                                </div>

                                                <!-- ستون راست: لیست دامنه‌های مسدود شده فرانت‌اند -->
                                                <div class="im-domain-column" style="flex: 1;">
                                                    <h4 style="margin-top:0;"><?php echo esc_html__('لیست دامنه‌های مسدود شده فرانت‌اند', 'internet-melli'); ?></h4>

                                                    <div class="im-domain-input-wrapper">
                                                        <div class="im-domain-input-group">
                                                            <input type="text"
                                                                id="domain_input_frontend"
                                                                class="im-text-input"
                                                                placeholder="example.com">
                                                            <button type="button"
                                                                id="add_domain_btn_frontend"
                                                                class="im-btn im-btn-primary">
                                                                <?php echo esc_html__('افزودن', 'internet-melli'); ?>
                                                            </button>
                                                        </div>

                                                        <div id="domains_list_frontend"
                                                            class="im-domains-box"
                                                            style="margin-top: 10px;"></div>
                                                    </div>

                                                    <input type="hidden"
                                                        id="internet_melli_blocked_domains_frontend"
                                                        name="internet_melli_blocked_domains_frontend"
                                                        value="<?php echo esc_attr($blocked_domains_frontend); ?>">
                                                </div>

                                            </div>
                                        </div>


                                        <input type="hidden" id="internet_melli_blocked_domains_frontend"
                                            name="internet_melli_blocked_domains_frontend"
                                            value="<?php echo esc_attr($blocked_domains); ?>">
                                    </div>
                                </div>


                                <div class="im-form-actions">
                                    <?php submit_button(esc_html__('ذخیره تنظیمات', 'internet-melli'), 'primary im-btn-primary', 'internet_melli_submit', true); ?>
                                    <span class="im-saving-indicator" style="display: none;">
                                        <span class="im-spinner"></span>
                                        <?php echo esc_html__('در حال ذخیره...', 'internet-melli'); ?>
                                    </span>
                                </div>


                            </form>
                            <div id="internet-melli-message"></div>



                            <div style="margin-top:30px; padding:15px; border:1px solid #cc0000; background:#ffecec;">
                                <h3 style="color:#cc0000;"><?php echo esc_html__('حذف کامل اطلاعات پلاگین', 'internet-melli'); ?></h3>
                                <p><?php echo esc_html__('با زدن این دکمه تمام تنظیمات ذخیره‌شده در دیتابیس حذف می‌شود. این عملیات غیرقابل بازگشت است.', 'internet-melli'); ?></p>

                                <button type="button"
                                    id="internet_melli_delete_all_btn"
                                    class="button button-secondary"
                                    style="border-color:#cc0000; color:#cc0000;">
                                    <?php echo esc_html__('حذف همه اطلاعات از دیتابیس', 'internet-melli'); ?>
                                </button>

                                <p id="internet_melli_delete_all_result" style="margin-top:10px; display:none;"></p>
                            </div>


                        </div>
                    </div>
