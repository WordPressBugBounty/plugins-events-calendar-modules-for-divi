/* =========================================================================
   Events Calendar Modules for Divi — wizard onboarding logic.
   Extracted from the inline <script> blocks of wizard-events-modules-divi.html
   (shared helpers + UTM appender, Step 2 layout & query, Step 3 done recap,
   Step 1 mode selection). Loaded AFTER assets/ecmd-wizard.js.

   Two pieces intentionally STAY inline in the HTML because they must run
   before ecmd-wizard.js / before first paint:
     1. window.ECMD_WIZARD         — wizard config; ecmd-wizard.js reads it
                                     synchronously at load time.
     2. the localStorage bootstrap — restores body[data-editor-selected] /
                                     [data-display-mode] / [data-page-created]
                                     and the persisted Step 1 `.is-selected`
                                     pick, avoiding a flash of wrong state.
   ========================================================================= */

/* =========================================================================
   CONFIG — single source of truth for every external URL, YouTube URL base
   and UTM string constant used in this file (owner's canonical link data
   for the ecmd / Events Calendar Modules for Divi wizard).

   UTM scheme (applies to every outbound eventscalendaraddons.com link):
     utm_source   = ecmd_plugin — the HOST plugin code (always this wizard,
                    even when a link points at a sibling addon page)
     utm_medium   = inside
     utm_campaign = the destination: demo | get_pro | docs | get_bundle
                    (plus `policy` for the coolplugins.net tracking policy)
     utm_content  = onboarding_step{N} — the wizard step hosting the link
   wordpress.org links get NO UTM parameters (none exist on this page).
   ========================================================================= */
var ECMD_ONBOARDING_CONFIG = {
  /* ---- UTM strings ---- */
  UTM_SOURCE: 'ecmd_plugin',
  UTM_MEDIUM: 'inside',
  UTM_CONTENT_STEP_PREFIX: 'onboarding_step',   /* + 1-based step number   */
  UTM_CONTENT_FALLBACK: 'onboarding',           /* link outside any step   */
  UTM_HOSTS: ['eventscalendaraddons.com', 'coolplugins.net'],
  CAMPAIGN_DEMO: 'demo',
  CAMPAIGN_DOCS: 'docs',
  CAMPAIGN_GET_PRO: 'get_pro',
  CAMPAIGN_GET_BUNDLE: 'get_bundle',
  CAMPAIGN_POLICY: 'policy',

  /* ---- eventscalendaraddons.com destinations ---- */
  PRO_URL:   'https://eventscalendaraddons.com/plugin/the-events-calendar-modules-for-divi/',
  DEMOS_URL: 'https://eventscalendaraddons.com/demos/the-events-calendar-modules-for-divi/',

  /* ---- YouTube — the canonical walkthrough video id (Z9s-7RgxZu8) is
          carried on the Step 1 option markup via data-youtube-id; the code
          below only assembles YouTube URLs from these bases. ---- */
  YT_THUMB_BASE: 'https://img.youtube.com/vi/',    /* + {id}/{quality}.jpg  */
  YT_EMBED_BASE: 'https://www.youtube.com/embed/', /* + {id} + params below */
  YT_EMBED_PARAMS: '?autoplay=1&rel=0&modestbranding=1',

  /* ---- Local asset fallbacks (normally provided by window.ECMD_WIZARD) ---- */
  ASSET_BASE_FALLBACK: 'helping-folder/',
  SIBLING_ICON_BASE_FALLBACK: 'events-addons-icons/',
  DIVI_ICON_FILE: 'divi-icon.png'
};

