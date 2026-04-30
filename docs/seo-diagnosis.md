# SEO Technical Troubleshooting: Website Not Indexing

## Scenario
"A new Worknoon website is not indexing even after sitemap submission."

## Diagnostic Framework

This guide provides a systematic approach to diagnosing and resolving indexation issues.

---

## 1. Crawlability Tests

### A. Robots.txt Audit

**Check location:** `https://www.worknoon.com/robots.txt`

**Common issues:**

**❌ BLOCKING ALL CRAWLERS:**
User-agent: *
Disallow: /

**Fix:** Change to allow crawling:
User-agent: *
Allow: /
Sitemap: https://www.worknoon.com/sitemap.xml


**❌ BLOCKING GOOGLEBOT:**
User-agent: Googlebot
Disallow: /

**Fix:** Remove Googlebot-specific blocks or change to Allow

**✅ CORRECT FORMAT:**
User-agent: *
Disallow: /wp-admin/
Disallow: /wp-includes/
Allow: /wp-admin/admin-ajax.php
Sitemap: https://www.worknoon.com/sitemap.xml

**Testing:**
- Google Search Console → Settings → Robots.txt Tester
- Or visit: `/robots.txt` directly in browser

---

### B. Meta Robots Tag Check

**Inspect page source** (`Ctrl+U` or right-click → View Page Source)

**Look for:**
```html
<meta name="robots" content="noindex, nofollow">
```

**❌ BLOCKING INDEXING:**
- `noindex` - Tells search engines not to index
- `nofollow` - Tells search engines not to follow links

**Common culprits:**
1. **WordPress Settings**
   - Settings → Reading → "Discourage search engines from indexing this site" ✓
   - **Fix:** Uncheck this box

2. **SEO Plugin Settings**
   - Yoast SEO → Search Appearance → "Noindex" set on post types
   - Rank Math → Titles & Meta → Noindex settings
   - **Fix:** Remove noindex from public pages

3. **Development Mode**
   - Coming Soon plugins (SeedProd, WP Maintenance Mode)
   - **Fix:** Deactivate or set to "Live" mode

**✅ CORRECT FORMAT:**
```html
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
```

---

### C. Server Status & Accessibility

**Test URL accessibility:**

**Tool 1: cURL Test**
```bash
curl -I https://www.worknoon.com
```

**Look for:**
HTTP/2 200 OK  ← Good! Site is accessible
HTTP/2 404     ← Not Found
HTTP/2 403     ← Forbidden
HTTP/2 500     ← Server Error
HTTP/2 503     ← Service Unavailable

**Tool 2: Google Search Console URL Inspection**
- Search Console → URL Inspection
- Enter URL: `https://www.worknoon.com`
- Check: "Coverage" section
- Look for: "URL is on Google" or error messages

**Common server issues:**
- **Firewall blocking Googlebot IP ranges**
- **Rate limiting too aggressive**
- **Server timeouts (slow response)**
- **HTTPS certificate errors**

**Fix:**
- Allow Googlebot IPs in firewall
- Increase rate limit thresholds
- Optimize server response time (< 2 seconds)
- Fix SSL certificate issues

---

### D. JavaScript Rendering Issues

**Test if content loads without JavaScript:**

**Method 1: Disable JavaScript in browser**
- Chrome → DevTools → Settings → Disable JavaScript
- Reload page → Check if content visible

**Method 2: Use Google's Mobile-Friendly Test**
- https://search.google.com/test/mobile-friendly
- Enter URL → View "Screenshot" of how Google sees it
- Compare to actual page

**Common issues:**
- Content loaded entirely via JavaScript (React, Vue, Angular SPAs)
- Key text/headings inside JS-rendered components
- Lazy loading not working for Googlebot

**Fix:**
- Implement server-side rendering (SSR)
- Use pre-rendering (Prerender.io, Rendertron)
- Add static HTML fallback for critical content
- Test with Google's URL Inspection "View Crawled Page"

---

## 2. Canonical Checks

