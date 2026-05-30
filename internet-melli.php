<?php
/**
 * Plugin Name:       مسدودکننده سایت‌های خارجی (Internet Melli)
 * Plugin URI:        https://talashnet.com
 * Description:       مسدود کردن درخواست‌های سایت‌های خارجی که در اینترنت ملی کندی ایجاد می‌کنند با استفاده از ریکوئستور
 * Version:           1.2.1
 * Author:            تلاش نت
 * Author URI:        https://talashnet.com
 * License:           GPL-2.0+
 * Text Domain:       internet-melli
 */

if (!defined('ABSPATH')) {
    exit;
}

define('INTERNET_MELLI_VERSION', $plugin_data['Version']);
define('INTERNET_MELLI_PATH', plugin_dir_path(__FILE__));
define('INTERNET_MELLI_URL', plugin_dir_url(__FILE__));

require_once INTERNET_MELLI_PATH . 'includes/class-internet-melli-admin.php';
require_once INTERNET_MELLI_PATH . 'includes/class-internet-melli-updater.php';
require_once INTERNET_MELLI_PATH . 'includes/class-internet-melli-blocker.php';


class Internet_Melli {
    private static $instance = null;
    private $admin;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->admin = new Internet_Melli_Admin('internet-melli', INTERNET_MELLI_VERSION);

        add_action('init', [$this, 'init']);
        add_action('admin_menu', [$this->admin, 'add_menu_page']);
        add_action('admin_init', [$this->admin, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this->admin, 'enqueue_styles']);
        add_action('admin_enqueue_scripts', [$this->admin, 'enqueue_scripts']);

        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_scripts']);
        add_action('wp_head', array($this, 'add_sw_to_head'), 1);


        // سرو فایل SW
        add_action('init', [$this, 'serve_sw_js']);
        add_action('template_redirect', [$this, 'serve_sw_js']); // برای اطمینان بیشتر

        // AJAX
        add_action('wp_ajax_internet_melli_toggle', [$this->admin, 'handle_toggle']);
        add_action('wp_ajax_internet_melli_test', [$this->admin, 'handle_test']);
        add_action('wp_ajax_check_plugin_update', 'internet_melli_check_update');
        add_action('wp_ajax_install_plugin_update', 'internet_melli_install_update');
        add_action('wp_ajax_internet_melli_delete_all', 'ajax_delete_all_data');

        // پاکسازی هنگام غیرفعال‌سازی افزونه
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        if ( class_exists( 'Internet_Melli_Blocker' ) ) {
            Internet_Melli_Blocker::init();
        }
    }

    public function init() {
        load_plugin_textdomain('internet-melli', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    public function enqueue_frontend_scripts() {

    }
    
    public function add_sw_to_head() {
        $enabled = (int) get_option('internet_melli_enabled', 0);
        
    if (!$enabled) {
        ?>
        <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                for (let registration of registrations) {
                    console.log('Unregistering Internet Melli SW:', registration.scope);
                    registration.unregister();
                }
            });
        }
        </script>
        <?php
        return;
    }
        
        $version = INTERNET_MELLI_VERSION;
        $sw_url = INTERNET_MELLI_URL . 'sw.js?ver=' . $version;
        
        // برای تست روی localhost - در production حذف شود
        $sw_url = home_url('sw.js?ver=' . $version); 
        // . '&nonce=' . wp_create_nonce('internet_melli_sw')
        //$sw_url = "https://exirmatab.com/?sw=internet-melli";
        
        ?>
        
        
    <script>
    console.log('SW URL: <?php echo $sw_url; ?>');
    
    if ('serviceWorker' in navigator) {
        // بررسی می‌کنیم آیا صفحه تحت کنترل SW هست یا نه
        var isControlled = navigator.serviceWorker.controller !== null;
        
        console.log('Page controlled by SW:', isControlled);
        
        navigator.serviceWorker.register('<?php echo $sw_url; ?>', { scope: '/' })
            .then(function(registration) {
                console.log('Internet Melli SW registered:', registration.scope);
                return navigator.serviceWorker.ready;
            })
            .then(function(registration) {
                console.log('Internet Melli SW is now ready and active!');
                
                // اگر صفحه تحت کنترل SW نیست (اولین بار)، ریلود می‌کنیم
                if (!navigator.serviceWorker.controller || !isControlled) {
                    console.log('Reloading page for SW to take control...');
                    window.location.reload();
                } else {
                    console.log('Page is already controlled by SW');
                }
            })
            .catch(function(err) {
                console.log('Internet Melli SW registration failed:', err);
            });
    }
    </script>
    
    
        <?php
    }

