# Site Kit GTag Script Deprioritization #

Contributors: [westonruter](https://profile.wordpress.org/westonruter)  
Tested up to: 6.8  
Stable tag:   0.1.0  
License:      [GPLv2](https://www.gnu.org/licenses/gpl-2.0.html) or later  
Tags:         performance

## Description ##

This plugin moves GTag script to the footer to further deprioritize to prevent impacting the critical rendering path. This is an extension to Site Kit by Google.

While the `google_gtagjs` script is already registered as being `async` and Chrome gives it a low fetch priority, other browsers do not automatically deprioritize async scripts; namely Safari and Firefox give such scripts a medium/normal priority. So in addition to moving the GTag script to the footer, it also adds `fetchpriority=low` to the script. 

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