// Shared helpers used by every step script. Kept minimal so the file
// doesn't accumulate boilerplate.
//
//   __ECMD.setDisplayMode  — flips body[data-display-mode] AND persists
//                            to localStorage so a page reload doesn't
//                            lose the user's "shortcode inside builder"
//                            preference.
window.__ECMD = {
  setDisplayMode: function (mode) {
    var slug = window.ECMD_WIZARD.slug;
    var key = 'ecmd:wizard:' + slug + ':displayMode';
    if (mode === 'shortcode') {
      document.body.setAttribute('data-display-mode', 'shortcode');
      try { localStorage.setItem(key, 'shortcode'); } catch (_) {}
    } else {
      document.body.removeAttribute('data-display-mode');
      try { localStorage.removeItem(key); } catch (_) {}
    }
  },

  // Track whether the draft page has been created. Flips the Step 4 button
  // set from "Create Draft Page" to "Preview / Edit / Change styling" and
  // marks the progress-strip Done step in success green. Persisted so a
  // reload on Step 4 keeps the post-create state.
  setPageCreated: function (created) {
    var slug = window.ECMD_WIZARD.slug;
    var key = 'ecmd:wizard:' + slug + ':pageCreated';
    if (created) {
      document.body.setAttribute('data-page-created', 'true');
      try { localStorage.setItem(key, '1'); } catch (_) {}
    } else {
      document.body.removeAttribute('data-page-created');
      try { localStorage.removeItem(key); } catch (_) {}
    }
  },

  // ---------------- UTM appender ---------------------------------------
  // Walks every outgoing link to eventscalendaraddons.com / coolplugins.net
  // and appends utm_source / utm_medium / utm_campaign / utm_content per the
  // CONFIG scheme above. utm_campaign is derived from the destination path:
  //   - /demos/...             → demo
  //   - /docs/...              → docs
  //   - /pricing/              → get_bundle
  //   - /plugin/...            → get_pro
  //   - /terms/usage-tracking  → policy (coolplugins.net)
  // utm_content is derived from the wizard step hosting the link
  // (onboarding_step1..3). Explicit override via data-utm="{campaign}"
  // on the link (e.g. get_bundle).
  applyUtm: function () {
    var CFG = ECMD_ONBOARDING_CONFIG;

    function isTarget(url) {
      return CFG.UTM_HOSTS.some(function (h) { return url.indexOf(h) !== -1; });
    }
    function detectCampaign(url, el) {
      if (el && el.dataset && el.dataset.utm) return el.dataset.utm;
      if (/eventscalendaraddons\.com\/demos\//.test(url)) return CFG.CAMPAIGN_DEMO;
      if (/eventscalendaraddons\.com\/docs\//.test(url)) return CFG.CAMPAIGN_DOCS;
      if (/eventscalendaraddons\.com\/pricing/.test(url)) return CFG.CAMPAIGN_GET_BUNDLE;
      if (/eventscalendaraddons\.com\/plugin\//.test(url)) return CFG.CAMPAIGN_GET_PRO;
      if (/coolplugins\.net\/terms\/usage-tracking/.test(url)) return CFG.CAMPAIGN_POLICY;
      return null;
    }
    // utm_content = onboarding_step{N}: 1-based index of the wizard step
    // (from ECMD_WIZARD.steps) that hosts the link element.
    function detectContent(el) {
      var stepEl = el && el.closest ? el.closest('.ecmd-wizard-step') : null;
      var steps = (window.ECMD_WIZARD && window.ECMD_WIZARD.steps) || [];
      if (stepEl) {
        for (var i = 0; i < steps.length; i++) {
          if (steps[i].id === stepEl.getAttribute('data-step')) {
            return CFG.UTM_CONTENT_STEP_PREFIX + (i + 1);
          }
        }
      }
      return CFG.UTM_CONTENT_FALLBACK;
    }
    function append(url, campaign, content) {
      if (!url || !campaign || url.indexOf('utm_source=') !== -1) return url;
      var params = { utm_source: CFG.UTM_SOURCE, utm_medium: CFG.UTM_MEDIUM,
                     utm_campaign: campaign, utm_content: content };
      var qs = Object.keys(params).map(function (k) {
        return k + '=' + encodeURIComponent(params[k]);
      }).join('&');
      var sep = url.indexOf('?') === -1 ? '?' : '&';
      var hashIdx = url.indexOf('#');
      if (hashIdx === -1) return url + sep + qs;
      return url.slice(0, hashIdx) + sep + qs + url.slice(hashIdx);
    }

    document.querySelectorAll('a[href]').forEach(function (a) {
      var href = a.getAttribute('href');
      if (!href || !isTarget(href)) return;
      var campaign = detectCampaign(href, a);
      if (campaign) a.setAttribute('href', append(href, campaign, detectContent(a)));
    });
    document.querySelectorAll('[data-demo-url]').forEach(function (el) {
      var url = el.getAttribute('data-demo-url');
      if (!url || !isTarget(url)) return;
      var campaign = detectCampaign(url, el);
      if (campaign) el.setAttribute('data-demo-url', append(url, campaign, detectContent(el)));
    });
  }
};

// Run UTM appender once, after the DOM is parsed. It's idempotent
// (skips URLs that already have utm_source), so late-added links stay safe.
if (document.readyState !== 'loading') window.__ECMD.applyUtm();
else document.addEventListener('DOMContentLoaded', window.__ECMD.applyUtm);

/* ============================================================
   WordPress integration: create the draft page and persist wizard state.
   The prototype path simply advanced the wizard; in WP, this capture-phase
   handler runs before wizard.js's delegated Next handler, completes the AJAX
   work, then advances after success.
   ============================================================ */
(function () {
  function wpConfig() {
    return window.ECMD_ONBOARDING || null;
  }

  function readState() {
    var slug = window.ECMD_WIZARD && window.ECMD_WIZARD.slug;
    if (!slug) return {};
    try {
      var raw = localStorage.getItem('ecmd:wizard:' + slug + ':state');
      return raw ? (JSON.parse(raw) || {}) : {};
    } catch (_) {
      return {};
    }
  }

  function collectSelections() {
    var state = readState();
    var selections = state.selections || {};
    document.querySelectorAll('[data-required-selection]').forEach(function (group) {
      var name = group.dataset.requiredSelection;
      var selected = group.querySelector('.is-selected[data-value]');
      if (name && selected && selected.dataset.value) selections[name] = selected.dataset.value;
    });
    return selections;
  }

  function ajax(fields) {
    var cfg = wpConfig();
    if (!cfg || !cfg.ajaxUrl) return Promise.reject(new Error('Missing ECMD_ONBOARDING.ajaxUrl'));
    var fd = new FormData();
    Object.keys(fields).forEach(function (key) { fd.append(key, fields[key]); });
    return fetch(cfg.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
      .then(function (r) { return r.json(); });
  }

  function i18n(key, fallback) {
    var labels = (window.ECMD_ONBOARDING && window.ECMD_ONBOARDING.i18n) || {};
    return labels[key] || fallback;
  }

  function createVerb() {
    var page = (window.ECMD_ONBOARDING && window.ECMD_ONBOARDING.page) || {};
    return (page.exists && page.isDraft) ? i18n('updateDraft', 'Update Draft Page') : i18n('createDraft', 'Create Draft Page');
  }

  function busyLabel() {
    var page = (window.ECMD_ONBOARDING && window.ECMD_ONBOARDING.page) || {};
    return (page.exists && page.isDraft) ? i18n('updating', 'Updating page…') : i18n('creating', 'Creating page…');
  }

  function syncCreateLabel() {
    document.querySelectorAll('[data-step-primary-cta] span:last-child').forEach(function (el) {
      el.textContent = createVerb();
    });
  }

  function setBusy(btn, busy, label) {
    if (!btn) return;
    if (busy) {
      if (!btn._ecmdHtml) btn._ecmdHtml = btn.innerHTML;
      btn.classList.add('is-busy');
      btn.setAttribute('aria-busy', 'true');
      btn.setAttribute('aria-disabled', 'true');
      btn.setAttribute('data-ecmd-busy', '1');
      if (label) {
        btn.innerHTML = '<span class="dashicons dashicons-update" aria-hidden="true"></span> <span>' + label + '</span>';
      }
    } else {
      btn.classList.remove('is-busy');
      btn.removeAttribute('aria-busy');
      btn.removeAttribute('aria-disabled');
      btn.removeAttribute('data-ecmd-busy');
      if (btn._ecmdHtml) {
        btn.innerHTML = btn._ecmdHtml;
        btn._ecmdHtml = null;
      }
    }
  }

  function setFailed(btn, message) {
    if (!btn) return;
    btn.classList.remove('is-busy');
    btn.removeAttribute('aria-busy');
    btn.removeAttribute('aria-disabled');
    btn.removeAttribute('data-ecmd-busy');
    btn._ecmdHtml = null;
    btn.innerHTML = '<span class="dashicons dashicons-warning" aria-hidden="true"></span> <span>' +
      (message || 'Failed — retry') + '</span>';
  }

  function savePreferences(selections) {
    var cfg = wpConfig();
    if (!cfg || !cfg.nonceSavePrefs) return Promise.resolve();
    var telemetry = document.querySelector('[data-wizard-telemetry]');
    return ajax({
      action: 'ecmd_onboarding_save_preferences',
      nonce: cfg.nonceSavePrefs,
      telemetry: telemetry && telemetry.checked ? '1' : '0',
      selections: JSON.stringify(selections || {})
    }).then(function (res) {
      if (res && res.success && res.data && res.data.telemetry) {
        window.ECMD_ONBOARDING.telemetry = res.data.telemetry;
        applyTelemetryUi();
      }
    }).catch(function () {});
  }

  function applyTelemetryUi() {
    var t = window.ECMD_ONBOARDING && window.ECMD_ONBOARDING.telemetry;
    var wrap = document.querySelector('.ecmd-telemetry');
    var box = document.querySelector('[data-wizard-telemetry]');
    if (!wrap) return;
    if (t && t.show === false) {
      wrap.hidden = true;
      return;
    }
    wrap.hidden = false;
    if (box && t && typeof t.checked === 'boolean') box.checked = !!t.checked;
  }

  if (document.readyState !== 'loading') applyTelemetryUi();
  else document.addEventListener('DOMContentLoaded', applyTelemetryUi);

  if (document.readyState !== 'loading') syncCreateLabel();
  else document.addEventListener('DOMContentLoaded', syncCreateLabel);

  function applyDraftLinks(data) {
    if (!data) return;
    document.querySelectorAll('a[href="#preview-draft"]').forEach(function (a) {
      if (data.previewUrl) a.href = data.previewUrl;
    });
    document.querySelectorAll('a[href="#edit-draft"]').forEach(function (a) {
      if (data.editUrl) a.href = data.editUrl;
    });
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-step-primary-cta]');
    var cfg = wpConfig();
    if (!btn || !cfg) return;
    e.preventDefault();
    e.stopImmediatePropagation();
    if (btn.classList.contains('is-busy')) return;

    var selections = collectSelections();
    setBusy(btn, true, busyLabel());
    savePreferences(selections)
      .then(function () {
        return ajax({
          action: 'ecmd_onboarding_create_page',
          nonce: cfg.nonceCreatePage,
          selections: JSON.stringify(selections)
        });
      })
      .then(function (res) {
        if (!res || !res.success) {
          throw new Error((res && res.data && res.data.message) || 'Could not create draft page.');
        }
        if (res.data && res.data.updated && window.ECMD_ONBOARDING && window.ECMD_ONBOARDING.page) {
          window.ECMD_ONBOARDING.page.exists = true;
          window.ECMD_ONBOARDING.page.isDraft = true;
        } else if (window.ECMD_ONBOARDING && window.ECMD_ONBOARDING.page) {
          window.ECMD_ONBOARDING.page.exists = true;
          window.ECMD_ONBOARDING.page.isDraft = true;
        }
        syncCreateLabel();
        applyDraftLinks(res.data);
        window.__ECMD.setPageCreated(true);
        btn.removeAttribute('data-step-primary-cta');
        btn.click();
        btn.setAttribute('data-step-primary-cta', 'true');
      })
      .catch(function (err) {
        setFailed(btn, err.message || 'Failed — retry');
      })
      .then(function () {
        if (!btn.classList.contains('is-busy') || btn.getAttribute('data-ecmd-busy') !== '1') return;
        setBusy(btn, false);
      });
  }, true);

  document.addEventListener('click', function (e) {
    var finish = e.target.closest('[data-wizard-finish]');
    var cfg = wpConfig();
    if (!finish || !cfg || !cfg.nonceComplete) return;
    document.body.classList.add('ecmd-finishing');
    ajax({
      action: 'ecmd_onboarding_complete',
      nonce: cfg.nonceComplete
    }).catch(function () {});
  }, true);
})();

/* ============================================================
   Combined Step 2 (Layout & Query) behaviour: Pro-selection tracking
   (Grid/Carousel) → notice + nav swap, auto-select defaults on step-enter,
   and pageCreated=true when the primary CTA is clicked (Modules for Divi
   always creates a draft Divi page).
   ============================================================ */
(function () {
  var step = document.querySelector('.ecmd-wizard-step[data-step="layout-query"]');
  if (!step) return;

  // (The Events Category multiselect row was removed for the Divi wizard —
  //  it keeps only Layout + "Which events to load?". No multiselect widget.)

  // ----- Auto-select first non-Pro option in each required group -----
  function autoSelectDefaults() {
    step.querySelectorAll('[data-required-selection]').forEach(function (group) {
      if (group.hidden || group.closest('[hidden]')) return;
      if (group.querySelector('.is-selected')) return;
      var first = group.querySelector(
        '.ecmd-layout-card:not([data-pro]), .ecmd-style-card:not([data-pro]), .ecmd-toggle-option:not([data-pro])'
      );
      if (first) first.click();
    });
  }

  // Pro installed on disk but not active → Activate CTA instead of Upgrade.
  function isDiviProInactive() {
    var cfg = window.ECMD_ONBOARDING || {};
    if (cfg.previewState === 'pro-inactive') return true;
    return document.body.getAttribute('data-preview-state') === 'pro-inactive';
  }

  function applyProFeatureCta(navRight, inactive) {
    if (!navRight) return;
    var upgradeBtn = navRight.querySelector('[data-step2-nav-variant="upgrade"]');
    if (!upgradeBtn) return;
    var label = upgradeBtn.querySelector('span:not(.dashicons)');
    var icon = upgradeBtn.querySelector('.dashicons');
    if (inactive) {
      upgradeBtn.setAttribute('href', '#');
      upgradeBtn.removeAttribute('target');
      upgradeBtn.removeAttribute('rel');
      upgradeBtn.setAttribute('data-activate-pro', '1');
      if (icon) icon.className = 'dashicons dashicons-plugins-checked';
      if (label) label.textContent = 'Activate Pro';
      return;
    }
    var href = upgradeBtn.getAttribute('data-upgrade-href')
      || 'https://eventscalendaraddons.com/plugin/the-events-calendar-modules-for-divi/';
    upgradeBtn.setAttribute('href', href);
    upgradeBtn.setAttribute('target', '_blank');
    upgradeBtn.setAttribute('rel', 'noopener');
    upgradeBtn.removeAttribute('data-activate-pro');
    if (icon) icon.className = 'dashicons dashicons-star-filled';
    if (label) label.textContent = 'Upgrade to Pro';
  }

  // ----- Pro detection → notice + nav swap (visible selections only) --
  function updateProState() {
    var count = 0;
    step.querySelectorAll('.is-selected[data-pro="true"]').forEach(function (el) {
      if (el.offsetParent !== null) count++;
    });
    var notice = step.querySelector('[data-pro-notice]');
    var countEl = step.querySelector('[data-pro-count]');
    var plural = step.querySelector('[data-pro-plural]');
    var desc = step.querySelector('[data-pro-notice-desc]');
    var navRight = step.querySelector('[data-step2-nav-right]');
    var inactive = isDiviProInactive();
    if (notice) notice.hidden = count === 0;
    if (countEl) countEl.textContent = count;
    if (plural) plural.textContent = count === 1 ? '' : 's';
    if (desc) {
      var key = inactive ? 'data-desc-activate' : 'data-desc-upgrade';
      var copy = desc.getAttribute(key);
      if (copy) desc.textContent = copy;
    }
    if (navRight) {
      navRight.setAttribute('data-nav-mode', count > 0 ? 'upgrade' : 'default');
      if (count > 0) applyProFeatureCta(navRight, inactive);
    }
  }

  // Click on the primary CTA — Modules for Divi always creates a draft
  // Divi page, so we flip the pageCreated flag before wizard.js advances.
  step.addEventListener('click', function (e) {
    var cta = e.target.closest('[data-step-primary-cta]');
    if (!cta) return;
    window.__ECMD.setPageCreated(true);
  });

  // ----- Boot on step-enter -------------------------------------------
  function refresh() {
    autoSelectDefaults();
    updateProState();
  }

  document.addEventListener('ecmd:wizard-step', function (e) {
    if (e.detail.stepId === 'layout-query') requestAnimationFrame(refresh);
  });

  // Refresh Pro state on any selection change inside this step
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.ecmd-layout-card, .ecmd-style-card, .ecmd-toggle-option')) return;
    requestAnimationFrame(updateProState);
  });
})();

