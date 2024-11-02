<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
20201123 - Standalone framework plugin
20201217 - Release standalone
20210107 - Update CSS
*/

$billingotomatis_plugin_list = [
	'Billingotomatis - Payment Gateway Indonesia' => [
		"description" => "Module Payment Gateway memungkinkan Anda untuk mengambil mutasi Bank secara otomatis. Bank yang disupport saat ini; BCA, Mandiri, BRI, BNI.",
		"url" =>" admin.php?page=bilo-payment-gateway",
		"download" => "https://www.domosquare.com/manage/dl.php?type=d&id=57",
		"info" => "https://www.domosquare.com/tutorial/billingotomatis/install-dan-konfigurasi-plugin-cek-mutasi-bank-otomatis-di-wordpress.html"
	],
	'Billingotomatis - SMS Gateway' => [
		"description" => "SMS Gateway untuk Wordpress. Dengan module ini Anda dapat mengirim SMS dengan mudah dan mengintegrasikan dengan wordpress/plugin Anda.",
		"url" => "admin.php?page=bilosms-main",
		"download" => "https://www.domosquare.com/manage/dl.php?type=d&id=61",
		"info" => "https://www.domosquare.com/tutorial/billingotomatis/sms-gateway-untuk-wordpress.html"
	],
	'Billingotomatis - Whatsapp Gateway' => [
		"description" => "Whatsapp Gateway untuk Wordpress. Dengan module ini Anda dapat mengirim WA dengan mudah dan mengintegrasikan dengan wordpress/plugin Anda.",
		"url" => "admin.php?page=bilowa-main",
		"download" => "https://www.domosquare.com/manage/dl.php?type=d&id=53",
		"info" => "https://www.domosquare.com/tutorial/billingotomatis/cara-order-dan-penggunaan-wa-gateway-billingotomatis.html"
	],
	'Billingotomatis - Woocommerce, payment gateway' => [
		"description" => "Payment Gateway untuk WooComerce, memungkinkan Anda mengotomatisasikan order WooCommerce Anda.",
		"url" => "admin.php?page=woo-bilo-gateway",
		"download" => "https://www.domosquare.com/manage/dl.php?type=d&id=58",
		"info" => "https://www.domosquare.com/tutorial/billingotomatis/pembayaran-otomatis-bank-indonesia-di-woocommerce-dengan-billingotomatis.html"
	],
	'Billingotomatis - Woocommerce, SMS gateway' => [
		"description" => "SMS Gateway untuk WooComerce, memungkinkan Anda ataupun WooComerce mengirim notifikasi SMS ke pelanggan secara otomatis ataupun manual.",
		"url" => "admin.php?page=woo-bilo-sms",
		"download" => "https://www.domosquare.com/manage/dl.php?type=d&id=59",
		"info" => "https://www.domosquare.com/tutorial/billingotomatis/integrasi-sms-gateway-dengan-toko-online-wordpress-woocommerce.html"
	],
	'Billingotomatis - Woocommerce, Whatsapp gateway' => [
		"description" => "Whatsapp Gateway untuk WooComerce, memungkinkan Anda ataupun WooComerce mengirim notifikasi WA ke pelanggan secara otomatis ataupun manual.",
		"url" => "admin.php?page=woo-bilo-wa",
		"download" => "https://www.domosquare.com/manage/dl.php?type=d&id=60",
		"info" => "https://www.domosquare.com/tutorial/billingotomatis/whatsapp-gateway-untuk-woocommerce-dan-cara-menggunakannya.html"
	]
];

function bilo_update_upgrade() {
	global $wpdb;
	$old_version = intval(get_option('bilo_version'));
    if ( $old_version <  20160316) {
    }
    update_option('bilo_version',BILO_VERSION);
}
add_action( 'plugins_loaded', 'bilo_update_upgrade' );

function bilo_admin_menu(){
  $plugin_page = add_menu_page( 'Billingotomatis.Com', 'Billingotomatis', 'manage_options', 'billingotomatis', 'bilo_manage_module', plugins_url( 'assets/img/icon.png',__FILE__ ),58 );
  add_action( 'admin_head-'. $plugin_page, 'bilo_load_css_js' );
  $plugin_page = add_submenu_page( 'billingotomatis', 'Kelola Module', 'Module', 'manage_options', 'billingotomatis', 'bilo_manage_module');
  add_action( 'admin_head-'. $plugin_page, 'bilo_load_css_js' );
  do_action('bilo_admin_menu');
}
add_action( 'admin_menu', 'bilo_admin_menu' );

function bilo_settings_link($links) {
  $settings_link = '<a href="admin.php?page=billingotomatis">Konfigurasi</a>';
  array_unshift($links, $settings_link);
  return $links;
}

