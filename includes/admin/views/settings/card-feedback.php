<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
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
                                            <br />
                                            <?php echo esc_html__('(در صورت تمایل راه ارتباطی خود را نیز ذکر کنید)', 'internet-melli'); ?>
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
