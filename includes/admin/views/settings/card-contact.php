<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
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
                                    <a href="https://ble.ir/talashnet" target="_blank" class="im-social-btn" title="<?php echo esc_attr__('پیام رسان بله', 'internet-melli'); ?>">
                                        <?php Internet_Melli_Admin_Svg::render('bale'); ?>
                                    </a>
                                    <a href="https://t.me/talashnet" target="_blank" class="im-social-btn" title="<?php echo esc_attr__('تلگرام', 'internet-melli'); ?>">
                                        <?php Internet_Melli_Admin_Svg::render('telegram'); ?>
                                    </a>
                                    <a href="https://instagram.com/talashnet" target="_blank" class="im-social-btn" title="<?php echo esc_attr__('اینستاگرام', 'internet-melli'); ?>">
                                        <?php Internet_Melli_Admin_Svg::render('instagram'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