$plugin = plugin_basename(dirname(__FILE__)."/billingotomatis.php");
add_filter("plugin_action_links_$plugin", 'bilo_settings_link' );

function bilo_compare_array($a, $b) { return $a['position'] - $b['position']; }
function bilo_manage_module() {
	global $billingotomatis_plugin_list;

	$plugins = get_plugins();
	$installed_bilo_plugins = [];
	$installed_bilo_plugins_path = [];
	foreach($plugins as $key => $val) {
		if(strtolower($val['Author']) == 'billingotomatis.com') {
			$installed_bilo_plugins[$val['Name']] = $val;
			$installed_bilo_plugins[$val['Name']]['path'] = $key;
			$installed_bilo_plugins_path[] = $key;
		}
	}

	if(isset($_REQUEST['aktifkan']) and in_array($_REQUEST['aktifkan'],$installed_bilo_plugins_path)) {
		$ret = activate_plugin($_REQUEST['aktifkan']);
		if($ret == NULL) {
			?>
			<div id="message" class="updated">
			  <p>Plugin berhasil diaktifkan.</p>
			</div>
			<?php
		} else {
			?>
			<div id="message" class="error">
			  <p>Plugin gagal diaktifkan diaktifkan.</p>
			</div>
			<?php
		}
	}

	$active_plugins = (array) get_option( 'active_plugins', array() );

?>

<div class="wrap bilo_addons_wrap">
  <h2> Kelola Module Billingotomatis.com <a class="add-new-h2" href="https://billingotomatis.com/wordpress">Lihat Semua Modul</a></h2>
	<?php
    #$item = (isset($item))?$item:array();
  	#$items = apply_filters('bilo_module_list',$item);
	if($billingotomatis_plugin_list): ?>
  <ul class="products">
  <?php

	foreach($billingotomatis_plugin_list as $key => $item):

		if(isset($installed_bilo_plugins[$key])) {
			if(in_array($installed_bilo_plugins[$key]['path'],$active_plugins)) {
				$button = '<a href="'.$item['url'].'" class="button button-primary">Kelola</a>';
			} else {
				$button = '<a href="admin.php?page=billingotomatis&aktifkan='.$installed_bilo_plugins[$key]['path'].'" class="button button-primary">Aktifkan</a>';
			}
		} else {
			$button = '<a href="'.$item['download'].'" class="button button-primary">Download</a>';
		}

		$key = str_replace('Billingotomatis - ','',$key);
		$list[] = '
	  <li class="product"><div class="desc">
      <h3>'.$key.'</h3>
      <p>'.$item['description'].'
			<br />
			<br />
			'.$button.'
			<a href="'.$item['info'].'" class="button button-success">Info</a>
			</p>
      </div>
			</li>';
	endforeach;
	#ksort($list);
	echo implode(" ",$list);
  ?>

  </ul>
	<div  class="notice">
		<h3>Petunjuk</h3>
		<p>Silahkan klik <strong>Download</strong> untuk mendownload plugin, lalu <a href="plugin-install.php" target="_blank">upload pada halaman penambahan plugin</a>, setelah terupload Anda bisa mengaktifkan plugin billingotomatis yang sudah didownload.
		<br /><br />
			Untuk mengetahui informasi penggunaan, klik tombol <strong>Info</strong>.
		</p>
  <p>
    Jika Anda belum menemukan module yang Anda cari di sini, silahkan kontak support atau cek email aktifasi produk billingotomatis Anda.
  </p>
  <?php else: ?>
  <p>Sepertinya belum ada module billingotomatis yang terinstall pada wordpress Anda, silahkan lakukan pencarian module billingotomatis dari plugin direktori wordpress, atau klik link "Lihat Semua Modul" di atas.
  <?php
	endif;
  ?>
</div>
<?php
}

function bilo_enqueue_script(){

  wp_enqueue_script( 'jquery' );
  wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-datepicker' );

}
add_action( 'admin_enqueue_scripts', 'bilo_enqueue_script' );

function bilo_load_css_js() {
  wp_register_style( 'font_awesome', plugins_url('assets/css/font-awesome.min.css', __FILE__), false, BILO_VERSION );
  wp_enqueue_style( 'font_awesome' );
  wp_register_style( 'style', plugins_url('assets/css/style.css', __FILE__), false, BILO_VERSION );
  wp_enqueue_style( 'style' );
  wp_register_style( 'jquery_custom_ui', plugins_url('assets/css/jquery-ui.css', __FILE__), false, BILO_VERSION );
  wp_enqueue_style( 'jquery_custom_ui' );
  wp_enqueue_script( 'script_js', plugins_url('assets/js/script.js', __FILE__), array(), BILO_VERSION );
}