### A. Canonical Tag Audit

**Inspect page source for:**
```html
<link rel="canonical" href="https://www.worknoon.com/">
```

**❌ COMMON CANONICAL ERRORS:**

**Error 1: Self-Referencing to Wrong URL**
```html
<!-- On https://www.worknoon.com/about -->
<link rel="canonical" href="https://www.worknoon.com/">
```
**Issue:** About page points to homepage  
**Fix:** Should point to itself:
```html
<link rel="canonical" href="https://www.worknoon.com/about">
```

**Error 2: HTTP vs HTTPS Mismatch**
```html
<!-- On HTTPS page -->
<link rel="canonical" href="http://www.worknoon.com/">
```
**Issue:** HTTPS page canonicals to HTTP version  
**Fix:** Use HTTPS in canonical:
```html
<link rel="canonical" href="https://www.worknoon.com/">
```

**Error 3: WWW vs Non-WWW Mismatch**
```html
<!-- On https://worknoon.com -->
<link rel="canonical" href="https://www.worknoon.com/">
```
**Issue:** Inconsistent domain format  
**Fix:** Choose one format (www or non-www) and stick to it everywhere

**Error 4: Multiple Canonical Tags**
```html
<link rel="canonical" href="https://www.worknoon.com/">
<link rel="canonical" href="https://worknoon.com/">
```
**Issue:** Conflicting signals confuse Google  
**Fix:** Remove duplicates, keep only one

**Error 5: Canonical to 404 Page**
```html
<link rel="canonical" href="https://www.worknoon.com/deleted-page">
```
**Issue:** Points to non-existent page  
**Fix:** Update to existing page or remove canonical

**Validation:**
- Google Search Console → Coverage → "Duplicate without user-selected canonical"
- Check for "Alternate page with proper canonical tag"

---

### B. Redirect Chain Check

**Test URL redirect path:**
```bash
curl -L -I https://worknoon.com
```

**❌ BAD: Multiple redirects**
http://worknoon.com
→ 301 → http://www.worknoon.com
→ 301 → https://www.worknoon.com
→ 301 → https://www.worknoon.com/

**Issue:** 3+ redirect hops slow crawling, waste crawl budget

**✅ GOOD: Single redirect**
http://worknoon.com
→ 301 → https://www.worknoon.com/

**Fix:**
- Implement direct 301 redirects
- Avoid redirect chains
- Use .htaccess or server config

---

## 3. Sitemap Structure Issues

### A. Sitemap Accessibility

**Test sitemap URL:** `https://www.worknoon.com/sitemap.xml`

**Should return:**
- **Status Code:** 200 OK
- **Content-Type:** `application/xml` or `text/xml`
- **Valid XML:** Properly formatted

**❌ COMMON ERRORS:**

**Error 1: 404 Not Found**
**Fix:**
- Implement direct 301 redirects
- Avoid redirect chains
- Use .htaccess or server config

---

## 3. Sitemap Structure Issues

### A. Sitemap Accessibility

**Test sitemap URL:** `https://www.worknoon.com/sitemap.xml`

**Should return:**
- **Status Code:** 200 OK
- **Content-Type:** `application/xml` or `text/xml`
- **Valid XML:** Properly formatted

**❌ COMMON ERRORS:**

**Error 1: 404 Not Found**
**Fix:**
- Implement direct 301 redirects
- Avoid redirect chains
- Use .htaccess or server config

---

## 3. Sitemap Structure Issues

### A. Sitemap Accessibility

**Test sitemap URL:** `https://www.worknoon.com/sitemap.xml`

**Should return:**
- **Status Code:** 200 OK
- **Content-Type:** `application/xml` or `text/xml`
- **Valid XML:** Properly formatted

**❌ COMMON ERRORS:**

**Error 1: 404 Not Found**
https://www.worknoon.com/sitemap.xml → 404

**Fix:** 
- Regenerate sitemap (Yoast, Rank Math, XML Sitemap plugins)
- Check .htaccess for blocking rules
- Verify sitemap file exists

