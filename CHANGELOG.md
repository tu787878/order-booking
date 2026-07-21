# Changelog

## 1.8.0
- Added API v2 (`ordertcg/v2/manage/*`) using AES-256-GCM authenticated encryption for the `code` parameter, replacing the reversible base64 scheme.
- Kept API v1 (`ordertcg/v1/manage/*`) unchanged on the legacy base64 auth for backward compatibility; it will be removed once all callers migrate to v2.
- Fixed the popup image upload (`manage/save_popup`), which previously failed silently because the `save_image2()` helper was undefined; base64 uploads now create a real media attachment.

## 1.7.6
- Fixed cart discount percentage display so it no longer shows values above 100% when the discount amount is larger than the current subtotal.
- Improved consistency between cart and checkout discount presentation.
