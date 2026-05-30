<?php
/**
 * Admin Class for Internet Melli Plugin
 *
 * Handles all admin-related functionality
 */
class Internet_Melli_Admin {
    private $plugin_name;
    private $version;
    private $updater;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->updater = new Internet_Melli_Updater($version);
    }

    /**
     * Add admin menu page
     */
    public function add_menu_page() {
        add_menu_page(
            __('مسدودکننده سایت‌های خارجی', 'internet-melli'),
            __('مسدودکننده خارجی', 'internet-melli'),
            'manage_options',
            'internet-melli',
            array($this, 'render_admin_page'),
            'dashicons-lock',
            100
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'internet_melli_settings',
            'internet_melli_enabled',
            array(
                'type' => 'integer',
                'sanitize_callback' => 'absint',
                'default' => 0
            )
        );

        register_setting(
            'internet_melli_settings',
            'internet_melli_blocked_domains_frontend',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default' => 'gravatar.com,googleapis.com,unpkg.com,github,fonts.googleapis.com,google,cdnjs,cloudflare,microsoft,clarity,fontawesome.com,ps.w.org'
            )
        );

        register_setting(
            'internet_melli_settings',
            'internet_melli_blocked_domains_backend',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default' => 'gravatar.com,googleapis.com,unpkg.com,github,fonts.googleapis.com,google,cdnjs,cloudflare,microsoft,clarity,fontawesome.com,ps.w.org'
            )
        );

        register_setting(
            'internet_melli_settings',
            'internet_melli_version',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => '1.2'
            )
        );

        register_setting(
            'internet_melli_settings',
            'internet_melli_sw_guarantee',
            array(
                'type' => 'integer',
                'sanitize_callback' => 'absint',
                'default' => 0
            )
        );
    }

    /**
     * Render admin page
     */
    public function render_admin_page() {
        $enabled = get_option('internet_melli_enabled', 0);
        $sw_guarantee = get_option('internet_melli_sw_guarantee', 0);


$blocked_domains_frontend = get_option(
    'internet_melli_blocked_domains_frontend',
    'gravatar.com,googleapis.com,unpkg.com,github,fonts.googleapis.com,google,cdnjs,cloudflare,microsoft,clarity,fontawesome.com,ps.w.org'
);

$blocked_domains_backend = get_option(
    'internet_melli_blocked_domains_backend',
    'wordpress.org,googleapis.com'
);

        $current_version = $plugin_data['Version'];
        ?>
        <div class="internet-melli-admin-wrapper">
            <!-- Header Section -->
            <div class="im-admin-header">
                <div class="im-header-content">
                    <div class="im-header-right">
                        <h1 class="im-main-title">
                            <span class="im-icon">🛡️</span>
                            <?php echo esc_html__('مسدودکننده سایت‌های خارجی', 'internet-melli'); ?>
                        </h1>
                        <p class="im-subtitle"><?php echo esc_html__('مدیریت و کنترل دسترسی به منابع خارجی', 'internet-melli'); ?></p>
                    </div>
                    <div class="im-header-left">
                        <span class="im-badge im-badge-<?php echo $enabled ? 'active' : 'inactive'; ?>">
                            <?php echo $enabled ? esc_html__('فعال', 'internet-melli') : esc_html__('غیرفعال', 'internet-melli'); ?>
                        </span>
                        <span class="im-version-badge">v<?php echo esc_html($this->version); ?></span>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="im-admin-grid">
                <!-- Settings Card -->
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
                                        <label for="internet_melli_enabled"><?php echo esc_html__('فعال سازی مسدودکننده', 'internet-melli'); ?></label>
                                        <p class="im-description"><?php echo esc_html__('با فعال کردن این گزینه، ریکوئستر مسدودکننده سایت‌های خارجی در سایت فعال می‌شود', 'internet-melli'); ?></p>
                                    </div>
                                    <div class="im-form-control">
                                        <label class="im-switch">
                                            <input type="checkbox" id="internet_melli_enabled" name="internet_melli_enabled" value="1" <?php checked($enabled, 1); ?>>
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
                                            <?php echo esc_html__('در برخی سایت‌ها ممکن است آدرس sw.js به درستی مسیردهی نشود و خطای 404 بدهد. با فعال کردن این گزینه یک فایل sw.js در روت وردپرس ساخته می‌شود تا همیشه در دسترس باشد.', 'internet-melli'); ?>
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
        <?php echo esc_html__('دامنه‌های خارج ایران را به صورت جداگانه برای فرانت‌اند و بک‌اند تنظیم کنید.
         تنظیم صحیح دامنه های بک اند خارجی باعث بهبود سرعت پیشخوان شما و کاربر بازدید کننده سایت میشوند.
          کنترل دامنه های فرانت اند باعث بهبود سرعت نمایش برای کاربر بازدید کننده سایت میشود.', 'internet-melli'); ?>
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
$backend_domains = get_option( 'internet_melli_blocked_domains_backend', '' );
?>
<input type="hidden"
       id="internet_melli_blocked_domains_backend"
       name="internet_melli_blocked_domains_backend"
       value="<?php echo esc_attr( $backend_domains ); ?>">

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
                                <h3 style="color:#cc0000;">حذف کامل اطلاعات پلاگین</h3>
                                <p>با زدن این دکمه تمام تنظیمات ذخیره‌شده در دیتابیس حذف می‌شود. این عملیات غیرقابل بازگشت است.</p>

                                <button type="button"
                                        id="internet_melli_delete_all_btn"
                                        class="button button-secondary"
                                        style="border-color:#cc0000; color:#cc0000;">
                                    حذف همه اطلاعات از دیتابیس
                                </button>

                                <p id="internet_melli_delete_all_result" style="margin-top:10px; display:none;"></p>
                            </div>


                    </div>
                </div>

                <!-- Sidebar Cards -->
                <div class="im-sidebar">
                    <!-- Plugin Info Card -->
                    <div class="im-card im-card-info">
                        <div class="im-card-header">
                            <h3><span class="dashicons dashicons-info"></span> <?php echo esc_html__('اطلاعات افزونه', 'internet-melli'); ?></h3>
                        </div>
                        <div class="im-card-body">
                            <ul class="im-info-list">
                                <li>
                                    <span class="im-info-label"><?php echo esc_html__('نسخه افزونه', 'internet-melli'); ?></span>
                                    <span class="im-info-value"><?php echo esc_html($this->version); ?></span>
                                </li>
                                <li>
                                    <span class="im-info-label"><?php echo esc_html__('وضعیت', 'internet-melli'); ?></span>
                                    <span class="im-info-value im-info-activity">
                                        <span class="im-status-dot im-status-<?php echo $enabled ? 'active' : 'inactive'; ?>"></span>
                                        <?php echo $enabled ? esc_html__('فعال', 'internet-melli') : esc_html__('غیرفعال', 'internet-melli'); ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Guide Card -->
                    <div class="im-card im-card-guide">
                        <div class="im-card-header">
                            <h3><span class="dashicons dashicons-editor-help"></span> <?php echo esc_html__('راهنما', 'internet-melli'); ?></h3>
                        </div>
                        <div class="im-card-body">
                            <p class="im-guide-text"><?php echo esc_html__('جهت فعالسازی کامل اقدامات زیر را انجام دهید', 'internet-melli'); ?></p>
                            <div class="im-guide-steps">
                                <div class="im-step">
                                    <span class="im-step-number">1</span>
                                    <span><?php echo esc_html__('افزودن دامنه های موردنظر', 'internet-melli'); ?></span>
                                </div>
                                <div class="im-step">
                                    <span class="im-step-number">2</span>
                                    <span><?php echo esc_html__('ذخیره تنظیمات', 'internet-melli'); ?></span>
                                </div>
                                <div class="im-step">
                                    <span class="im-step-number">3</span>
                                    <span><?php echo esc_html__('بررسی پیوندهای یکتا', 'internet-melli'); ?></span>
                                </div>
                                <div class="im-step">
                                    <span class="im-step-number">4</span>
                                    <span><?php echo esc_html__('بررسی بلاک های پیشنهادشده', 'internet-melli'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Test Card -->
                    <div class="im-card im-card-test">
                        <div class="im-card-header">
                            <h3><span class="dashicons dashicons-performance"></span> <?php echo esc_html__('تست ریکوئستر', 'internet-melli'); ?></h3>
                        </div>
                        <div class="im-card-body">
                            <p class="im-test-desc"><?php echo esc_html__('وضعیت ریکوئستر را بررسی کنید', 'internet-melli'); ?></p>
                            <button type="button" id="internet-melli-test-btn" class="im-btn im-btn-secondary">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <?php echo esc_html__('تست وضعیت', 'internet-melli'); ?>
                            </button>
                            <div id="internet-melli-test-result" class="im-test-result"></div>
                        </div>
                    </div>
                    
                    <!-- Update Card -->
<div class="im-card im-card-update">
    <div class="im-card-header im-card-header-update">
        <h3><span class="dashicons dashicons-update"></span> <?php echo esc_html__('آپدیت افزونه', 'internet-melli'); ?></h3>
    </div>
    <div class="im-card-body">
        <div class="im-update-current">
            <span class="im-update-label"><?php echo esc_html__('نسخه فعلی:', 'internet-melli'); ?></span>
            <span class="im-update-version">v<?php echo esc_html($this->version); ?></span>
        </div>
        
        <button type="button" id="im-check-update-btn" class="im-btn im-btn-primary im-btn-block">
            <span class="dashicons dashicons-search"></span>
            <?php echo esc_html__('بررسی آپدیت', 'internet-melli'); ?>
        </button>
        
        <div id="im-check-result" class="im-update-result"></div>
        
        <div id="im-update-section" class="im-update-available" style="display: none;">
            <div class="im-update-info">
                <span class="dashicons dashicons-yes-alt"></span>
                <span><?php echo esc_html__('نسخه جدید موجود است!', 'internet-melli'); ?></span>
            </div>
            <div class="im-update-version-info">
                <span class="im-old-version">v<?php echo esc_html($this->version); ?></span>
                <span class="im-arrow">→</span>
                <span class="im-new-version" id="im-new-version-display"></span>
            </div>
            <div id="im-release-notes" class="im-release-notes"></div>
            <button type="button" id="im-install-update-btn" class="im-btn im-btn-success im-btn-block">
                <span class="dashicons dashicons-download"></span>
                <?php echo esc_html__('دانلود و نصب آپدیت', 'internet-melli'); ?>
            </button>
        </div>
        
        <div id="im-update-loading" class="im-update-loading" style="display: none;">
            <div class="im-update-spinner"></div>
            <span id="im-update-loading-text"><?php echo esc_html__('در حال پردازش...', 'internet-melli'); ?></span>
        </div>
        
        <div id="im-update-message" class="im-update-message"></div>
    </div>
</div>

<!-- feedback Card -->
<div class="im-card im-card-feedback">
    <div class="im-card-header im-card-header-feedback">
        <h3>
            <span class="dashicons dashicons-feedback"></span>
            <?php echo esc_html__('ارسال فیدبک', 'internet-melli'); ?>
        </h3>
    </div>

    <div class="im-card-body">
        
        <form id="im-feedback-form" method="post">

            <!-- نانس هماهنگ با wp_localize_script -->
            <input type="hidden"
                   id="im_feedback_nonce"
                   name="im_feedback_nonce"
                   value="<?php echo wp_create_nonce('internet_melli_nonce'); ?>">

            <input type="hidden"
                   name="user"
                   value="<?php echo esc_attr($_SERVER['HTTP_HOST']); ?>">

            <div class="im-feedback-field">
                <label for="im-feedback-text">
                    <?php echo esc_html__('منتظر نظراتتون هستیم 💬', 'internet-melli'); ?>
                </label>

                <textarea id="im-feedback-text"
                          name="text"
                          rows="4"
                          class="im-feedback-textarea"
                          placeholder="<?php echo esc_attr__('لطفاً پیام خود را بنویسید...', 'internet-melli'); ?>"
                          required></textarea>
            </div>

            <button type="submit"
                    id="im-send-feedback-btn"
                    class="im-btn im-btn-primary im-btn-block">
                <span class="dashicons dashicons-send"></span>
                <?php echo esc_html__('ارسال', 'internet-melli'); ?>
            </button>

        </form>

        <div id="im-feedback-result" class="im-feedback-result"></div>

        <div id="im-feedback-loading"
             class="im-feedback-loading"
             style="display: none;">
            <div class="im-feedback-spinner"></div>
            <span><?php echo esc_html__('در حال ارسال...', 'internet-melli'); ?></span>
        </div>

    </div>
</div>


                    <!-- Contact Card -->
                    <div class="im-card im-card-contact">
                        <div class="im-card-header im-card-header-contact">
                            <h3><span class="dashicons dashicons-email"></span> <?php echo esc_html__('ارتباط با ما', 'internet-melli'); ?></h3>
                        </div>
                        <div class="im-card-body">
                            <div class="im-contact-company">
                                <div class="im-company-logo">
                                    <span class="im-logo-icon">🌐</span>
                                </div>
                                <h4 class="im-company-name"><?php echo esc_html__('تلاش نت', 'internet-melli'); ?></h4>
                                <p class="im-company-tagline"><?php echo esc_html__('راهکاری جامع در فناوری اطلاعات', 'internet-melli'); ?></p>
                            </div>
                            <div class="im-contact-links">
                                <a href="https://talashnet.com" target="_blank" class="im-contact-link">
                                    <span class="dashicons dashicons-admin-site"></span>
                                    <?php echo esc_html__('وب سایت', 'internet-melli'); ?>
                                </a>
                                <a href="mailto:info@talashnet.com" class="im-contact-link">
                                    <span class="dashicons dashicons-email-alt"></span>
                                    <?php echo esc_html__('ایمیل', 'internet-melli'); ?>
                                </a>
                                <a href="https://talashnet.com/contactus" target="_blank" class="im-contact-link">
                                    <span class="dashicons dashicons-phone"></span>
                                    <?php echo esc_html__('تماس', 'internet-melli'); ?>
                                </a>
                            </div>
                            <div class="im-social-links">
                                <a href="https://ble.ir/talashnet" target="_blank" class="im-social-btn" title="پیام رسان بله">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg" class="Hz484Q"><path fill-rule="evenodd" clip-rule="evenodd" d="M7.69592 13.3899C7.05115 13.3899 6.52734 12.8661 6.52734 12.2214C6.52734 11.5785 7.05115 11.0547 7.69592 11.0547C8.34068 11.0547 8.86449 11.5785 8.86449 12.2214C8.86449 12.8661 8.34068 13.3899 7.69592 13.3899ZM12.2388 13.3899C11.594 13.3899 11.0702 12.8661 11.0702 12.2214C11.0702 11.5785 11.594 11.0547 12.2388 11.0547C12.8835 11.0547 13.4073 11.5785 13.4073 12.2214C13.4073 12.8661 12.8835 13.3899 12.2388 13.3899ZM15.6132 12.2214C15.6132 12.8661 16.137 13.3899 16.7817 13.3899C17.4265 13.3899 17.9503 12.8661 17.9503 12.2214C17.9503 11.5785 17.4265 11.0547 16.7817 11.0547C16.137 11.0547 15.6132 11.5785 15.6132 12.2214Z" fill="#00B894"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M12.02 2C6.21 2 2 6.74612 2 12.015C2 13.6975 2.49 15.4291 3.35 17.0115C3.51 17.2729 3.53 17.6024 3.42 17.9139L2.75 20.1572C2.6 20.698 3.06 21.0976 3.57 20.9374L5.59 20.3375C6.14 20.1572 6.57 20.3866 7.08 20.698C8.54 21.5583 10.36 22 12 22C16.96 22 22 18.1642 22 11.985C22 6.65598 17.7 2 12.02 2Z" stroke="#00B894" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                </a>
                                <a href="https://t.me/talashnet" target="_blank" class="im-social-btn" title="تلگرام">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18 1.897-.962 6.502-1.359 8.627-.168.9-.5 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                                </a>
                                <a href="https://instagram.com/talashnet" target="_blank" class="im-social-btn" title="اینستاگرام">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>

        </style>
        <?php
    }

    /**
     * Enqueue admin styles
     */
    public function enqueue_styles($hook) {
        if ('toplevel_page_internet-melli' !== $hook) {
            return;
        }
        wp_enqueue_style(
            $this->plugin_name . '-admin',
            INTERNET_MELLI_URL . 'assets/css/admin-style.css',
            array(),
            $this->version
        );
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_scripts($hook) {
        if ('toplevel_page_internet-melli' !== $hook) {
            return;
        }
        wp_enqueue_script(
            $this->plugin_name . '-admin',
            INTERNET_MELLI_URL . 'assets/js/admin-settings.js',
            array('jquery'),
            $this->version,
            true
        );

       wp_localize_script(
    $this->plugin_name . '-admin',
    'internetMelli',
    array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('internet_melli_nonce'),
        'strings' => array(
            'saving' => __('در حال ذخیره...', 'internet-melli'),
            'saved' => __('تنظیمات با موفقیت ذخیره شد!', 'internet-melli'),
            'error' => __('خطایی رخ داد. لطفا دوباره تلاش کنید.', 'internet-melli'),
            'testing' => __('در حال تست...', 'internet-melli'),
            'testsw' => __('تست وضعیت', 'internet-melli'),
            'test_success' => __('ریکوئستر فعال است!', 'internet-melli'),
            'test_inactive' => __('ریکوئستر فعال نیست.', 'internet-melli'),
            'domain_empty' => __('لطفاً یک دامنه وارد کنید', 'internet-melli'),
            'domain_invalid' => __('لطفاً یک دامنه معتبر وارد کنید', 'internet-melli'),
            'domain_exists' => __('این دامنه قبلاً اضافه شده است', 'internet-melli'),
            'domain_added' => __('دامنه اضافه شد', 'internet-melli'),
            'domain_removed' => __('دامنه حذف شد', 'internet-melli'),
            'domain_updated' => __('دامنه ویرایش شد', 'internet-melli'),
            'no_domains' => __('هنوز دامنه‌ای اضافه نشده است', 'internet-melli'),
            'checking'        => __('در حال بررسی...', 'internet-melli'),
            'check_update'    => __('بررسی آپدیت', 'internet-melli'),
            'install_update'  => __('دانلود و نصب آپدیت', 'internet-melli'),
            'update_confirm'  => __('آیا مطمئن هستید که می‌خواهید افزونه را آپدیت کنید؟', 'internet-melli'),
            'update_available'=> __('نسخه جدید یافت شد!', 'internet-melli'),
            'no_update'       => __('شما از آخرین نسخه استفاده می‌کنید', 'internet-melli'),
            'release_notes'   => __('تغییرات:', 'internet-melli'),
            'updating'        => __('در حال آپدیت...', 'internet-melli'),
            'no_download_url' => __('لینک دانلود یافت نشد', 'internet-melli')

        )
    )
);
}

    /**
     * Handle AJAX toggle request
     */
    public function handle_toggle() {
        check_ajax_referer('internet_melli_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('دسترسی غیرمجاز', 'internet-melli')));
        }

        $sw_guarantee = isset($_POST['sw_guarantee']) ? intval($_POST['sw_guarantee']) : 0;
        update_option('internet_melli_sw_guarantee', $sw_guarantee);


$enabled = isset($_POST['enabled']) ? intval($_POST['enabled']) : 0;

$blocked_domains_frontend = isset($_POST['blocked_domains_frontend'])
    ? sanitize_textarea_field($_POST['blocked_domains_frontend'])
    : '';

// $blocked_domains_backend = isset($_POST['blocked_domains_backend'])
//     ? sanitize_textarea_field($_POST['blocked_domains_backend'])
//     : '';

update_option('internet_melli_enabled', $enabled);
update_option('internet_melli_blocked_domains_frontend', $blocked_domains_frontend);
// update_option('internet_melli_blocked_domains_backend', $blocked_domains_backend);

if ( isset($_POST['blocked_domains_backend']) ) {
    $backend_domains = sanitize_text_field( wp_unslash( $_POST['blocked_domains_backend'] ) );
    update_option( 'internet_melli_blocked_domains_backend', $backend_domains );
}


$sw_file = ABSPATH . 'sw.js';

if ($sw_guarantee) {

    if ( class_exists('Internet_Melli') && method_exists('Internet_Melli', 'generate_sw_content') ) {

        $sw_content = Internet_Melli::generate_sw_content();
        $written = false;

        // ✅ روش اول: file_put_contents
        if ( is_writable(ABSPATH) || ( file_exists($sw_file) && is_writable($sw_file) ) ) {
            $result = @file_put_contents($sw_file, $sw_content);
            if ($result !== false) {
                $written = true;
            }
        }

        // ✅ اگر روش اول شکست خورد → WP Filesystem
        if ( ! $written ) {

            require_once ABSPATH . 'wp-admin/includes/file.php';

            $creds = request_filesystem_credentials('', '', false, false, null);

            if ( WP_Filesystem($creds) ) {
                global $wp_filesystem;

                if ( $wp_filesystem->put_contents($sw_file, $sw_content, FS_CHMOD_FILE) ) {
                    $written = true;
                }
            }
        }

        // ✅ اگر هر دو روش شکست خورد
        if ( ! $written ) {
            wp_send_json_error(array(
                'message' => 'امکان ایجاد فایل sw.js وجود ندارد. دسترسی نوشتن روی روت وردپرس بررسی شود.'
            ));
        }
    }

} else {

    $deleted = false;

    // ✅ حذف با روش معمولی
    if ( file_exists($sw_file) ) {
        if ( @unlink($sw_file) ) {
            $deleted = true;
        }
    }

    // ✅ اگر حذف معمولی نشد → WP Filesystem
    if ( ! $deleted && file_exists($sw_file) ) {

        require_once ABSPATH . 'wp-admin/includes/file.php';

        $creds = request_filesystem_credentials('', '', false, false, null);

        if ( WP_Filesystem($creds) ) {
            global $wp_filesystem;

            if ( $wp_filesystem->delete($sw_file) ) {
                $deleted = true;
            }
        }
    }
}





        // Flush rewrite rules
        flush_rewrite_rules();

        wp_send_json_success(array(
            'message' => __('تنظیمات ذخیره شد!', 'internet-melli'),
            'enabled' => $enabled
        ));
    }

    /**
     * Handle AJAX test request
     */
    public function handle_test() {
        check_ajax_referer('internet_melli_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('دسترسی غیرمجاز', 'internet-melli')));
        }

        $enabled = get_option('internet_melli_enabled', 0);

        wp_send_json_success(array(
            'enabled' => $enabled,
            'message' => $enabled ? __('ریکوئستر فعال است', 'internet-melli') : __('ریکوئستر غیرفعال است', 'internet-melli')
        ));
    }
}