<?php
/**
 * Post editor metabox for ClickReach – Social Share Buttons & Counter
 *
 * @package ClickReach_Social_Share_Buttons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SSBC_Metabox
 */
class SSBC_Metabox {

	/**
	 * Hook into WordPress.
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
		add_action( 'save_post', array( __CLASS__, 'save_meta_box' ) );
	}

	/**
	 * Add meta box to supported post types.
	 */
	public static function add_meta_box() {
		$post_types = SSBC_Settings::get_option( 'post_types', array( 'post' ) );

		if ( ! empty( $post_types ) && is_array( $post_types ) ) {
			foreach ( $post_types as $type ) {
				add_meta_box(
					'ssbc_share_meta_box',
					__( 'Social Share & Counter', 'clickreach-social-share-buttons' ),
					array( __CLASS__, 'render_meta_box' ),
					$type,
					'side',
					'default'
				);
			}
		}
	}

	/**
	 * Render post metabox.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public static function render_meta_box( $post ) {
		wp_nonce_field( 'ssbc_save_meta_box', 'ssbc_meta_box_nonce' );

		$selected_status = get_post_meta( $post->ID, '_ssbc_status', true );
		if ( empty( $selected_status ) ) {
			$selected_status = 'default';
		}

		$all_networks = SSBC_Icons::get_supported_networks();
		?>
		<div class="ssbc-metabox-wrapper">
			<p><strong><?php esc_html_e( 'Display Option:', 'clickreach-social-share-buttons' ); ?></strong></p>
			<label style="display:block; margin-bottom: 5px;">
				<input type="radio" name="ssbc_status" value="default" <?php checked( $selected_status, 'default' ); ?>>
				<?php esc_html_e( 'Default (Global Settings)', 'clickreach-social-share-buttons' ); ?>
			</label>
			<label style="display:block; margin-bottom: 5px;">
				<input type="radio" name="ssbc_status" value="enable" <?php checked( $selected_status, 'enable' ); ?>>
				<?php esc_html_e( 'Force Enable on this post', 'clickreach-social-share-buttons' ); ?>
			</label>
			<label style="display:block; margin-bottom: 12px;">
				<input type="radio" name="ssbc_status" value="disable" <?php checked( $selected_status, 'disable' ); ?>>
				<?php esc_html_e( 'Disable on this post', 'clickreach-social-share-buttons' ); ?>
			</label>

			<hr style="margin: 12px 0;">

			<p><strong><?php esc_html_e( 'Share Statistics:', 'clickreach-social-share-buttons' ); ?></strong></p>
			<table class="widefat fixed striped" style="margin-bottom: 10px;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Platform', 'clickreach-social-share-buttons' ); ?></th>
						<th style="width: 60px; text-align: right;"><?php esc_html_e( 'Clicks', 'clickreach-social-share-buttons' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$total_clicks = 0;
					foreach ( $all_networks as $key => $meta ) {
						$count = absint( get_post_meta( $post->ID, '_ssbc_clicks_' . $key, true ) );
						$total_clicks += $count;
						?>
						<tr>
							<td><?php echo esc_html( $meta['name'] ); ?></td>
							<td style="text-align: right;"><strong><?php echo esc_html( number_format_i18n( $count ) ); ?></strong></td>
						</tr>
						<?php
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th><strong><?php esc_html_e( 'Total Shares', 'clickreach-social-share-buttons' ); ?></strong></th>
						<th style="text-align: right;"><strong><?php echo esc_html( number_format_i18n( $total_clicks ) ); ?></strong></th>
					</tr>
				</tfoot>
			</table>
			<p class="description" style="font-size: 11px;">
				<?php esc_html_e( 'Share counts update automatically whenever visitors share or click your links.', 'clickreach-social-share-buttons' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Save metabox data securely.
	 *
	 * @param int $post_id Post ID.
	 */
	public static function save_meta_box( $post_id ) {
		// Nonce check
		if ( ! isset( $_POST['ssbc_meta_box_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ssbc_meta_box_nonce'] ), 'ssbc_save_meta_box' ) ) {
			return;
		}

		// Autosave check
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Revision check
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Capability check
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save display status
		if ( isset( $_POST['ssbc_status'] ) ) {
			$status = sanitize_key( $_POST['ssbc_status'] );
			if ( in_array( $status, array( 'default', 'enable', 'disable' ), true ) ) {
				update_post_meta( $post_id, '_ssbc_status', $status );
			}
		}

		// Note: Counters are NEVER incremented here during post edit/save!
	}
}