/* ============================================================
   Step 3 (Done) behaviour: read wizard state from localStorage and update
   the Divi builder recap + dynamic success title. A draft Divi page is
   always created, so there is no pre-create UI. The bundle promo is always
   visible (no stack-aware toggling).
   ============================================================ */
(function () {
  var stepSuccess = document.querySelector('.ecmd-wizard-step[data-step="success"]');
  if (!stepSuccess) return;

  var ASSET_BASE = window.ECMD_WIZARD.assetBase || ECMD_ONBOARDING_CONFIG.ASSET_BASE_FALLBACK;
  // Modules for Divi is Divi-only, so BUILDER_INFO has one entry.
  var BUILDER_INFO = {
    divi: { label: 'Divi', logo: ASSET_BASE + ECMD_ONBOARDING_CONFIG.DIVI_ICON_FILE }
  };

  // ----- State collection ---------------------------------------------
  // Read persisted state from localStorage (source of truth wizard.js
  // writes to) and use the DOM to look up display labels.
  function collectState() {
    var persisted = {};
    try {
      var raw = localStorage.getItem('ecmd:wizard:' + window.ECMD_WIZARD.slug + ':state');
      if (raw) {
        var s = JSON.parse(raw);
        if (s && s.selections) persisted = s.selections;
      }
    } catch (_) {}

    var state = {};

    // Look up the layout label from persisted state.
    var layoutValue = persisted.layout;
    if (!layoutValue) {
      var domSel = document.querySelector('.ecmd-wizard-step [data-required-selection="layout"] .is-selected');
      if (domSel) layoutValue = domSel.dataset.value;
    }
    if (layoutValue) {
      var card = document.querySelector(
        '.ecmd-wizard-step [data-required-selection="layout"] [data-value="' + layoutValue + '"]'
      );
      var labelEl = card && card.querySelector('.ecmd-layout-card__name');
      state.layout = {
        value: layoutValue,
        label: labelEl ? labelEl.textContent.trim() : layoutValue
      };
    }

    return state;
  }

  // ----- Refresh Step 3 (Done) ----------------------------------------
  // Post-create only. Update the title with the picked layout name and set
  // the Divi builder recap card. The bundle promo below is always visible
  // (team decision — promote the Events Calendar Addons bundle instead of
  // per-sibling cross-sells), so no stack-aware toggling is needed.
  function refreshStep(stepEl) {
    if (!stepEl) return;
    var state = collectState();
    var layoutName = (state.layout && state.layout.label) || 'Events';
    var bldrPanel = stepEl.querySelector('[data-review-panel="builder-recap"]');

    // Dynamic success-header copy (mirrors the SPB "Congrats!" pattern).
    var t = stepEl.querySelector('[data-success-title]');
    var l = stepEl.querySelector('[data-success-lede]');
    if (t) t.textContent = 'Congrats! Your Events ' + layoutName + ' is ready.';
    if (l) l.textContent = 'Draft Divi page created — preview or open it in the Divi Builder to fine-tune anything.';

    // Builder recap (always Divi).
    if (bldrPanel) {
      var info = BUILDER_INFO.divi;
      var logo = bldrPanel.querySelector('[data-review-builder-logo]');
      var title= bldrPanel.querySelector('[data-review-builder-title]');
      var desc = bldrPanel.querySelector('[data-review-builder-desc]');
      if (logo) logo.src = info.logo;
      if (title) title.textContent = 'Events module inserted in Divi page';
      if (desc) desc.textContent = 'Preview or open the draft to fine-tune anything in the Divi Builder.';
    }
  }

  // ----- Step-enter refresh -------------------------------------------
  document.addEventListener('ecmd:wizard-step', function (e) {
    if (e.detail.stepId === 'success') requestAnimationFrame(function () { refreshStep(stepSuccess); });
  });

  // ----- Finish / Recreate: clear persisted state ---------------------
  // Both actions kill the wizard's state key, display-mode key, AND the
  // page-created flag so the next visit starts genuinely fresh. Finish
  // lets the anchor navigate to the hub; Recreate hard-reloads the wizard.
  function clearPersistedState() {
    var slug = window.ECMD_WIZARD && window.ECMD_WIZARD.slug;
    if (!slug) return;
    try {
      localStorage.removeItem('ecmd:wizard:' + slug + ':state');
      localStorage.removeItem('ecmd:wizard:' + slug + ':displayMode');
      localStorage.removeItem('ecmd:wizard:' + slug + ':pageCreated');
    } catch (_) {}
    document.body.removeAttribute('data-page-created');
  }
  // Both Finish (bottom of Step 4) and Exit Setup (top header) share the
  // same behaviour: clear ALL persisted wizard keys and let the anchor
  // navigate to the ECA dashboard.
  document.addEventListener('click', function (e) {
    if (!e.target.closest('[data-wizard-finish]')) return;
    clearPersistedState();
    try { localStorage.setItem('ecmd:wizard:' + window.ECMD_WIZARD.slug + ':completed', '1'); } catch (_) {}
  });
})();

