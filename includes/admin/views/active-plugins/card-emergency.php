<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
            <div class="im-card im-card-main">

                <div class="im-card-header">
                    <h2>
                        <span class="dashicons dashicons-warning"></span>
                        <?php echo esc_html__('ابزار مدیریت اضطراری افزونه‌ها', 'internet-melli'); ?>
                    </h2>
                </div>

                <div class="im-card-body">

                    <p class="im-description im-description-highlight">
                        <?php echo esc_html__('اگر برای نصب در سایت دیگر خود، به دلیل خطاهای PHP یا کندی شدید سایت جدیدتان امکان ورود به پیشخوان وردپرس را ندارید، این ابزار به شما کمک می‌کند افزونه‌های مشکل‌دار را به‌صورت مستقیم و اضطراری مدیریت کنید.', 'internet-melli'); ?>
                    </p>

                    <div class="im-guide-steps im-guide-steps-spaced">

                        <div class="im-step">
                            <div class="im-step-number">1</div>
                            <div>
                                <?php echo esc_html__('فایل ابزار اورژانسی را دانلود کنید.', 'internet-melli'); ?>
                            </div>
                        </div>

                        <div class="im-step">
                            <div class="im-step-number">2</div>
                            <div>
                                <?php echo esc_html__('فایل را در مسیر public_html یا روت وردپرس هاست قرار دهید.', 'internet-melli'); ?>
                            </div>
                        </div>

                        <div class="im-step">
                            <div class="im-step-number">3</div>
                            <div>
                                <?php echo esc_html__('آدرس فایل را در مرورگر باز کنید.', 'internet-melli'); ?>
                                <div class="im-step-example">
                                    <?php echo esc_html__('مثال:', 'internet-melli'); ?>
                                    <code>yourdomain.ir/direct-plugin-manager.php</code>
                                </div>
                            </div>
                        </div>

                        <div class="im-step">
                            <div class="im-step-number">4</div>
                            <div>
                                <?php echo esc_html__('از صفحه بازشده افزونه‌ها را فعال یا غیرفعال کنید.', 'internet-melli'); ?>
                            </div>
                        </div>

                        <div class="im-step">
                            <div class="im-step-number">5</div>
                            <div>
                                <?php echo esc_html__('پس از اتمام کار، فایل نصب شده را حذف کنید.', 'internet-melli'); ?>
                            </div>
                        </div>

                    </div>

                    <div class="im-form-actions im-guide-download">
                        <a href="<?php echo esc_url(admin_url('admin-post.php?action=im_download_emergency_tool')); ?>"
                            class="im-btn im-btn-primary im-btn-download">
                            <span class="dashicons dashicons-download"></span>
                            <?php echo esc_html__('دانلود ابزار اورژانسی', 'internet-melli'); ?>
                        </a>
                    </div>


                </div>
            </div>