**Error 2: HTML Instead of XML**
```xml
<!DOCTYPE html>  ← Wrong! This is HTML, not XML
<html>
```
**Fix:**
- Check if sitemap is being overridden by page/template
- Verify sitemap plugin is active
- Clear cache (WP Rocket, W3 Total Cache)

**Error 3: Blocked by Robots.txt**


In robots.txt:
Disallow: /sitemap.xml  ← Blocking sitemap!

**Fix:** Remove sitemap from Disallow rules

---

### B. Sitemap Content Validation

**Check sitemap structure:**

**✅ VALID SITEMAP:**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://www.worknoon.com/</loc>
    <lastmod>2026-04-29</lastmod>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>https://www.worknoon.com/about</loc>
    <lastmod>2026-04-28</lastmod>
    <priority>0.8</priority>
  </url>
</urlset>
```

**❌ COMMON SITEMAP ISSUES:**

**Issue 1: Contains Noindex URLs**
- Sitemap includes pages with `<meta name="robots" content="noindex">`
- **Fix:** Exclude noindex pages from sitemap

**Issue 2: Contains 404 URLs**
- Sitemap lists deleted/non-existent pages
- **Fix:** Remove dead URLs, regenerate sitemap

**Issue 3: Contains Redirect URLs**
- Sitemap lists URLs that 301 redirect elsewhere
- **Fix:** List final destination URLs only

**Issue 4: Wrong Protocol**
```xml
<loc>http://www.worknoon.com/</loc>  ← HTTP in sitemap
<!-- But site serves HTTPS -->
```
**Fix:** Use HTTPS in all sitemap URLs

**Issue 5: Too Large**
- Sitemap > 50MB or > 50,000 URLs
- **Fix:** Split into multiple sitemaps, use sitemap index:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>https://www.worknoon.com/sitemap-pages.xml</loc>
  </sitemap>
  <sitemap>
    <loc>https://www.worknoon.com/sitemap-posts.xml</loc>
  </sitemap>
</sitemapindex>
```

**Validation Tools:**
- XML Sitemap Validator: https://www.xml-sitemaps.com/validate-xml-sitemap.html
- Google Search Console → Sitemaps → Submit and check status

---

### C. Sitemap Submission in Search Console

**Steps:**
1. Google Search Console → Sitemaps
2. Enter sitemap URL: `sitemap.xml`
3. Click "Submit"

**Check status:**
- **✅ Success:** "Sitemap submitted successfully"
- **❌ Error:** "Couldn't fetch sitemap" → Check accessibility
- **⚠️ Warning:** "Some URLs couldn't be indexed" → Check Coverage report

**Common Search Console sitemap errors:**
- "Sitemap could not be read" → XML syntax error
- "General HTTP error" → Server/firewall issue
- "Unsupported file format" → Wrong content-type header
- "Sitemap is an HTML page" → Not actually XML

---

## 4. Page Speed Indexing Blockers

### A. Core Web Vitals Impact

**Google prioritizes fast sites for indexing.**

**Test page speed:**
- PageSpeed Insights: https://pagespeed.web.dev/
- GTmetrix: https://gtmetrix.com/

**Minimum thresholds:**
- **LCP (Largest Contentful Paint):** < 2.5 seconds
- **FID (First Input Delay):** < 100ms
- **CLS (Cumulative Layout Shift):** < 0.1

**Slow sites get:**
- Slower crawl rate
- Delayed indexing
- Lower crawl budget

**Fix slow speeds:**
1. **Optimize images**
   - Compress (TinyPNG, Smush)
   - WebP format
   - Lazy loading

2. **Minify CSS/JS**
   - Autoptimize
   - WP Rocket
   - Remove unused CSS

3. **Enable caching**
   - Browser caching
   - Server-side caching (Redis, Memcached)
   - CDN (Cloudflare)

4. **Upgrade hosting**
   - Shared hosting → VPS or managed WordPress
   - Better server specs

---

