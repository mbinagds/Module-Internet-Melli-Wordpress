<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
            <div class="im-tabs-container">
                <div class="im-tabs-header">
                    <div class="im-tabs">
                        <a href="?page=internet-melli&tab=settings"
                            class="im-tab <?php echo $active_tab == 'settings' ? 'im-tab-active' : ''; ?>">
                            <?php echo esc_html__('تنظیمات افزونه', 'internet-melli'); ?>
                        </a>

                        <a href="?page=internet-melli&tab=active-plugins"
                            class="im-tab <?php echo $active_tab == 'active-plugins' ? 'im-tab-active' : ''; ?>">
                            <?php echo esc_html__('فعال‌سازی اورژانسی افزونه‌ها', 'internet-melli'); ?>
                        </a>
                    </div>
                </div>
            </div>
