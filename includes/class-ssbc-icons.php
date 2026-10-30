<?php
/**
 * Icon definitions for ClickReach – Social Share Buttons & Counter
 *
 * @package ClickReach_Social_Share_Buttons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SSBC_Icons
 * Provides lightweight, crisp, embedded inline SVGs for supported social networks.
 */
class SSBC_Icons {

	/**
	 * Get list of all supported networks with meta information.
	 *
	 * @return array
	 */
	public static function get_supported_networks() {
		return array(
			'x'        => array(
				'name'       => __( 'X (Twitter)', 'clickreach-social-share-buttons' ),
				'color'      => '#000000',
				'text_color' => '#ffffff',
			),
			'facebook' => array(
				'name'       => __( 'Facebook', 'clickreach-social-share-buttons' ),
				'color'      => '#1877f2',
				'text_color' => '#ffffff',
			),
			'linkedin' => array(
				'name'       => __( 'LinkedIn', 'clickreach-social-share-buttons' ),
				'color'      => '#0a66c2',
				'text_color' => '#ffffff',
			),
			'whatsapp' => array(
				'name'       => __( 'WhatsApp', 'clickreach-social-share-buttons' ),
				'color'      => '#25d366',
				'text_color' => '#ffffff',
			),
			'pinterest' => array(
				'name'       => __( 'Pinterest', 'clickreach-social-share-buttons' ),
				'color'      => '#e60023',
				'text_color' => '#ffffff',
			),
			'reddit'   => array(
				'name'       => __( 'Reddit', 'clickreach-social-share-buttons' ),
				'color'      => '#ff4500',
				'text_color' => '#ffffff',
			),
			'copy'     => array(
				'name'       => __( 'Copy Link', 'clickreach-social-share-buttons' ),
				'color'      => '#4a5568',
				'text_color' => '#ffffff',
			),
		);
	}

	/**
	 * Render raw inline SVG icon by network key.
	 *
	 * @param string $network Network slug.
	 * @return string Safe inline SVG string.
	 */
	public static function get_svg( $network ) {
		$svgs = array(
			'x' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true" focusable="false"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',

			'facebook' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true" focusable="false"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',

			'linkedin' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true" focusable="false"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>',

			'whatsapp' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true" focusable="false"><path d="M17.472 14.382c-.301-.15-1.78-.878-2.056-.978-.276-.101-.477-.15-.678.15-.2.301-.778.978-.954 1.18-.175.201-.351.226-.652.075s-1.27-.468-2.42-1.493c-.894-.798-1.497-1.783-1.673-2.084-.175-.301-.019-.464.132-.614.136-.135.301-.351.451-.527.151-.176.201-.301.301-.502.101-.201.05-.377-.025-.527s-.678-1.633-.929-2.236c-.244-.588-.493-.509-.678-.518l-.578-.01c-.201 0-.527.075-.803.377s-1.054 1.03-1.054 2.511 1.079 2.912 1.23 3.113c.151.201 2.124 3.243 5.145 4.549.719.311 1.28.497 1.718.636.722.23 1.379.197 1.899.12.579-.087 1.78-.728 2.031-1.431.251-.703.251-1.305.176-1.431-.076-.126-.277-.201-.578-.352zm-5.462 7.618A9.94 9.94 0 0 1 7.02 20.73l-.358-.212-3.714.974.991-3.62-.233-.371a9.947 9.947 0 0 1-1.527-5.31c0-5.514 4.486-10 10-10s10 4.486 10 10c0 5.515-4.486 10-10 10zM12 0C5.373 0 0 5.373 0 12c0 2.115.552 4.103 1.517 5.836L0 24l6.335-1.662A11.94 11.94 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>',

			'pinterest' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345-.09.375-.291 1.199-.334 1.357-.057.24-.19.291-.439.176-1.644-.765-2.672-3.166-2.672-5.097 0-4.149 3.015-7.96 8.694-7.96 4.565 0 8.114 3.253 8.114 7.602 0 4.535-2.859 8.185-6.828 8.185-1.333 0-2.586-.693-3.014-1.509l-.821 3.129c-.298 1.144-1.103 2.578-1.643 3.449A11.968 11.968 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>',

			'reddit' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.968 0 1.754.786 1.754 1.754 0 .716-.435 1.333-1.01 1.614a3.111 3.111 0 0 1 .042.52c0 2.694-3.13 4.87-7.004 4.87-3.874 0-7.004-2.176-7.004-4.87 0-.183.015-.366.043-.534A1.748 1.748 0 0 1 4.028 12c0-.968.786-1.754 1.754-1.754.463 0 .898.196 1.207.49 1.207-.883 2.878-1.43 4.744-1.487l.885-4.182a.342.342 0 0 1 .14-.197.35.35 0 0 1 .238-.042l2.906.617a1.214 1.214 0 0 1 1.108-.703zM9.25 12C8.561 12 8 12.562 8 13.25c0 .687.561 1.248 1.25 1.248.687 0 1.248-.561 1.248-1.249 0-.688-.561-1.249-1.249-1.249zm5.5 0c-.687 0-1.248.561-1.248 1.25 0 .687.561 1.248 1.249 1.248.688 0 1.249-.561 1.249-1.249 0-.687-.562-1.249-1.25-1.249zm-5.466 3.99a.327.327 0 0 0-.231.095.326.326 0 0 0 0 .463c.844.845 2.193 1.056 2.947 1.056.755 0 2.103-.211 2.947-1.056a.326.326 0 0 0 0-.463.327.327 0 0 0-.463 0c-.636.636-1.752.83-2.484.83-.732 0-1.848-.194-2.484-.83a.328.328 0 0 0-.232-.095z"/></svg>',

			'copy' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true" focusable="false"><path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/></svg>',
		);

		return isset( $svgs[ $network ] ) ? $svgs[ $network ] : '';
	}

	/**
	 * Build sharing URL for a given platform.
	 *
	 * @param string $network Platform key.
	 * @param string $url     Canonical URL to share.
	 * @param string $title   Post title to share.
	 * @return string Share link URL.
	 */
	public static function get_share_url( $network, $url, $title = '' ) {
		$encoded_url   = rawurlencode( $url );
		$encoded_title = rawurlencode( html_entity_decode( $title, ENT_QUOTES, 'UTF-8' ) );

		switch ( $network ) {
			case 'x':
				return 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title;

			case 'facebook':
				return 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url . '&quote=' . $encoded_title;

			case 'linkedin':
				return 'https://www.linkedin.com/feed/?shareActive=true&text=' . $encoded_title . '%20' . $encoded_url;

			case 'whatsapp':
				return 'https://api.whatsapp.com/send?text=' . $encoded_title . '%20' . $encoded_url;

			case 'pinterest':
				return 'https://pinterest.com/pin/create/button/?url=' . $encoded_url . '&description=' . $encoded_title;

			case 'reddit':
				return 'https://reddit.com/submit?url=' . $encoded_url . '&title=' . $encoded_title;

			case 'copy':
				return '#';

			default:
				return '';
		}
	}
}
