<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
            <div class="im-tabs-container">
                <div class="im-tabs-header">
                    <div class="im-tabs">
                        <a href="?page=talashnet-external-request-blocker&tab=settings"
                            class="im-tab <?php echo $active_tab == 'settings' ? 'im-tab-active' : ''; ?>">
                            <?php echo esc_html__('تنظیمات افزونه', 'talashnet-external-request-blocker'); ?>
                        </a>

                        <a href="?page=talashnet-external-request-blocker&tab=active-plugins"
                            class="im-tab <?php echo $active_tab == 'active-plugins' ? 'im-tab-active' : ''; ?>">
                            <?php echo esc_html__('فعال‌سازی اورژانسی افزونه‌ها', 'talashnet-external-request-blocker'); ?>
                        </a>
                    </div>
                </div>
            </div>
