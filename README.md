# Site Kit GTag Script Deprioritization #

Contributors: [westonruter](https://profile.wordpress.org/westonruter)  
Tested up to: 6.8  
Stable tag:   0.1.0  
License:      [GPLv2](https://www.gnu.org/licenses/gpl-2.0.html) or later  
Tags:         performance

## Description ##

This plugin moves the GTag script to the footer, adds `fetchpriority=low`, and eliminates the `dns-prefetch` resource hint to deprioritize to prevent it from impacting the critical rendering path. This is an extension to [Site Kit by Google](https://wordpress.org/plugins/google-site-kit/) plugin.

This does not primarily benefit Chrome since it already gives `async` scripts a priority of low. It does benefit Safari and Firefox, however, since they have a default medium/normal priority.

## Installation ##

1. Download the plugin [ZIP from GitHub](https://github.com/westonruter/google-site-kit-gtag-script-deprioritization/archive/refs/heads/main.zip) or if you have a local clone of the repo run `npm run plugin-zip`.
2. Visit **Plugins > Add New Plugin** in the WordPress Admin.
3. Click **Upload Plugin**.
4. Select the `google-site-kit-gtag-script-deprioritization.zip` file on your system from step 1 and click **Install Now**.
5. Click the **Activate Plugin** button.

You may also install and update via [Git Updater](https://git-updater.com/).

## Changelog ##

### 0.1.0 ###

* Initial release.
