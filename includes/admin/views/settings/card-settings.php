<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
                    <div class="im-card im-card-main">
                        <div class="im-card-header">
                            <h2><span class="dashicons dashicons-admin-settings"></span> <?php echo esc_html__('تنظیمات افزونه', 'talashnet-external-request-blocker'); ?></h2>
                        </div>
                        <div class="im-card-body">
                            <form method="post" id="tnet-form">
                                <?php wp_nonce_field('tnet_nonce', 'tnet_nonce'); ?>

                                <div class="im-form-group">
                                    <div class="im-form-row">
                                        <div class="im-form-label">
                                            <label for="tnet_backend_enabled">
                                                <?php echo esc_html__('فعالسازی مسدودکننده بک اند', 'talashnet-external-request-blocker'); ?>
                                            </label>
                                            <p class="im-description">
                                                <?php echo esc_html__('این گزینه بسیار کاربردی است و پیشنهاد میشود در اکثر سایت ها فعال باشد.', 'talashnet-external-request-blocker'); ?>
                                            </p>
                                        </div>
                                        <div class="im-form-control">
                                            <label class="im-switch">
                                                <input type="checkbox"
                                                    id="tnet_backend_enabled"
                                                    name="tnet_backend_enabled"
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
                                            <label for="tnet_enabled">
                                                <?php echo esc_html__('فعال سازی مسدودکننده فرانت اند (ریکوئستر)', 'talashnet-external-request-blocker'); ?>
                                            </label>
                                            <p class="im-description">
                                                <?php echo esc_html__('این گزینه اختیاری است. این گزینه در شرایط اختلال شدید اینترنت کاربرد دارد و برای سایت‌هایی که به‌دلیل لود نشدن فونت گوگل یا سایر منابع خارجی کاربران با صفحه سفید مواجه می‌شوند، راهکار مناسبی محسوب می‌شود.', 'talashnet-external-request-blocker'); ?>
                                            </p>
                                        </div>
                                        <div class="im-form-control">
                                            <label class="im-switch">
                                                <input type="checkbox"
                                                    id="tnet_enabled"
                                                    name="tnet_enabled"
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
                                            <label for="tnet_sw_guarantee">
                                                <?php echo esc_html__('تضمین ریکوئستر', 'talashnet-external-request-blocker'); ?>
                                            </label>
                                            <p class="im-description">
                                                <?php echo esc_html__('این گزینه اختیاری است. در برخی سایت‌ها ممکن است آدرس sw.js به درستی مسیردهی نشود و خطای 404 بدهد. با فعال کردن این گزینه یک فایل sw.js در روت وردپرس ساخته می‌شود تا همیشه در دسترس باشد.', 'talashnet-external-request-blocker'); ?>
                                            </p>
                                        </div>
                                        <div class="im-form-control">
                                            <label class="im-switch">
                                                <input type="checkbox"
                                                    id="tnet_sw_guarantee"
                                                    name="tnet_sw_guarantee"
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
                                                <?php echo esc_html__('لیست دامنه‌های مسدود شده', 'talashnet-external-request-blocker'); ?>
                                            </label>
                                            <p class="im-description">
                                                <?php echo esc_html__('دامنه‌های خارج ایران را به صورت جداگانه برای فرانت‌اند و بک‌اند تنظیم کنید. تنظیم صحیح دامنه های بک اند خارجی باعث بهبود سرعت پیشخوان شما و کاربر بازدید کننده سایت میشوند. کنترل دامنه های فرانت اند باعث بهبود سرعت نمایش برای کاربر بازدید کننده سایت میشود.', 'talashnet-external-request-blocker'); ?>
                                            </p>

                                            <div class="im-domain-columns" style="display: flex; gap: 20px; margin-top: 15px;">

                                                <!-- ستون چپ: لیست دامنه‌های مسدود شده بک‌اند -->
                                                <div class="im-domain-column" style="flex: 1;">
                                                    <h4 style="margin-top:0;"><?php echo esc_html__('لیست دامنه‌های شناسایی شده بک‌اند', 'talashnet-external-request-blocker'); ?></h4>

                                                    <div class="im-domain-input-wrapper">
                                                        <div class="im-domain-input-group">
                                                            <input type="text"
                                                                id="domain_input_backend"
                                                                class="im-text-input"
                                                                placeholder="example.com">
                                                            <button type="button"
                                                                id="add_domain_btn_backend"
                                                                class="im-btn im-btn-primary">
                                                                <?php echo esc_html__('افزودن', 'talashnet-external-request-blocker'); ?>
                                                            </button>
                                                        </div>

                                                        <div id="domains_list_backend"
                                                            class="im-domains-box"
                                                            style="margin-top: 10px;"></div>
                                                    </div>

                                                    <?php
                                                    $backend_domains = get_option('tnet_blocked_domains_backend', '');
                                                    ?>
                                                    <input type="hidden"
                                                        id="tnet_blocked_domains_backend"
                                                        name="tnet_blocked_domains_backend"
                                                        value="<?php echo esc_attr($backend_domains); ?>">

                                                </div>

                                                <!-- ستون راست: لیست دامنه‌های مسدود شده فرانت‌اند -->
                                                <div class="im-domain-column" style="flex: 1;">
                                                    <h4 style="margin-top:0;"><?php echo esc_html__('لیست دامنه‌های مسدود شده فرانت‌اند', 'talashnet-external-request-blocker'); ?></h4>

                                                    <div class="im-domain-input-wrapper">
                                                        <div class="im-domain-input-group">
                                                            <input type="text"
                                                                id="domain_input_frontend"
                                                                class="im-text-input"
                                                                placeholder="example.com">
                                                            <button type="button"
                                                                id="add_domain_btn_frontend"
                                                                class="im-btn im-btn-primary">
                                                                <?php echo esc_html__('افزودن', 'talashnet-external-request-blocker'); ?>
                                                            </button>
                                                        </div>

                                                        <div id="domains_list_frontend"
                                                            class="im-domains-box"
                                                            style="margin-top: 10px;"></div>
                                                    </div>

                                                    <input type="hidden"
                                                        id="tnet_blocked_domains_frontend"
                                                        name="tnet_blocked_domains_frontend"
                                                        value="<?php echo esc_attr($blocked_domains_frontend); ?>">
                                                </div>

                                            </div>
                                        </div>


                                        <input type="hidden" id="tnet_blocked_domains_frontend"
                                            name="tnet_blocked_domains_frontend"
                                            value="<?php echo esc_attr($blocked_domains); ?>">
                                    </div>
                                </div>


                                <div class="im-form-actions">
                                    <?php submit_button(esc_html__('ذخیره تنظیمات', 'talashnet-external-request-blocker'), 'primary im-btn-primary', 'tnet_submit', true); ?>
                                    <span class="im-saving-indicator" style="display: none;">
                                        <span class="im-spinner"></span>
                                        <?php echo esc_html__('در حال ذخیره...', 'talashnet-external-request-blocker'); ?>
                                    </span>
                                </div>


                            </form>
                            <div id="tnet-message"></div>



                            <div style="margin-top:30px; padding:15px; border:1px solid #cc0000; background:#ffecec;">
                                <h3 style="color:#cc0000;"><?php echo esc_html__('حذف کامل اطلاعات پلاگین', 'talashnet-external-request-blocker'); ?></h3>
                                <p><?php echo esc_html__('با زدن این دکمه تمام تنظیمات ذخیره‌شده در دیتابیس حذف می‌شود. این عملیات غیرقابل بازگشت است.', 'talashnet-external-request-blocker'); ?></p>

                                <button type="button"
                                    id="tnet_delete_all_btn"
                                    class="button button-secondary"
                                    style="border-color:#cc0000; color:#cc0000;">
                                    <?php echo esc_html__('حذف همه اطلاعات از دیتابیس', 'talashnet-external-request-blocker'); ?>
                                </button>

                                <p id="tnet_delete_all_result" style="margin-top:10px; display:none;"></p>
                            </div>


                        </div>
                    </div>
