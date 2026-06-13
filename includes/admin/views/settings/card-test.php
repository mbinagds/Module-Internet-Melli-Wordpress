<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
                        <div class="im-card im-card-test">
                            <div class="im-card-header">
                                <h3><span class="dashicons dashicons-performance"></span> <?php echo esc_html__('تست مسدودکننده فرانت اند (ریکوئستر)', 'internet-melli'); ?></h3>
                            </div>
                            <div class="im-card-body">
                                <p class="im-test-desc"><?php echo esc_html__('وضعیت ریکوئستر را بررسی کنید', 'internet-melli'); ?></p>
                                <p><?php echo esc_html__('تنها در زمان اختلال شدید اینترنت، نیاز به فعال بودن ریکوئستر هست', 'internet-melli'); ?></p>
                                <button type="button" id="internet-melli-test-btn" class="im-btn im-btn-secondary">
                                    <span class="dashicons dashicons-yes-alt"></span>
                                    <?php echo esc_html__('تست وضعیت', 'internet-melli'); ?>
                                </button>
                                <div id="internet-melli-test-result" class="im-test-result"></div>
                            </div>
                        </div>
