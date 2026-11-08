# Changelog

## 1.8.24
- Added an active checkbox to each special time-window row so rules can be disabled without deleting them.

## 1.8.23
- Use a 24-hour time picker for special delivery and pickup time windows.

## 1.8.22
- Widened special time-window columns so complete times remain visible.

## 1.8.21
- Prevent all delivery or pickup card actions, including row deletion, while that service is off.

## 1.8.20
- Visually disable the full delivery or pickup settings card when its service is turned off.

## 1.8.19
- Merged preparation-time and time-interval overrides into one table per service; blank value cells leave that setting at its standard value.

## 1.8.18
- Moved popup settings into Bestellannahme; grouped standard values and weekday-specific time windows; disabled delivery and pickup settings when the service is off; arranged time settings and closing times side by side with compact tables and German “bis” labels.

## 1.8.17
- Simplified the delivery and pickup settings tab with grouped, collapsible sections, separate service cards, compact time-window editors, and optional help text.

## 1.8.16
- Added multiple daily custom time-interval windows for delivery and pickup, with fixed intervals used outside the windows and preparation boundaries preserved in frontend selections.

## 1.8.15
- Apply preparation times to available frontend time selections for delivery and pickup, using custom time windows and the fixed default elsewhere. Keep the selected order time unchanged and validate availability before checkout.

## 1.8.14
- Show close_shop_text when no enabled pickup or delivery service is currently open, including breaks and times before opening or after closing.
- Render the closing bar on product-category menu URLs as well as menu Pages.

## 1.8.13
- Fixed invisible checkout field labels in the white theme while preserving dark-theme label colors.

## 1.8.12
- Fixed the closing bar during weekly breaks within date-specific opening hours, and ignored disabled delivery/pickup methods when checking whether the restaurant is open.

## 1.8.11
- Added multiple daily preparation-time windows for delivery and pickup in both settings screens, overriding the fixed time during matching windows, including overnight periods.

## 1.8.10
- Keep coupon error notifications visible for at least eight seconds and prevent older popup timers from hiding newer notifications.

## 1.8.9
- Show the German minimum-order requirement when a coupon is applied below its configured order minimum.

## 1.8.8
- Enabled announcement bar animation on desktop and mobile.
- Kept shop headers visible while scrolling on desktop and mobile.
- Added a mobile scroll-to-top button positioned above floating cart controls.

## 1.8.7
- Fixed duplicate worksheet-scoped filter definitions that could cause Excel to reject or repair multi-sheet exports.

## 1.8.6
- Improved Excel report overview, table readability and column widths; made linked charts compact with fixed dimensions.

## 1.8.5
- Restricted statistics and report export to administrators; client Shop Besitzer accounts no longer see or access reports.
- Added circle, column and line diagrams, with editable Excel charts linked to exported worksheet data.

## 1.8.4
- Added product statistics, date filters, sorting, time and day analysis, and formatted Excel export from shop settings.

## 1.8.1
- Added a setting to show or hide the `Bestellzeitpunkt` (order placed time) on the printed receipt and pool receipts, found under *Pool Druckverteilung* in tab 4 of the plugin settings. Enabled by default, including on existing installs.
- Synced the stale `$plugin_config['version']` value with the plugin header version (the value is informational; the updater reads the header).

## 1.8.0
- Added API v2 (`ordertcg/v2/manage/*`) using AES-256-GCM authenticated encryption for the `code` parameter, replacing the reversible base64 scheme.
- Kept API v1 (`ordertcg/v1/manage/*`) unchanged on the legacy base64 auth for backward compatibility; it will be removed once all callers migrate to v2.
- Fixed the popup image upload (`manage/save_popup`), which previously failed silently because the `save_image2()` helper was undefined; base64 uploads now create a real media attachment.

## 1.7.6
- Fixed cart discount percentage display so it no longer shows values above 100% when the discount amount is larger than the current subtotal.
- Improved consistency between cart and checkout discount presentation.
