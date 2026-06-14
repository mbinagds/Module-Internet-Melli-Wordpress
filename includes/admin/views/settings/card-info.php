<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
                        <div class="im-card im-card-info">
                            <div class="im-card-header">
                                <h3><span class="dashicons dashicons-info"></span> <?php echo esc_html__('اطلاعات افزونه', 'talashnet-external-request-blocker'); ?></h3>
                            </div>
                            <div class="im-card-body">
                                <ul class="im-info-list">
                                    <li>
                                        <span class="im-info-label"><?php echo esc_html__('نسخه افزونه', 'talashnet-external-request-blocker'); ?></span>
                                        <span class="im-info-value"><?php echo esc_html($this->version); ?></span>
                                    </li>
                                    <li>
                                        <span class="im-info-label"><?php echo esc_html__('موتور شناسایی', 'talashnet-external-request-blocker'); ?></span>
                                        <span class="im-info-value im-info-activity">
                                            <span class="im-status-dot im-status-active"></span>
                                            <?php echo esc_html__('فعال', 'talashnet-external-request-blocker'); ?>
                                        </span>
                                    </li>
                                    <li>
                                        <span class="im-info-label"><?php echo esc_html__('وضعیت مسدودکننده بک اند', 'talashnet-external-request-blocker'); ?></span>
                                        <span class="im-info-value im-info-activity">
                                            <span class="im-status-dot im-status-<?php echo $backend_enabled ? 'active' : 'inactive'; ?>"></span>
                                            <?php echo $backend_enabled ? esc_html__('فعال', 'talashnet-external-request-blocker') : esc_html__('غیرفعال', 'talashnet-external-request-blocker'); ?>
                                        </span>
                                    </li>
                                    <li>
                                        <span class="im-info-label"><?php echo esc_html__('وضعیت مسدودکننده فرانت اند', 'talashnet-external-request-blocker'); ?></span>
                                        <span class="im-info-value im-info-activity">
                                            <span class="im-status-dot im-status-<?php echo $enabled ? 'active' : 'inactive'; ?>"></span>
                                            <?php echo $enabled ? esc_html__('فعال', 'talashnet-external-request-blocker') : esc_html__('غیرفعال', 'talashnet-external-request-blocker'); ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