### B. Render-Blocking Resources

**Issue:** CSS/JS blocks page rendering

**Identify blockers:**
- PageSpeed Insights → "Eliminate render-blocking resources"

**Fix:**
```html
<!-- Bad: Blocking CSS -->
<link rel="stylesheet" href="style.css">

<!-- Good: Async load non-critical CSS -->
<link rel="preload" href="style.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

<!-- Bad: Blocking JS -->
<script src="script.js"></script>

<!-- Good: Defer JS -->
<script src="script.js" defer></script>
```

**WordPress fixes:**
- Autoptimize → "Inline and defer CSS"
- WP Rocket → "Load JavaScript deferred"

---

### C. Server Response Time

**Test TTFB (Time To First Byte):**
- WebPageTest: https://www.webpagetest.org/
- Ideal: < 600ms
- Acceptable: < 1 second
- Slow: > 1 second

**Slow TTFB causes:**
- Unoptimized database queries
- No server caching
- Underpowered hosting
- Too many plugins

**Fix:**
- Enable object caching (Redis)
- Optimize database (WP-Optimize)
- Reduce plugins
- Upgrade hosting tier

---

## 5. Search Console Debugging Steps

### A. URL Inspection Tool

**Step-by-step diagnosis:**

**1. Open Search Console**
- Go to: https://search.google.com/search-console

**2. URL Inspection**
- Click "URL Inspection" (top)
- Enter: `https://www.worknoon.com/about`
- Press Enter

**3. Check "Coverage" Status**

**✅ Good statuses:**
- "URL is on Google" → Indexed successfully
- "URL can be indexed" → Eligible, will be indexed soon

**❌ Problem statuses:**
- "URL is not on Google: Crawled - currently not indexed" → Low value or duplicate
- "URL is not on Google: Discovered - currently not indexed" → In queue, waiting
- "URL is not on Google: Excluded by 'noindex' tag" → Meta robots blocking
- "URL is not on Google: Blocked by robots.txt" → robots.txt issue
- "URL is not on Google: Page with redirect" → Redirects to another page
- "URL is not on Google: Not found (404)" → Broken link
- "URL is not on Google: Server error (5xx)" → Server issues

**4. Click "Test Live URL"**
- Tests current state (not cached)
- Shows how Googlebot sees page right now

**5. Click "View Crawled Page"**
- Shows HTML Google received
- Shows rendered screenshot
- Compare to actual page → Identify rendering issues

**6. Request Indexing**
- If issues fixed, click "Request Indexing"
- Google prioritizes re-crawl
- Check back in 24-48 hours

---

### B. Coverage Report Analysis

**Search Console → Coverage**

**Check:**
1. **Error count:** URLs with errors preventing indexing
2. **Valid with warnings:** Indexed but with issues
3. **Valid:** Successfully indexed
4. **Excluded:** Intentionally not indexed

**Common "Excluded" reasons:**
- "Duplicate, Google chose different canonical" → Canonical issue
- "Crawled - currently not indexed" → Low quality content
- "Discovered - currently not indexed" → New pages waiting
- "Alternate page with proper canonical tag" → Correct duplicate handling
- "Page with redirect" → Redirects working correctly

**Actions:**
- Click each error/warning type
- Review affected URLs
- Fix issues
- Click "Validate Fix"
- Monitor re-crawl

---

### C. Index Coverage Improvement

**If "Crawled - currently not indexed":**

**Reasons Google doesn't index:**
1. **Thin content** → Add more valuable content (500+ words)
2. **Duplicate content** → Make unique or consolidate
3. **Low internal links** → Link from important pages
4. **Low external links** → Build backlinks
5. **New site** → Be patient, keep adding quality content

**Immediate actions:**
- Improve content quality and length
- Add internal links from high-priority pages
- Request indexing via URL Inspection
- Build quality backlinks

---

## 6. Advanced Diagnostics

### A. Log File Analysis

**If nothing else works, check server logs:**

