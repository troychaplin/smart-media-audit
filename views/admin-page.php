<?php
use WP_Media_Audit\Admin\List_Table;
use WP_Media_Audit\Scanner\Batch_Runner;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$progress = Batch_Runner::get_progress();
$is_scanning = 'scanning' === $progress['status'];

$table = new List_Table();
$table->prepare_items();
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Media Audit', 'wp-media-audit' ); ?></h1>

	<div class="media-audit-toolbar">
		<div class="media-audit-scan-status">
			<?php if ( 'complete' === $progress['status'] || 'idle' === $progress['status'] ) : ?>
				<?php if ( 'complete' === $progress['status'] ) : ?>
					<span class="media-audit-last-scanned">
						<?php esc_html_e( 'Index is up to date.', 'wp-media-audit' ); ?>
					</span>
				<?php else : ?>
					<span class="media-audit-last-scanned">
						<?php esc_html_e( 'Index has not been built yet.', 'wp-media-audit' ); ?>
					</span>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<button id="media-audit-scan-btn" class="button button-primary">
			<?php esc_html_e( 'Scan Now', 'wp-media-audit' ); ?>
		</button>
	</div>

	<div id="media-audit-progress-wrap" <?php echo $is_scanning ? '' : 'hidden'; ?>>
		<div id="media-audit-progress-bar-track">
			<div id="media-audit-progress-bar"
				style="width: <?php echo $is_scanning && $progress['total'] ? esc_attr( round( $progress['progress'] / $progress['total'] * 100 ) ) . '%' : '0%'; ?>">
			</div>
		</div>
		<span id="media-audit-progress-label">
			<?php
			if ( $is_scanning ) {
				printf(
					/* translators: 1: processed count, 2: total count */
					esc_html__( 'Scanning… %1$d / %2$d posts', 'wp-media-audit' ),
					(int) $progress['progress'],
					(int) $progress['total']
				);
			}
			?>
		</span>
	</div>

	<form method="get">
		<input type="hidden" name="page" value="wp-media-audit">
		<input type="hidden" name="filter" value="<?php echo esc_attr( sanitize_key( $_GET['filter'] ?? 'all' ) ); ?>">
		<?php
		$table->views();
		$table->search_box( __( 'Search media', 'wp-media-audit' ), 'media-search' );
		$table->display();
		?>
	</form>
</div>
