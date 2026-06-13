<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
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
