<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
        <div class="internet-melli-admin-wrapper">
            <!-- Header Section -->
            <?php include __DIR__ . '/header.php'; ?>

            <!-- Tabs Container -->
            <?php include __DIR__ . '/tabs.php'; ?>


            <?php if ($active_tab == 'settings') : ?>
                <?php include __DIR__ . '/settings/tab-settings.php'; ?>
            <?php endif; ?>

            <?php if ($active_tab == 'active-plugins') : ?>
                <?php include __DIR__ . '/active-plugins/tab-active.php'; ?>
            <?php endif; ?>
