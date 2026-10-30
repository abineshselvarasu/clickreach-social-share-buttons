/**
 * Frontend JavaScript for Social Share Buttons & Counter
 * Pure Vanilla JS — zero jQuery dependency for maximum performance.
 */
document.addEventListener('DOMContentLoaded', function () {
	// Clicks already tracked during this page view (in memory only, no storage).
	var trackedClicks = {};

	// Event delegation for all share buttons
	document.body.addEventListener('click', function (event) {
		var button = event.target.closest('.ssbc-btn');
		if (!button) {
			return;
		}

		var platform = button.getAttribute('data-platform');
		var postId   = button.getAttribute('data-post-id');
		var shareUrl = button.getAttribute('data-share-url') || window.location.href;

		if (!platform || !postId) {
			return;
		}

		// Handle "Copy Link" action
		if (platform === 'copy') {
			event.preventDefault();
			copyToClipboard(shareUrl);
			showToast((typeof ssbcData !== 'undefined' && ssbcData.i18n && ssbcData.i18n.copied) ? ssbcData.i18n.copied : 'Link Copied!');
		}

		// Send tracking AJAX request
		trackShareClick(postId, platform, button);
	});

	/**
	 * Send AJAX request to increment share count.
	 *
	 * @param {string|number} postId
	 * @param {string} platform
	 * @param {HTMLElement} button
	 */
	function trackShareClick(postId, platform, button) {
		if (typeof ssbcData === 'undefined' || !ssbcData.ajaxUrl) {
			return;
		}

		// Count each network only once per post per page view
		var clickKey = postId + ':' + platform;
		if (trackedClicks[clickKey]) {
			return;
		}
		trackedClicks[clickKey] = true;

		var formData = new FormData();
		formData.append('action', 'ssbc_track_click');
		formData.append('nonce', ssbcData.nonce);
		formData.append('post_id', postId);
		formData.append('platform', platform);

		fetch(ssbcData.ajaxUrl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin'
		})
			.then(function (response) {
				return response.json();
			})
			.then(function (data) {
				if (data && data.success && data.data) {
					// Update all matching button count badges for this post on the page
					var countEls = document.querySelectorAll('.ssbc-wrapper[data-post-id="' + postId + '"] .ssbc-btn[data-platform="' + platform + '"] .ssbc-count');
					countEls.forEach(function (el) {
						if (data.data.count_display) {
							el.textContent = data.data.count_display;
						}
					});

					// Update all total share counter badges for this post on the page
					var totalEls = document.querySelectorAll('.ssbc-total-count[data-post-id="' + postId + '"]');
					totalEls.forEach(function (el) {
						if (data.data.total_display) {
							el.textContent = data.data.total_display;
						}
					});
				}
			})
			.catch(function (error) {
				// Silently fail so sharing action is never blocked
			});
	}

	/**
	 * Copy text to clipboard with modern API and fallback.
	 *
	 * @param {string} text
	 */
	function copyToClipboard(text) {
		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(text);
		} else {
			var textArea = document.createElement('textarea');
			textArea.value = text;
			textArea.style.position = 'fixed';
			textArea.style.left = '-999999px';
			textArea.style.top = '-999999px';
			document.body.appendChild(textArea);
			textArea.focus();
			textArea.select();
			try {
				document.execCommand('copy');
			} catch (err) {
				// Fallback failed
			}
			document.body.removeChild(textArea);
		}
	}

	/**
	 * Display visual toast notification.
	 *
	 * @param {string} message
	 */
	function showToast(message) {
		var existing = document.querySelector('.ssbc-toast');
		if (existing) {
			existing.remove();
		}

		var toast = document.createElement('div');
		toast.className = 'ssbc-toast';
		toast.textContent = message;
		document.body.appendChild(toast);

		// Trigger entrance animation
		requestAnimationFrame(function () {
			toast.classList.add('ssbc-toast-visible');
		});

		setTimeout(function () {
			toast.classList.remove('ssbc-toast-visible');
			setTimeout(function () {
				if (toast.parentNode) {
					toast.parentNode.removeChild(toast);
				}
			}, 300);
		}, 2000);
	}
});
