# Bros Clean Admin – Hide Dashboard Ads

**Bros Clean Admin** is a lightweight WordPress plugin that cleans up your `wp-admin` area by hiding most ads, review messages and promo banners, while keeping real warnings and errors visible.  

Power users can also define their own custom promo/review keywords to hide, and developers can hook into the keyword lists via simple filters.

---

## ✨ Features

- Hides dashboard promo widgets and sale banners  
- Hides "Rate us / Leave a review" style messages  
- Hides common plugin promo / upsell boxes on settings pages  
- Keeps WordPress core error/warning notices visible  
- Simple settings page under **Settings → Bros Clean Admin**  
- Custom promo + review keyword lists (comma-separated)  
- Translation-ready (`bros-clean-admin` text domain)  
- One-click **Reset to Defaults** button  
- Handy activation notice with direct **Settings** link  
- Small, focused codebase – no impact on the frontend

---

## 📦 Files in This Plugin

- `clean-admin.php` – main plugin file  
- `brosclad-admin-clean.js` – admin cleaner script  
- `readme.txt` – WordPress.org-style readme (for the plugin directory)  
- `screenshot-1.png` – plugin screenshot  
- `languages/` *(optional)* – translation files (e.g. `bros-clean-admin.pot`)

---

## 🚀 Installation

### 1. Install from WordPress.org (Recommended)

1. Go to [WordPress.org plugin page](https://wordpress.org/plugins/bros-clean-admin-hide-dashboard-ads/)
2. Click **"Download Version X.X"**
3. You'll get: `bros-clean-admin-hide-dashboard-ads.zip`
4. In your WordPress admin: **Plugins → Add New → Upload Plugin**
5. Upload the ZIP file
6. Click **Install Now**, then **Activate Plugin**
7. Go to **Settings → Bros Clean Admin** to configure

### 2. Install from GitHub (Development Version)

**Option A: Download ZIP from GitHub**
1. Click **"Code"** → **"Download ZIP"** on this repository
2. Extract the ZIP file
3. **Rename folder** from `Bros-Clean-Admin` to `bros-clean-admin-hide-dashboard-ads`
4. Upload to your server: `/wp-content/plugins/bros-clean-admin-hide-dashboard-ads/`
5. Activate in WordPress

**Option B: Git Clone (for developers)**
```bash
cd wp-content/plugins
git clone https://github.com/EneaCodes/Bros-Clean-Admin.git bros-clean-admin-hide-dashboard-ads
