<?php
namespace WP_Media_Audit\Admin;

class Admin_Menu {

	public static function register(): void {
		add_action( 'admin_menu',            array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
	}

	public static function add_menu(): void {
		add_submenu_page(
			'upload.php',
			__( 'Media Audit', 'wp-media-audit' ),
			__( 'Media Audit', 'wp-media-audit' ),
			'manage_options',
			'wp-media-audit',
			array( __CLASS__, 'render_page' )
		);
	}

	public static function enqueue_assets( string $hook ): void {
		if ( 'media_page_wp-media-audit' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'wp-media-audit-admin',
			plugin_dir_url( WP_MEDIA_AUDIT_FILE ) . 'assets/css/admin.css',
			array(),
			WP_MEDIA_AUDIT_VERSION
		);

		wp_enqueue_script(
			'wp-media-audit-progress',
			plugin_dir_url( WP_MEDIA_AUDIT_FILE ) . 'assets/js/progress.js',
			array(),
			WP_MEDIA_AUDIT_VERSION,
			true
		);

		wp_localize_script( 'wp-media-audit-progress', 'wpMediaAudit', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'media_audit_nonce' ),
		) );
	}

	public static function render_page(): void {
		require_once WP_MEDIA_AUDIT_DIR . 'views/admin-page.php';
	}
}