/* ============================================================
   Step 1 specific behavior: video swap + promo (Divi Modules Pro) +
   nav-mode swap + auto-advance on dismiss.
   ============================================================ */
(function () {
  var SIBLING_ICON_BASE = (window.ECMD_WIZARD && window.ECMD_WIZARD.siblingIconBase) || ECMD_ONBOARDING_CONFIG.SIBLING_ICON_BASE_FALLBACK;
  // Only one entry — the Single Page mode promotes Divi Modules PRO.
  // Custom single event page templates in the Divi Builder ship in the Pro
  // version of THIS same addon, so `proHref` (external landing page) is
  // used when not installed. When Pro IS installed, `wizardHref` opens the
  // live demos gallery (there's no separate sibling wizard).
  var ADDON_MAP = {
    'single-page': {
      addon: 'Events Calendar Modules for Divi Pro',
      addonShort: 'Divi Modules',
      icon: SIBLING_ICON_BASE + 'events-calendar-modules-for-divi.svg',
      wizardHref: ECMD_ONBOARDING_CONFIG.DEMOS_URL,
      proHref: ECMD_ONBOARDING_CONFIG.PRO_URL,
      desc: 'Design a custom single event page template inside the Divi Builder — Pro only.'
    }
  };

  // Preview state → active / inactive / absent Divi Pro.
  function getProPreviewState(builder) {
    if (builder !== 'single-page') return '';
    return document.body.getAttribute('data-preview-state') || '';
  }

  function updatePromo(modeValue) {
    var promo = document.querySelector('[data-editor-promo]');
    if (!promo) return;

    // Listing mode (or nothing picked) → no promo, plain Continue nav
    var info = ADDON_MAP[modeValue];
    if (!info) {
      promo.hidden = true;
      promo.innerHTML = '';
      promo.className = 'ecmd-editor-promo';
      setNavMode('default', null);
      return;
    }

    var state = getProPreviewState(modeValue);
    var installed = state === 'pro-installed';
    var inactive = state === 'pro-inactive';
    var mod = installed ? 'ecmd-editor-promo--ready' : 'ecmd-editor-promo--suggest';
    var kicker;
    var title;
    var desc;

    if (installed) {
      kicker = '<span class="dashicons dashicons-yes-alt" aria-hidden="true"></span> Already active';
      title = info.addon + ' is active on your site';
      desc = 'Jump into the Divi Builder to design your single event page template.';
    } else if (inactive) {
      kicker = '<span class="dashicons dashicons-warning" aria-hidden="true"></span> Installed but not active';
      title = 'Activate ' + info.addon;
      desc = 'You already have Pro on your site. Activate the plugin to unlock single event page templates.';
    } else {
      kicker = '<span class="dashicons dashicons-star-filled" aria-hidden="true"></span> Pro feature';
      title = 'Get ' + info.addon;
      desc = info.desc;
    }

    promo.className = 'ecmd-editor-promo ' + mod;
    promo.hidden = false;
    promo.innerHTML =
      '<span class="ecmd-editor-promo__icon">' +
        '<img src="' + info.icon + '" alt="">' +
      '</span>' +
      '<div class="ecmd-editor-promo__body">' +
        '<span class="ecmd-editor-promo__kicker">' + kicker + '</span>' +
        '<h3 class="ecmd-editor-promo__title">' + title + '</h3>' +
        '<p class="ecmd-editor-promo__desc">' + desc + '</p>' +
      '</div>';

    // Swap the nav-right area to promo mode
    setNavMode(installed ? 'promo-ready' : 'promo-suggest', info, inactive);
  }

  function setNavMode(mode, info, inactive) {
    var navRight = document.querySelector('.ecmd-wizard-step.is-active [data-nav-right]');
    if (!navRight) return;
    navRight.setAttribute('data-nav-mode', mode);

    if (info) {
      // Push addon name into all promo buttons
      navRight.querySelectorAll('[data-addon-name]').forEach(function (el) {
        el.textContent = info.addonShort;
      });
      var openBtn = navRight.querySelector('[data-promo-open]');
      var installBtn = navRight.querySelector('[data-promo-install]');
      // Open (Pro is active) → demos gallery.
      // Inactive Pro → Plugins screen to activate.
      // Absent Pro → external Get Pro landing page.
      if (openBtn) {
        openBtn.setAttribute('href', info.wizardHref);
        if (/^https?:/.test(info.wizardHref)) {
          openBtn.setAttribute('target', '_blank');
          openBtn.setAttribute('rel', 'noopener');
        }
      }
      if (installBtn) {
        if (inactive) {
          installBtn.setAttribute('href', '#');
          installBtn.removeAttribute('target');
          installBtn.removeAttribute('rel');
          installBtn.setAttribute('data-activate-pro', '1');
          var icon = installBtn.querySelector('.dashicons');
          if (icon) icon.className = 'dashicons dashicons-plugins-checked';
          var label = installBtn.querySelector('span:last-child');
          if (label) label.innerHTML = 'Activate <span data-addon-name>' + info.addonShort + '</span> Pro';
        } else {
          installBtn.setAttribute('href', info.proHref || info.wizardHref);
          installBtn.setAttribute('target', '_blank');
          installBtn.setAttribute('rel', 'noopener');
          installBtn.removeAttribute('data-activate-pro');
          var star = installBtn.querySelector('.dashicons');
          if (star) star.className = 'dashicons dashicons-star-filled';
          var getLabel = installBtn.querySelector('span:last-child');
          if (getLabel) getLabel.innerHTML = 'Get <span data-addon-name>' + info.addonShort + '</span> Pro';
        }
      }
      // Re-tag promo links with UTM. applyUtm() only walked the DOM once at
      // DOMContentLoaded, before these hrefs existed. It is idempotent, so
      // re-running just decorates the newly set URLs.
      if (window.__ECMD && window.__ECMD.applyUtm) window.__ECMD.applyUtm();
    }
  }

  // Match Create Draft / install CTAs: is-busy + spinning update icon.
  function setActivateBusy(btn, busy, label) {
    if (!btn) return;
    if (busy) {
      if (!btn._ecmdActivateHtml) btn._ecmdActivateHtml = btn.innerHTML;
      btn.classList.add('is-busy');
      btn.setAttribute('aria-busy', 'true');
      btn.setAttribute('aria-disabled', 'true');
      btn.setAttribute('data-ecmd-busy', '1');
      if (label) {
        btn.innerHTML = '<span class="dashicons dashicons-update" aria-hidden="true"></span> <span>' + label + '</span>';
      }
      return;
    }
    btn.classList.remove('is-busy');
    btn.removeAttribute('aria-busy');
    btn.removeAttribute('aria-disabled');
    btn.removeAttribute('data-ecmd-busy');
    if (btn._ecmdActivateHtml) {
      btn.innerHTML = btn._ecmdActivateHtml;
      btn._ecmdActivateHtml = null;
    }
  }

  function setActivateFailed(btn, message) {
    if (!btn) return;
    btn.classList.remove('is-busy');
    btn.removeAttribute('aria-busy');
    btn.removeAttribute('aria-disabled');
    btn.removeAttribute('data-ecmd-busy');
    btn._ecmdActivateHtml = null;
    btn.innerHTML = '<span class="dashicons dashicons-warning" aria-hidden="true"></span> <span>' +
      (message || 'Failed \u2014 retry') + '</span>';
  }

  // AJAX activate Divi Pro. On success, swap to Continue (Pro onboarding) — no auto-redirect.
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-activate-pro]');
    if (!btn) return;
    e.preventDefault();
    if (btn.classList.contains('is-busy') || btn.getAttribute('data-ecmd-busy') === '1') return;
    var cfg = window.ECMD_ONBOARDING;
    var init = cfg && cfg.proInit;
    if (!init || !cfg.ajaxUrl || !cfg.nonceActivate) return;
    setActivateBusy(btn, true, 'Activating\u2026');
    var fd = new FormData();
    fd.append('action', 'eca_dashboard_plugin_activate');
    fd.append('security', cfg.nonceActivate);
    fd.append('init', init);
    fetch(cfg.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (res) {
        if (res && res.success) {
          btn.classList.remove('is-busy');
          btn.removeAttribute('aria-busy');
          btn.removeAttribute('aria-disabled');
          btn.removeAttribute('data-ecmd-busy');
          btn.removeAttribute('data-activate-pro');
          btn._ecmdActivateHtml = null;
          btn.setAttribute('href', cfg.ajaxUrl.replace('admin-ajax.php', 'admin.php?page=ecmd-onboarding'));
          btn.innerHTML = '<span>Continue</span> <span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span>';
        } else {
          setActivateFailed(btn, (res && res.data && res.data.message) || 'Failed \u2014 retry');
        }
      })
      .catch(function () {
        setActivateFailed(btn, 'Failed \u2014 retry');
      });
  });

  function updateVideoPreview(modeValue, videoLabel) {
    var video = document.querySelector('[data-editor-video]');
    var label = document.querySelector('[data-editor-video-label]');
    var watermark = document.querySelector('[data-editor-video-watermark]');
    var frame = document.querySelector('[data-video-frame]');
    var selected = document.querySelector('.ecmd-editor-option.is-selected');

    // Reset any playing iframe when selection changes
    if (video) {
      video.classList.remove('is-playing');
      video.setAttribute('data-editor', modeValue || '');
    }
    if (frame) frame.innerHTML = '';

    if (label) label.textContent = videoLabel || 'Pick a setup to see a walkthrough';

    // Set the YouTube thumbnail as background. Preload maxresdefault (HD);
    // if it fails, fall back to hqdefault. Clear if nothing selected.
    if (video) {
      var ytId = selected && selected.dataset.youtubeId;
      if (ytId) {
        var maxres = ECMD_ONBOARDING_CONFIG.YT_THUMB_BASE + ytId + '/maxresdefault.jpg';
        var hq     = ECMD_ONBOARDING_CONFIG.YT_THUMB_BASE + ytId + '/hqdefault.jpg';
        var probe = new Image();
        probe.onload = function () {
          if (this.naturalWidth < 200) {
            video.style.backgroundImage = 'url("' + hq + '")';
          } else {
            video.style.backgroundImage = 'url("' + maxres + '")';
          }
        };
        probe.onerror = function () {
          video.style.backgroundImage = 'url("' + hq + '")';
        };
        probe.src = maxres;
        video.style.backgroundImage = 'url("' + hq + '")';
      } else {
        video.style.backgroundImage = '';
      }
    }

    // Watermark: always Divi for this wizard.
    var assetBase = (window.ECMD_WIZARD && window.ECMD_WIZARD.assetBase) || ECMD_ONBOARDING_CONFIG.ASSET_BASE_FALLBACK;
    if (watermark) {
      if (modeValue) {
        watermark.src = assetBase + ECMD_ONBOARDING_CONFIG.DIVI_ICON_FILE;
        watermark.hidden = false;
      } else {
        watermark.hidden = true;
      }
    }
  }

  function playVideo() {
    var video = document.querySelector('[data-editor-video]');
    var frame = document.querySelector('[data-video-frame]');
    var selected = document.querySelector('.ecmd-editor-option.is-selected');
    if (!video || !frame) return;
    var ytId = selected && selected.dataset.youtubeId;
    if (!ytId) return;
    frame.innerHTML = '<iframe src="' + ECMD_ONBOARDING_CONFIG.YT_EMBED_BASE + ytId
      + ECMD_ONBOARDING_CONFIG.YT_EMBED_PARAMS + '" title="Walkthrough video" '
      + 'frameborder="0" allow="accelerometer; autoplay; clipboard-write; '
      + 'encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
    video.classList.add('is-playing');
  }

  document.addEventListener('click', function (e) {
    // Mode option selected → swap video + update promo.
    var opt = e.target.closest('.ecmd-editor-option');
    if (opt && !opt.classList.contains('is-locked')) {
      window.__ECMD.setDisplayMode(null);
      // Picking a different mode is a substantive change — reset the
      // pageCreated flag so Step 4 starts fresh.
      window.__ECMD.setPageCreated(false);
      var value = opt.dataset.value;
      var videoLabel = opt.dataset.videoLabel || '';
      requestAnimationFrame(function () {
        updateVideoPreview(value, videoLabel);
        updatePromo(value);
      });
      return;
    }

    // Play the walkthrough when the user clicks the video thumbnail.
    var video = e.target.closest('[data-editor-video]');
    if (video && !video.classList.contains('is-playing') && video.getAttribute('data-editor')) {
      e.preventDefault();
      playVideo();
      return;
    }

    // "No, back to Events Listing" dismiss → switch selection to Listing
    // and auto-advance.
    var dismiss = e.target.closest('[data-editor-promo-dismiss]');
    if (dismiss) {
      e.preventDefault();
      var listingOpt = document.querySelector('.ecmd-editor-option[data-value="listing"]');
      if (listingOpt) listingOpt.click();
      requestAnimationFrame(function () {
        var nextBtn = document.querySelector('.ecmd-wizard-step.is-active [data-wizard-next]');
        if (nextBtn) nextBtn.click();
      });
    }
  });

  // Preferred pre-selection: Listing is the free happy-path; fall back
  // to Single Page if for some reason Listing isn't visible.
  var PREFERRED_ORDER = ['listing', 'single-page'];

  function isVisible(el) {
    return el && el.offsetParent !== null;
  }

  function autoSelectPreferred() {
    var current = document.querySelector('.ecmd-editor-option.is-selected');
    if (isVisible(current)) return;

    // Persisted state always wins over the preferred-order fallback.
    try {
      var slug = window.ECMD_WIZARD.slug;
      var raw = localStorage.getItem('ecmd:wizard:' + slug + ':state');
      if (raw) {
        var s = JSON.parse(raw);
        if (s && s.selections && s.selections.editor) return;
      }
    } catch (_) {}

    if (current) current.classList.remove('is-selected');
    for (var i = 0; i < PREFERRED_ORDER.length; i++) {
      var opt = document.querySelector('.ecmd-editor-option[data-value="' + PREFERRED_ORDER[i] + '"]');
      if (isVisible(opt)) { opt.click(); return; }
    }
  }

  // Preview state change → re-evaluate promo (install/ready flip).
  document.addEventListener('ecmd:preview-state', function () {
    var selected = document.querySelector('.ecmd-editor-option.is-selected');
    if (isVisible(selected)) {
      updatePromo(selected.dataset.value);
    } else {
      autoSelectPreferred();
    }
  });

  // Re-run when user navigates back to Step 1 from a later step
  document.addEventListener('ecmd:wizard-step', function (e) {
    if (e.detail.stepId !== 'mode') return;
    var selected = document.querySelector('.ecmd-editor-option.is-selected');
    if (isVisible(selected)) {
      updateVideoPreview(selected.dataset.value, selected.dataset.videoLabel || '');
      updatePromo(selected.dataset.value);
    } else {
      autoSelectPreferred();
    }
  });

  // Boot
  function bootstrap() {
    var selected = document.querySelector('.ecmd-editor-option.is-selected');
    if (isVisible(selected)) {
      updateVideoPreview(selected.dataset.value, selected.dataset.videoLabel || '');
      updatePromo(selected.dataset.value);
    } else {
      autoSelectPreferred();
    }
  }
  if (document.readyState !== 'loading') bootstrap();
  else document.addEventListener('DOMContentLoaded', bootstrap);
})();