**Access logs** (via cPanel/SSH):
```bash
grep "Googlebot" /var/log/apache2/access.log
```

**Look for:**
- Googlebot visit frequency
- Status codes (200, 404, 500, 503)
- Crawled URLs
- User agent strings

**What to check:**
- Is Googlebot visiting at all? (If no, check robots.txt/firewall)
- Getting 200 OK status? (If not, fix errors)
- Crawling important pages? (If not, improve internal linking)

---

### B. Check for Manual Actions

**Search Console → Security & Manual Actions → Manual Actions**

**If manual action exists:**
- "Unnatural links" → Disavow toxic backlinks
- "Thin content" → Improve content quality
- "Cloaking" → Show same content to users and bots
- "Hacked site" → Clean malware, request reconsideration

**Fix:**
- Address the issue
- Submit reconsideration request
- Wait 1-4 weeks for review

---

### C. Check Google Business Profile Suspension

**If local business site:**
- Suspended Google Business Profile can affect indexing
- Check: https://business.google.com/
- If suspended: Appeal or verify identity

---

## 7. Complete Troubleshooting Checklist

**Run through this in order:**

### ☐ Phase 1: Basic Checks (30 minutes)
- [ ] Settings → Reading → "Discourage search engines" is UNCHECKED
- [ ] robots.txt allows crawling (no `Disallow: /`)
- [ ] No `<meta name="robots" content="noindex">` in page source
- [ ] Site returns 200 OK status (curl test)
- [ ] HTTPS works correctly (no SSL errors)
- [ ] Sitemap.xml is accessible and valid XML

### ☐ Phase 2: Search Console (30 minutes)
- [ ] Site verified in Google Search Console
- [ ] Sitemap submitted successfully
- [ ] URL Inspection shows "URL can be indexed" or is indexed
- [ ] No manual actions
- [ ] Coverage report shows no critical errors

### ☐ Phase 3: Technical SEO (1 hour)
- [ ] Canonical tags point to correct URLs
- [ ] No redirect chains (max 1 redirect)
- [ ] Page speed > 50 (mobile), > 70 (desktop)
- [ ] Server response time < 1 second
- [ ] JavaScript content renders correctly

### ☐ Phase 4: Content Quality (1-2 hours)
- [ ] Pages have 500+ words of unique content
- [ ] Proper heading structure (H1, H2, H3)
- [ ] Internal links from homepage/important pages
- [ ] Quality external links (backlinks)
- [ ] Schema markup implemented

### ☐ Phase 5: Waiting Period
- [ ] New sites: Allow 2-4 weeks for initial indexing
- [ ] Changed content: Request re-index, wait 24-48 hours
- [ ] Technical fixes: Wait 1 week for re-crawl

---

## 8. Expected Timeline

**After fixes:**
- **Immediate (24-48 hours):** URL Inspection re-crawl
- **Short-term (1 week):** Normal crawl cycle picks up changes
- **Medium-term (2-4 weeks):** New site initial indexing
- **Long-term (1-3 months):** Building authority, increasing crawl rate

**Don't expect instant indexing!** Even with perfect technical SEO, indexing takes time.

---

## 9. When to Escalate

**Contact Google Support if:**
- All checks pass
- No technical issues found
- 6+ weeks with no indexing
- Manual action appeal ignored
- Site suddenly deindexed without reason

**Where to get help:**
- Google Search Central Help Forum
- Twitter @GoogleSearchC
- Professional SEO audit

---

## Summary

**Most common causes of indexing issues (in order):**
1. ✓ "Discourage search engines" checked in WordPress
2. ✓ robots.txt blocking Googlebot
3. ✓ Noindex meta tag on pages
4. ✓ Sitemap not submitted or contains errors
5. ✓ Slow page speed (> 3 seconds)
6. ✓ Canonical tag errors
7. ✓ Thin/duplicate content
8. ✓ New site waiting period

**Fix these first, then move to advanced diagnostics.**

**Remember:** Patience is key. Even perfect technical SEO requires time for indexing.