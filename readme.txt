=== ClickReach – Social Share Buttons & Counter ===
Contributors: abineshselvarasu
Donate link: https://profiles.wordpress.org/abineshselvarasu/
Tags: social share, share buttons, social counter, share counter, social sharing
Requires at least: 5.6
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lightweight, privacy-friendly social sharing buttons with live click counters. Zero bloat, pure inline SVGs, Core Web Vitals friendly.

== Description ==

**ClickReach – Social Share Buttons & Counter** is an ultra-fast, lightweight, and privacy-focused social sharing plugin for WordPress. It adds beautiful, responsive social sharing buttons with real-time click and share counters to your posts, pages, and custom post types without slowing down your website.

Unlike heavy commercial alternatives that load bloated libraries, external fonts, and slow down your Core Web Vitals, **ClickReach** is built from the ground up for speed, simplicity, and compliance.

### 🚀 Key Features

* **100% Free Forever:** No locked features, no "Pro" version nag screens, and no subscription fees.
* **Ultra-Lightweight & Fast:** Pure embedded inline SVGs. Zero external HTTP requests, zero webfont downloads, and no render-blocking styles.
* **Real-time Share & Click Counters:** Live click counting via secure AJAX that updates without requiring a page refresh.
* **Privacy-Friendly & GDPR Compliant:** No third-party tracking scripts, no cookies, and no personal data collection.
* **Top Social Platforms Supported:**
  * **X (formerly Twitter)** with official branding
  * **Facebook**
  * **LinkedIn**
  * **WhatsApp** (optimized for mobile viral sharing)
  * **Pinterest**
  * **Reddit**
  * **Copy Link** (with instant "Copied!" toast feedback)
* **Customizable Button Shapes:** Choose between **Rounded** (soft corners), **Pill** (full capsule), or **Flat** (clean rectangular) styles.
* **Flexible Placement:**
  * Automatically display above content, below content, or both.
  * Target specific post types (Posts, Pages, or Custom Post Types).
  * Manual embedding anywhere via shortcode: `[clickreach_social_share_buttons]`.
* **Total Share Count Badge:** Optional aggregate counter showing total shares across all platforms.
* **Per-Post Controls:** Easily enable or disable share buttons individually on any specific post or page from the editor sidebar.
* **Clean Code:** Strictly adheres to WordPress.org security, nonces, sanitization, and coding standards.

== Installation ==

### Automatic Installation (Recommended)
1. Log in to your WordPress Admin Dashboard.
2. Navigate to **Plugins > Add New**.
3. Search for `ClickReach Social Share Buttons`.
4. Click **Install Now** and then **Activate**.

### Manual Installation
1. Download the `clickreach-social-share-buttons.zip` file.
2. Log in to your WordPress Dashboard and go to **Plugins > Add New > Upload Plugin**.
3. Choose the zip file and click **Install Now**.
4. Activate the plugin.

### Configuration
1. Go to **Settings > ClickReach Social Share Buttons** in your WordPress dashboard.
2. Select your desired social networks, button shapes, and display positions.
3. Click **Save Settings**.

== Frequently Asked Questions ==

= Does this plugin require jQuery on the frontend? =
No! The frontend sharing buttons and AJAX click tracker are written in pure vanilla JavaScript, ensuring zero dependency on jQuery and optimal Core Web Vitals scores.

= Does this plugin slow down my site? =
Not at all. The plugin uses pure inline SVGs (less than 3KB total) and loads CSS/JS conditionally only on pages where buttons are actively displayed.

= How does the click counter work? =
When a visitor clicks a share link, a secure background AJAX request increments the recorded count for that specific post and platform, updating the live counter in real time without refreshing the page.

= Can I place the buttons manually? =
Yes! Simply use the shortcode `[clickreach_social_share_buttons]` inside any post, page, block, or page builder (Elementor, Beaver Builder, etc.).

= Is this plugin GDPR compliant? =
Yes, completely. It does not set cookies, does not collect personal visitor data, and does not connect to third-party ad or tracking networks. The only external connections made are the standard share redirect URLs that open on the social platform's own website when a user clicks a share button — no data is sent automatically or in the background.

== External Services ==

This plugin generates share links that redirect visitors to external social media platforms when they choose to click a share button. No data is sent automatically or in the background — connections only happen when a visitor actively clicks a share button, opening the social platform's standard share page in a new tab.

The following external services are used as share destinations:

**X (Twitter)**
Used to share a post title and URL on X (formerly Twitter).
Data sent: Post URL and post title (only when user clicks the share button).
Service URL: https://twitter.com/intent/tweet
Terms of Service: https://twitter.com/en/tos
Privacy Policy: https://twitter.com/en/privacy

**Facebook**
Used to share a post URL on Facebook.
Data sent: Post URL (only when user clicks the share button).
Service URL: https://www.facebook.com/sharer/sharer.php
Terms of Service: https://www.facebook.com/terms.php
Privacy Policy: https://www.facebook.com/policy.php

**LinkedIn**
Used to share a post title and URL on LinkedIn.
Data sent: Post URL and post title (only when user clicks the share button).
Service URL: https://www.linkedin.com/feed/
Terms of Service: https://www.linkedin.com/legal/user-agreement
Privacy Policy: https://www.linkedin.com/legal/privacy-policy

**WhatsApp**
Used to share a post title and URL via WhatsApp.
Data sent: Post URL and post title (only when user clicks the share button).
Service URL: https://api.whatsapp.com/send
Terms of Service: https://www.whatsapp.com/legal/terms-of-service
Privacy Policy: https://www.whatsapp.com/legal/privacy-policy

**Pinterest**
Used to share a post URL and description on Pinterest.
Data sent: Post URL and post title (only when user clicks the share button).
Service URL: https://pinterest.com/pin/create/button/
Terms of Service: https://policy.pinterest.com/en/terms-of-service
Privacy Policy: https://policy.pinterest.com/en/privacy-policy

**Reddit**
Used to share a post URL and title on Reddit.
Data sent: Post URL and post title (only when user clicks the share button).
Service URL: https://reddit.com/submit
Terms of Service: https://www.redditinc.com/policies/user-agreement
Privacy Policy: https://www.reddit.com/policies/privacy-policy

**Copy Link (no external service)**
Copies the post URL to the visitor's clipboard. No external connection is made.

== Screenshots ==

1. Clean, responsive frontend social sharing buttons with live count badges and optional Total Shares counter.
2. Settings page (Settings > ClickReach Social Share Buttons) to configure enabled networks, button styles, and positions.
3. Post editor sidebar metabox showing per-post display overrides and detailed click analytics.

== Changelog ==

= 1.0.0 =
* Initial public release on WordPress.org.
* Embedded crisp inline SVG icons for X, Facebook, LinkedIn, WhatsApp, Pinterest, Reddit, and Copy Link.
* Added real-time AJAX click tracking with secure nonces.
* Added flexible display positioning (Above, Below, Both, or Shortcode).
* Added 3 button shape styles: Rounded, Pill, and Flat.
* Added per-post override metabox in post editor.
* Added Total Share Count badge option.
* 100% WordPress.org coding standard compliance.

== Upgrade Notice ==

= 1.0.0 =
Initial release of ClickReach – Social Share Buttons & Counter.