public function serve_sw_js() {
    // چک کردن درخواست requester
    if (isset($_GET['sw']) && $_GET['sw'] === 'internet-melli') {
        $blocked_domains = get_option('internet_melli_blocked_domains_frontend', 'gravatar.com,googleapis.com,fonts.googleapis.com,google,cdnjs,cloudflare,microsoft,clarity,fontawesome.com,ps.w.org');
        $domains_array = array_map('trim', explode(',', $blocked_domains));
        $domains_json = json_encode($domains_array);
        $version = INTERNET_MELLI_VERSION;
        
        // Headers مناسب
        header('Content-Type: application/javascript; charset=utf-8');
        //header('Cache-Control: no-cache, no-store, must-revalidate');
        //header('Pragma: no-cache');
        //header('Expires: 0');
        
        echo "
const blocked = {$domains_json};
const SW_VERSION = '{$version}';

self.addEventListener('install', (event) => {
    self.skipWaiting();
    console.log('Internet Melli SW v' + SW_VERSION + ' installed');
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        clients.claim().then(() => {
            console.log('Internet Melli SW v' + SW_VERSION + ' activated');
            return self.registration.update();
        })
    );
});

self.addEventListener('fetch', function(event) {
    const url = event.request.url;
    //console.log('SW Intercepted:', url);
    
    for (let b of blocked) {        
        if (url.includes(b)) {            
            console.log('❌ Blocked:', b);            
            event.respondWith(new Response('', {status: 403}));            
            return;        
        }    
    }
});

";
        
        exit;
    }
    
    // Rewrite rule برای sw.js
    add_rewrite_rule('^sw\.js$', '?sw=internet-melli', 'top');
}

    public function deactivate() {
        // unregister همه SWها هنگام غیرفعال‌سازی افزونه
        // این کار در سمت کلاینت انجام می‌شود، اما می‌توانیم یک صفحه خالی یا پیام بگذاریم
        flush_rewrite_rules();
    }
}

// ==========================================
// AJAX Handlers for Plugin Update
// ==========================================

function internet_melli_check_update() {
    // بررسی nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'internet_melli_nonce')) {
        wp_send_json(array(
            'status'  => 'error',
            'message' => __('خطای امنیتی', 'internet-melli')
        ));
    }
    
    // بررسی دسترسی
    if (!current_user_can('manage_options')) {
        wp_send_json(array(
            'status'  => 'error',
            'message' => __('دسترسی غیرمجاز', 'internet-melli')
        ));
    }

    // خواندن نسخه فعلی از فایل اصلی
    $plugin_data = get_plugin_data(INTERNET_MELLI_PATH . 'internet-melli.php');
    $current_version = $plugin_data['Version'];

    // ایجاد نمونه از کلاس آپدیت
    $updater = new Internet_Melli_Updater($current_version);
    $result = $updater->check_for_update();
    
    wp_send_json($result);
}

function internet_melli_install_update() {
    // بررسی nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'internet_melli_nonce')) {
        wp_send_json(array(
            'status'  => 'error',
            'message' => __('خطای امنیتی', 'internet-melli')
        ));
    }
    
    // بررسی دسترسی
    if (!current_user_can('install_plugins')) {
        wp_send_json(array(
            'status'  => 'error',
            'message' => __('دسترسی غیرمجاز', 'internet-melli')
        ));
    }

    $download_url = isset($_POST['download_url']) ? esc_url_raw($_POST['download_url']) : '';
    
    if (empty($download_url)) {
        wp_send_json(array(
            'status'  => 'error',
            'message' => __('لینک دانلود یافت نشد', 'internet-melli')
        ));
    }

    // خواندن نسخه فعلی
    $plugin_data = get_plugin_data(INTERNET_MELLI_PATH . 'internet-melli.php');
    $current_version = $plugin_data['Version'];

    $updater = new Internet_Melli_Updater($current_version);
    $result = $updater->download_and_install($download_url);
    
    wp_send_json($result);
}


function ajax_delete_all_data() {

    if (!current_user_can('manage_options')) {
        wp_send_json_error([
            'message' => __('دسترسی غیرمجاز.', 'internet-melli')
        ]);
    }

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'internet_melli_nonce')) {
        wp_send_json_error([
            'message' => __('توکن امنیتی نامعتبر است.', 'internet-melli')
        ]);
    }

    // گزینه‌هایی که باید حذف شوند:
    delete_option('internet_melli_enabled');
    delete_option('internet_melli_blocked_domains_frontend');
    delete_option('internet_melli_blocked_domains_backend');
    delete_option('internet_melli_version');
    delete_option('internet_melli_sw_cache_version');

    // اگر قبلاً گزینه قدیمی وجود داشته است:
    delete_option('internet_melli_blocked_domains');

    wp_send_json_success([
        'message' => __('تمام اطلاعات پلاگین با موفقیت حذف شد.', 'internet-melli')
    ]);
}


Internet_Melli::get_instance();

register_activation_hook(__FILE__, function() {
    if (false === get_option('internet_melli_enabled')) {
        update_option('internet_melli_enabled', 1);
    }
    
    add_rewrite_rule('^sw\.js$', '?sw=internet-melli', 'top');
    flush_rewrite_rules();
});

