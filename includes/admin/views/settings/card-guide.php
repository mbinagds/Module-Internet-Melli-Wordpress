<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
                        <div class="im-card im-card-guide">
                            <div class="im-card-header">
                                <h3><span class="dashicons dashicons-editor-help"></span> <?php echo esc_html__('راهنما', 'talashnet-external-request-blocker'); ?></h3>
                            </div>
                            <div class="im-card-body">
                                <p class="im-guide-text" style="margin-bottom: 20px; font-weight: bold; color: #856404;">
                                    <?php echo esc_html__('برای اطمینان از عملکرد صحیح افزونه، موارد زیر را بررسی کنید:', 'talashnet-external-request-blocker'); ?>
                                </p>
                                <div class="im-guide-steps">
                                    <div class="im-step">
                                        <span class="im-step-number">1</span>
                                        <span>
                                            <?php

$p1 = esc_html__('در صورت فعال بودن', 'talashnet-external-request-blocker');
$p2 = esc_html__('درگاه بانکی یا سرویس پیامکی یا سایتهایی که api ایرانی آنها مهم است', 'talashnet-external-request-blocker');
$p3 = esc_html__('فرآیند آن را یک‌بار به‌صورت کامل بررسی کنید. پس از شناسایی دامنه های مرتبط، وضعیت آن را روی Open قرار دهید.', 'talashnet-external-request-blocker');


printf(
    '%1$s <strong style="color:#a710a7">%2$s</strong> %3$s',
    $p1,
    $p2,
    $p3
);
?>

                                        </span>

                                    </div>
                                    <div class="im-step">
                                        <span class="im-step-number">2</span>
                                        <span><?php echo esc_html__('جهت فعال‌سازی ریکوئستر می بایست گواهی SSL سایت فعال باشد.', 'talashnet-external-request-blocker'); ?></span>
                                    </div>
                                    <div class="im-step">
                                        <span class="im-step-number">3</span>
                                        <span>
                                                    <?php
        printf(
            esc_html__('پس از اعمال هرگونه تغییر در بخش‌های افزونه، حتماً بر روی گزینه "%s" کلیک کنید.', 'talashnet-external-request-blocker'),
            esc_html__('ذخیره تنظیمات', 'talashnet-external-request-blocker')
        );
        ?>
                                        </span>
                                    </div>
                                    <div class="im-step">
                                        <span class="im-step-number">4</span>
                                        <span><?php echo esc_html__('این افزونه کاملا اپن-سورس است و هیچگونه تغییری در هسته وردپرس ایجاد نمی‌کند', 'talashnet-external-request-blocker'); ?></span>
                                    </div>
                                    <div class="im-step">
                                        <span class="im-step-number">5</span>
                                        <span><?php echo esc_html__('پس از انجام تنظیمات اولیه، ریست کردن افزونه کش در بعضی سایت ها کمک کننده است', 'talashnet-external-request-blocker'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
