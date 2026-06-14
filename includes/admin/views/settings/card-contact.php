<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
                        <div class="im-card im-card-contact">
                            <div class="im-card-header im-card-header-contact">
                                <h3><span class="dashicons dashicons-email"></span> <?php echo esc_html__('ارتباط با ما', 'talashnet-external-request-blocker'); ?></h3>
                            </div>
                            <div class="im-card-body">
                                <div class="im-contact-company">
                                    <div class="im-company-logo">
                                        <span class="im-logo-icon">🌐</span>
                                    </div>
                                    <h4 class="im-company-name"><?php echo esc_html__('تلاش نت', 'talashnet-external-request-blocker'); ?></h4>
                                    <p class="im-company-tagline"><?php echo esc_html__('راهکاری جامع در فناوری اطلاعات', 'talashnet-external-request-blocker'); ?></p>
                                </div>
                                <div class="im-contact-links">
                                    <a href="https://talashnet.com" target="_blank" class="im-contact-link">
                                        <span class="dashicons dashicons-admin-site"></span>
                                        <?php echo esc_html__('وب سایت', 'talashnet-external-request-blocker'); ?>
                                    </a>
                                    <a href="mailto:info@talashnet.com" class="im-contact-link">
                                        <span class="dashicons dashicons-email-alt"></span>
                                        <?php echo esc_html__('ایمیل', 'talashnet-external-request-blocker'); ?>
                                    </a>
                                    <a href="https://talashnet.com/contactus" target="_blank" class="im-contact-link">
                                        <span class="dashicons dashicons-phone"></span>
                                        <?php echo esc_html__('تماس', 'talashnet-external-request-blocker'); ?>
                                    </a>
                                </div>
                                <div class="im-social-links">
                                    <a href="https://ble.ir/talashnet" target="_blank" class="im-social-btn" title="<?php echo esc_attr__('پیام رسان بله', 'talashnet-external-request-blocker'); ?>">
                                        <?php Tnet_Admin_Svg::render('bale'); ?>
                                    </a>
                                    <a href="https://t.me/talashnet" target="_blank" class="im-social-btn" title="<?php echo esc_attr__('تلگرام', 'talashnet-external-request-blocker'); ?>">
                                        <?php Tnet_Admin_Svg::render('telegram'); ?>
                                    </a>
                                    <a href="https://instagram.com/talashnet" target="_blank" class="im-social-btn" title="<?php echo esc_attr__('اینستاگرام', 'talashnet-external-request-blocker'); ?>">
                                        <?php Tnet_Admin_Svg::render('instagram'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
