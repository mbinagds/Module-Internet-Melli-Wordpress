<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
            <div class="im-admin-header">
                <div class="im-header-content">
                    <div class="im-header-right">
                        <h1 class="im-main-title">
                            <span class="im-icon">🛡️</span>
                            <?php echo esc_html__('مسدودکننده سایت‌های خارجی', 'talashnet-external-request-blocker'); ?>
                        </h1>
                        <p class="im-subtitle"><?php echo esc_html__('مدیریت و کنترل دسترسی به منابع خارجی (افزونه اینترنت ملی)', 'talashnet-external-request-blocker'); ?></p>
                    </div>
                    <div class="im-header-left">
                        <!-- <span class="im-badge im-badge-<?php //echo $enabled ? 'active' : 'inactive'; 
                                                            ?>"> -->
                        <?php
                        //echo $enabled ? esc_html__('فعال', 'talashnet-external-request-blocker') : esc_html__('غیرفعال', 'talashnet-external-request-blocker'); 
                        ?>
                        <!--</span>-->
                        <span class="im-badge im-badge-active">
                            <?php echo esc_html__('موتور شناسایی: فعال', 'talashnet-external-request-blocker'); ?>
                        </span>
                        <span class="im-version-badge">v<?php echo esc_html($this->version); ?></span>
                    </div>
                </div>
            </div>
