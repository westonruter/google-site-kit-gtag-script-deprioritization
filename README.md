# Site Kit GTag Script Deprioritization #

Contributors: [westonruter](https://profile.wordpress.org/westonruter)  
Tested up to: 6.8  
Stable tag:   0.1.0  
License:      [GPLv2](https://www.gnu.org/licenses/gpl-2.0.html) or later  
Tags:         performance

## Description ##

This plugin deprioritizes the loading of the GTag script in the [Site Kit by Google](https://wordpress.org/plugins/google-site-kit/) plugin to attempt to reduce network contention with loading resources in the critical rendering path (e.g. the LCP element image). It deprioritizes the GTag script by:

1. Adding `fetchpriority="low"` to the `script` tag.
2. Moving the `script` tag from the `head` to the footer.
3. Removing the `dns-prefetch` for `www.googletagmanager.com`.

This does not primarily benefit Chrome since that browser already gives `async` scripts a priority of low. It does benefit Safari and Firefox, however, since they have a default medium/normal priority.

Here is the performance impact in Firefox using a patched version of [benchmark-web-vitals](https://github.com/GoogleChromeLabs/wpp-research/tree/main/cli#benchmark-web-vitals) with [Firefox support](https://github.com/GoogleChromeLabs/wpp-research/pull/191):

```bash
npm run research -- benchmark-web-vitals -n 250 -o md --diff -b firefox \
  --url "http://localhost:10023/bison/?disable_site_kit_gtag_script_deprioritization=1" \
  --url "http://localhost:10023/bison/" 
```

| URL               | Before | After | Diff (ms) | Diff (%) |
|:------------------|-------:|------:|----------:|---------:|
| FCP (median)      |     75 |    74 |     -1.00 |    -1.3% |
| LCP (median)      |     76 |    75 |     -1.00 |    -1.3% |
| TTFB (median)     |     38 |    38 |      0.00 |     0.0% |
| LCP-TTFB (median) |     41 |    40 |     -1.00 |    -2.4% |

Note that I wasn't able to emulate a slower network connection since this isn't supported.

In Chrome, the performance gain on a broadband connection is marginal, as tested with:

```bash
npm run research -- benchmark-web-vitals -n 1000 -c "broadband" -o md --diff \
  --url "http://localhost:10023/bison/?disable_site_kit_gtag_script_deprioritization=1" \
  --url "http://localhost:10023/bison/"
```

| Metric            | Before |  After | Diff (ms) | Diff (%) |
|:------------------|-------:|-------:|----------:|---------:|
| FCP (median)      |  137.5 |  138.1 |     +0.60 |    +0.4% |
| LCP (median)      |    367 |  361.4 |     -5.60 |    -1.5% |
| TTFB (median)     |   31.4 |   31.4 |      0.00 |     0.0% |
| LCP-TTFB (median) |  335.5 | 329.95 |     -5.55 |    -1.7% |

And on a Fast 4G connection, the improvement in Chrome is even less remarkable:

```bash
npm run research -- benchmark-web-vitals -n 250 -c "Fast 4G" -o md --diff \
  --url "http://localhost:10023/bison/?disable_site_kit_gtag_script_deprioritization=1" \
  --url "http://localhost:10023/bison/"
```

| Metric            | Before |  After | Diff (ms) | Diff (%) |
|:------------------|-------:|-------:|----------:|---------:|
| FCP (median)      | 406.95 | 403.25 |     -3.70 |    -0.9% |
| LCP (median)      |    573 |  572.8 |     -0.20 |    -0.0% |
| TTFB (median)     |   32.3 |   32.4 |     +0.10 |    +0.3% |
| LCP-TTFB (median) | 540.75 | 539.95 |     -0.80 |    -0.1% |

To see the real benchmark improvements I would use Firefox. I did try briefly to [add Firefox to benchmark-web-vitals](https://github.com/GoogleChromeLabs/wpp-research/pull/191), but some of the Puppeteer APIs being used aren't supported yet.

Here is a diff of the change this applies:

```diff
--- before.html
+++ after.html
@@ -10,7 +10,6 @@
       }
     </style>
     <title>Bison &#8211; gtag-load-test</title>
-    <link rel="dns-prefetch" href="//www.googletagmanager.com" />
     <link
       rel="alternate"
       type="application/rss+xml"
@@ -1986,18 +1985,12 @@
       href="http://localhost:10023/wp-content/themes/twentytwentyfive/style.css?ver=1.2"
       media="all"
     />
-    <script id="google_gtagjs-js-before">
+    <script id="google_site_kit_gtag_deprioritization_data_layer-js-after">
       window.dataLayer = window.dataLayer || [];
       function gtag() {
         dataLayer.push(arguments);
       }
     </script>
-    <script
-      src="https://www.googletagmanager.com/gtag/js?id=abc123"
-      id="google_gtagjs-js"
-      async
-      data-wp-strategy="async"
-    ></script>
     <link rel="https://api.w.org/" href="http://localhost:10023/wp-json/" />
     <link
       rel="alternate"
@@ -2656,5 +2649,12 @@
         sibling.parentElement.insertBefore(skipLink, sibling);
       })();
     </script>
+    <script
+      src="https://www.googletagmanager.com/gtag/js?id=abc123"
+      id="google_gtagjs-js"
+      async
+      data-wp-strategy="async"
+      fetchpriority="low"
+    ></script>
   </body>
 </html>
```

I intend to propose this change for inclusion in Site Kit.

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
