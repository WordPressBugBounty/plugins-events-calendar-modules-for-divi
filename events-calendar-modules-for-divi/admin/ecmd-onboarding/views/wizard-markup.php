<?php
/**
 * ECMD onboarding wizard markup (admin view).
 *
 * Expected variables (set by ECMD_Onboarding_Page::get_wizard_markup()):
 * @var string $dashboard_url
 * @var string $plugin_icon
 * @var string $divi_icon
 * @var string $preview_state
 * @var bool   $show_telemetry
 *
 * @package Events_Calendar_Modules_For_Divi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ecmd-onboarding-admin" data-preview-state="<?php echo esc_attr( $preview_state ); ?>">
<div class="ecmd-wizard-shell">

  <!-- ============================================================
       Header — brand (left) + progress steps (center) + Exit (right)
       Replaces the admin bar + old sticky progress strip.
       ============================================================ -->
  <header class="ecmd-wizard-header">
    <div class="ecmd-wizard-header__brand">
      <img src="<?php echo esc_url( $plugin_icon ); ?>" alt="" class="ecmd-wizard-header__brand-icon">
      <span class="ecmd-wizard-header__brand-name">
        <strong>Events Calendar Modules</strong>
        <em>for Divi</em>
      </span>
    </div>

    <ol class="ecmd-wizard-header__steps" data-wizard-progress></ol>

    <a href="<?php echo esc_url( $dashboard_url ); ?>" data-wizard-finish class="ecmd-wizard-header__exit">
      <span>Exit Setup</span>
      <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
    </a>
  </header>

  <!-- ============================================================
       Main content — card holds each step with inline nav.
       ============================================================ -->
  <main class="ecmd-wizard-main">
    <div class="ecmd-wizard-card">

      <!-- ============================================================
           STEP 1 — Setup / Mode selection
           Modules for Divi is a Divi-only addon, so this step asks the
           user what they want to build (an events listing vs a single
           event page template) rather than which editor to use.
           The `data-required-selection="editor"` group name is kept
           for wizard.js compatibility — only the values change.
           Listing ships `is-selected` in raw markup so seedSelectionsFromDom
           enables the Continue CTA on first load (no localStorage / incognito).
           ============================================================ -->
      <section class="ecmd-wizard-step is-active" data-step="mode">
        <header class="ecmd-wizard-card__heading">
          <h1 class="ecmd-wizard-card__title">Set Up The Events Calendar with Divi</h1>
          <p class="ecmd-wizard-card__desc">Choose what you want to create today: an events listing page or a custom single event page template in Divi.</p>
        </header>

        <div class="ecmd-editor-selector">
          <!-- Left: mode options grid — two options stack vertically. -->
          <div class="ecmd-editor-selector__options" data-required-selection="editor">

            <a href="#" role="button" class="ecmd-editor-option is-selected" data-value="listing" data-video-label="Events Calendar Modules for Divi" data-youtube-id="Z9s-7RgxZu8">
              <img src="<?php echo esc_url( $plugin_icon ); ?>" alt="" class="ecmd-editor-option__icon">
              <span class="ecmd-editor-option__text">
                <span class="ecmd-editor-option__name">Events Listing</span>
                <span class="ecmd-editor-option__sub">Show events from The Events Calendar in List, Grid and Carousel layouts using the Events Layout Module in Divi Builder.</span>
              </span>
              <span class="ecmd-editor-option__check" aria-hidden="true"><span class="dashicons dashicons-yes"></span></span>
            </a>

            <a href="#" role="button" class="ecmd-editor-option" data-value="single-page" data-video-label="Event Single Page (Divi)" data-youtube-id="Z9s-7RgxZu8">
              <img src="<?php echo esc_url( $plugin_icon ); ?>" alt="" class="ecmd-editor-option__icon">
              <span class="ecmd-editor-option__text">
                <span class="ecmd-editor-option__name">Single Event Page Template</span>
                <span class="ecmd-editor-option__sub">Create a custom single event page template in Divi Builder and replace the default page from The Events Calendar.</span>
              </span>
              <span class="ecmd-editor-option__check" aria-hidden="true"><span class="dashicons dashicons-yes"></span></span>
            </a>

          </div>

          <!-- Right: YouTube-style dummy video preview (swaps per selection) -->
          <div class="ecmd-editor-selector__video" data-editor-video>
            <img src="" alt="" class="ecmd-editor-selector__video-watermark" data-editor-video-watermark hidden>
            <span class="ecmd-editor-selector__video-waves" aria-hidden="true"></span>
            <a href="#" class="ecmd-editor-selector__video-play" aria-label="Play walkthrough video" data-editor-video-play></a>
            <span class="ecmd-editor-selector__video-label">
              <span class="dashicons dashicons-video-alt3" aria-hidden="true"></span>
              <span data-editor-video-label>Pick a setup to see a walkthrough</span>
            </span>
            <!-- Iframe container (populated when Play is clicked) -->
            <div class="ecmd-editor-selector__video-frame" data-video-frame></div>
          </div>
        </div>

        <!-- Sibling promo (shown when user picks "Event Single Page" and
             Events Calendar Modules for Divi Pro is NOT installed). Driven by inline JS. -->
        <div class="ecmd-editor-promo" data-editor-promo hidden></div>

        <!-- Inline nav — no Skip on step 1; nav swaps to promo mode
             when Single Page mode is picked without Divi Modules Pro installed. -->
        <div class="ecmd-wizard-card__nav">
          <a href="#" role="button" class="ecmd-btn-ghost" data-wizard-back>
            <span class="dashicons dashicons-arrow-left-alt" aria-hidden="true"></span>
            <span>Back</span>
          </a>
          <div class="ecmd-wizard-card__nav-right" data-nav-right data-nav-mode="default">
            <!-- Default: single Continue button (Events Listing is free) -->
            <a href="#" role="button" class="ecmd-btn-disabled" data-wizard-next data-nav-variant="default" aria-disabled="true">
              <span data-wizard-next-label>Continue</span>
              <span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span>
            </a>

            <!-- Promo variant: Divi Modules Pro NOT installed.
                 Custom single event page templates in the Divi Builder are a
                 Pro-only feature of this same addon, so the CTA is "Get Pro"
                 (accent orange = the reserved "Pro / upsell" color). -->
            <a href="#" role="button" class="ecmd-btn-accent" data-nav-variant="promo-install" data-promo-install>
              <span class="dashicons dashicons-star-filled" aria-hidden="true"></span>
              <span>Get <span data-addon-name>Divi Modules</span> Pro</span>
            </a>

            <!-- Promo variant (paired with Get Pro): open the live demos
                 gallery in a new tab so users can see what Pro
                 unlocks before buying. Shown only in promo-suggest mode. -->
            <a href="https://eventscalendaraddons.com/demos/the-events-calendar-modules-for-divi/"
               role="button" class="ecmd-btn-secondary" data-nav-variant="promo-demos" target="_blank" rel="noopener" data-utm="demo">
              <span class="dashicons dashicons-external" aria-hidden="true"></span>
              <span>View Single Demos</span>
            </a>

            <!-- Promo variant: Divi Modules Pro INSTALLED — open its templates. -->
            <a href="#" role="button" class="ecmd-btn-primary" data-nav-variant="promo-open" data-promo-open>
              <span>Open <span data-addon-name>Divi Modules</span> setup</span>
              <span class="dashicons dashicons-arrow-right-alt" aria-hidden="true"></span>
            </a>

            <!-- Shared dismiss — switches selection back to "Events Listing"
                 (the free happy-path) and auto-advances. -->
            <a href="#" role="button" class="ecmd-btn-secondary" data-nav-variant="promo-dismiss" data-editor-promo-dismiss>
              <span data-editor-promo-dismiss-label>No, continue with Events Listing</span>
            </a>
          </div>
        </div>
      </section>

      <!-- ============================================================
           STEP 2 — Layout & Events Query (combined)
           Modules for Divi always creates a draft Divi page. Two rows:
             1. Layout           (3 cards: List free, Grid + Carousel Pro)
             2. Which events?     (Upcoming / Past / Both — all free)
           Then telemetry consent. Primary CTA is "Create Draft Page".
           Defaults ship `is-selected` in raw markup (list, upcoming) so
           seedSelectionsFromDom enables the CTA without a prior click.
           ============================================================ -->
      <section class="ecmd-wizard-step" data-step="layout-query">
        <header class="ecmd-wizard-card__heading">
          <h1 class="ecmd-wizard-card__title">Layout &amp; events query</h1>
          <p class="ecmd-wizard-card__desc">Pick a layout and choose which events show up in your Divi page. Sensible defaults are pre-selected &mdash; tweak as needed.</p>
        </header>

        <div class="ecmd-settings-list">

          <!-- LAYOUT ROW ------------------------------------------------ -->
          <div class="ecmd-settings-row" data-section="layout">
            <div class="ecmd-settings-row__info">
              <span class="ecmd-settings-row__icon" aria-hidden="true">
                <span class="dashicons dashicons-layout"></span>
              </span>
              <div class="ecmd-settings-row__meta">
                <h3 class="ecmd-settings-row__title">Layout</h3>
                <p class="ecmd-settings-row__desc">How events are arranged on the page.</p>
                <a class="ecmd-demo-link ecmd-settings-row__demo" href="https://eventscalendaraddons.com/demos/the-events-calendar-modules-for-divi/" target="_blank" rel="noopener" data-utm="demo">
                  <span class="dashicons dashicons-external" aria-hidden="true"></span>
                  <span>View all demos</span>
                </a>
              </div>
            </div>
            <div class="ecmd-settings-row__body">

              <!-- Divi module layouts (3: List free, Grid + Carousel Pro).
                   List ships is-selected as the free default. -->
              <div class="ecmd-layout-grid" data-required-selection="layout">
                <a href="#" role="button" class="ecmd-layout-card is-selected" data-value="list">
                  <span class="ecmd-layout-card__icon" aria-hidden="true"><span class="dashicons dashicons-list-view"></span></span>
                  <span class="ecmd-layout-card__name">List</span>
                  <span class="ecmd-layout-card__check" aria-hidden="true"><span class="dashicons dashicons-yes"></span></span>
                </a>
                <a href="#" role="button" class="ecmd-layout-card" data-value="grid" data-pro="true">
                  <span class="ecmd-layout-card__pro">Pro</span>
                  <span class="ecmd-layout-card__icon" aria-hidden="true"><span class="dashicons dashicons-grid-view"></span></span>
                  <span class="ecmd-layout-card__name">Grid</span>
                  <span class="ecmd-layout-card__check" aria-hidden="true"><span class="dashicons dashicons-yes"></span></span>
                </a>
                <a href="#" role="button" class="ecmd-layout-card" data-value="carousel" data-pro="true">
                  <span class="ecmd-layout-card__pro">Pro</span>
                  <span class="ecmd-layout-card__icon" aria-hidden="true"><span class="dashicons dashicons-images-alt2"></span></span>
                  <span class="ecmd-layout-card__name">Carousel</span>
                  <span class="ecmd-layout-card__check" aria-hidden="true"><span class="dashicons dashicons-yes"></span></span>
                </a>
              </div>

            </div>
          </div>

          <!-- WHICH EVENTS TO LOAD? — 3 cards, all free ------------------ -->
          <div class="ecmd-settings-row" data-section="time">
            <div class="ecmd-settings-row__info">
              <span class="ecmd-settings-row__icon" aria-hidden="true">
                <span class="dashicons dashicons-clock"></span>
              </span>
              <div class="ecmd-settings-row__meta">
                <h3 class="ecmd-settings-row__title">Which events to load?</h3>
                <p class="ecmd-settings-row__desc">Show only upcoming, past or all events.</p>
              </div>
            </div>
            <div class="ecmd-settings-row__body">
              <div class="ecmd-style-row ecmd-style-row--3col" data-required-selection="time">
                <a href="#" role="button" class="ecmd-style-card is-selected" data-value="upcoming">
                  <span class="ecmd-style-card__name">Upcoming</span>
                  <span class="ecmd-style-card__check" aria-hidden="true"><span class="dashicons dashicons-yes"></span></span>
                </a>
                <a href="#" role="button" class="ecmd-style-card" data-value="past">
                  <span class="ecmd-style-card__name">Past</span>
                  <span class="ecmd-style-card__check" aria-hidden="true"><span class="dashicons dashicons-yes"></span></span>
                </a>
                <a href="#" role="button" class="ecmd-style-card" data-value="both">
                  <span class="ecmd-style-card__name">Both</span>
                  <span class="ecmd-style-card__check" aria-hidden="true"><span class="dashicons dashicons-yes"></span></span>
                </a>
              </div>
            </div>
          </div>

        </div>

        <!-- PRO NOTICE — visible when any Pro option is selected.
             Copy swaps to Activate when Pro is installed but inactive (JS). -->
        <div class="ecmd-pro-notice" data-pro-notice hidden>
          <span class="ecmd-pro-notice__icon" aria-hidden="true">
            <span class="dashicons dashicons-star-filled"></span>
          </span>
          <div class="ecmd-pro-notice__body">
            <h4 class="ecmd-pro-notice__title">You've picked <span data-pro-count>1</span> Pro feature<span data-pro-plural>s</span></h4>
            <p class="ecmd-pro-notice__desc" data-pro-notice-desc data-desc-upgrade="Upgrade to Events Calendar Modules for Divi Pro to unlock everything you've selected. Or pick free options to keep going." data-desc-activate="You already have Events Calendar Modules for Divi Pro installed. Activate it to unlock everything you've selected. Or pick free options to keep going.">Upgrade to Events Calendar Modules for Divi Pro to unlock everything you've selected. Or pick free options to keep going.</p>
          </div>
        </div>

        <!-- Telemetry consent — sits above the nav so it's the last thing
             users see before creating the draft page. The container is a
             <div> (not a <label>) so only the checkbox area toggles state. -->
        <div class="ecmd-telemetry"<?php echo $show_telemetry ? '' : ' hidden'; ?>>
          <label class="ecmd-telemetry__checkbox-wrap">
            <input type="checkbox" class="ecmd-telemetry__checkbox" data-wizard-telemetry checked>
            <span class="ecmd-telemetry__mark" aria-hidden="true"><span class="dashicons dashicons-yes"></span></span>
          </label>
          <div class="ecmd-telemetry__body">
            <strong class="ecmd-telemetry__title">Help improve Events Calendar Addons</strong>
            <span class="ecmd-telemetry__desc">Share non-sensitive usage data &mdash; WP version, addon versions, active builder, theme name. No personal data or event content. Change anytime in Settings. <a class="ecmd-telemetry__policy" href="https://my.coolplugins.net/terms/usage-tracking/" target="_blank" rel="noopener" data-utm="policy">View policy<span class="dashicons dashicons-external" aria-hidden="true"></span></a></span>
          </div>
        </div>

        <!-- NAV — Modules for Divi always creates a draft Divi page, so the
             primary CTA is fixed as "Create Draft Page" with a small hint.
             Swaps to Upgrade to Pro when a Pro layout (Grid/Carousel) is picked. -->
        <div class="ecmd-wizard-card__nav">
          <a href="#" role="button" class="ecmd-btn-ghost" data-wizard-back>
            <span class="dashicons dashicons-arrow-left-alt" aria-hidden="true"></span>
            <span>Back</span>
          </a>
          <div class="ecmd-wizard-card__nav-right" data-step2-nav-right data-nav-mode="default">
            <span class="ecmd-wizard-card__nav-hint">Insert Events module in a Divi page</span>
            <a href="#" role="button" class="ecmd-btn-primary" data-wizard-next data-step2-nav-variant="default" data-step-primary-cta>
              <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
              <span>Create Draft Page</span>
            </a>
            <a href="https://eventscalendaraddons.com/plugin/the-events-calendar-modules-for-divi/" role="button" class="ecmd-btn-accent" data-step2-nav-variant="upgrade" data-upgrade-href="https://eventscalendaraddons.com/plugin/the-events-calendar-modules-for-divi/" target="_blank" rel="noopener">
              <span class="dashicons dashicons-star-filled" aria-hidden="true"></span>
              <span>Upgrade to Pro</span>
            </a>
          </div>
        </div>
      </section>

      <!-- ============================================================
           STEP 3 — Done
           Modules for Divi always creates a draft Divi page, so this step
           opens in the post-create state directly.
           ============================================================ -->
      <section class="ecmd-wizard-step" data-step="success" data-always-valid="true">
        <div class="ecmd-wizard-success">
          <span class="ecmd-wizard-success__icon ecmd-wizard-success__icon--lg" aria-hidden="true">
            <span class="dashicons dashicons-yes"></span>
          </span>
          <h2 class="ecmd-wizard-success__title" data-success-title>All done!</h2>
          <p class="ecmd-wizard-success__lede" data-success-lede>Your Divi page with the Events module is ready to preview.</p>
        </div>

        <div class="ecmd-review-recap">

          <!-- Builder recap — Divi. Logo + one-liner. -->
          <div class="ecmd-review-panel ecmd-review-panel--builder" data-review-panel="builder-recap">
            <div class="ecmd-review-builder">
              <img class="ecmd-review-builder__logo" data-review-builder-logo src="<?php echo esc_url( $divi_icon ); ?>" alt="">
              <div>
                <strong class="ecmd-review-builder__title" data-review-builder-title>Events module inserted in Divi page</strong>
                <p class="ecmd-review-builder__desc" data-review-builder-desc>Preview or open the draft to fine-tune anything in the Divi Builder.</p>
              </div>
            </div>
          </div>

        </div>

        <!-- Success actions — a draft Divi page is always created, so only
             the post-create buttons + Finish are shown. -->
        <div class="ecmd-wizard-success__actions">

          <a href="#preview-draft" target="_blank" rel="noopener" class="ecmd-btn-primary">
            <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
            <span>Preview Page</span>
          </a>
          <a href="#edit-draft" target="_blank" rel="noopener" class="ecmd-btn-secondary">
            <span class="dashicons dashicons-edit" aria-hidden="true"></span>
            <span>Edit Page in Divi</span>
          </a>

          <a href="<?php echo esc_url( $dashboard_url ); ?>" data-wizard-finish class="ecmd-btn-success">
            <span>Finish</span>
            <span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
          </a>
        </div>

        <!-- Bundle promo — every free wizard's final step promotes the
             Events Calendar Addons bundle instead of per-sibling cross-sells,
             so users see one consolidated offer. Links to /pricing/ with an
             explicit data-utm="get_bundle" campaign so bundle conversions are
             tracked separately from single-plugin Pro upgrades. -->
        <div class="ecmd-cross-sell ecmd-cross-sell--bundle" data-cross-sell="bundle">
          <span class="ecmd-cross-sell__icon ecmd-cross-sell__icon--chip" aria-hidden="true">
            <span class="dashicons dashicons-awards"></span>
          </span>
          <div class="ecmd-cross-sell__body">
            <strong class="ecmd-cross-sell__title">The Events Calendar Addons Bundle</strong>
            <span class="ecmd-cross-sell__desc">Get events calendar addons discounted bundle for your event website.</span>
          </div>
          <div class="ecmd-cross-sell__actions">
            <a href="https://eventscalendaraddons.com/pricing/" target="_blank" rel="noopener" class="ecmd-btn-accent" data-utm="get_bundle">
              <span class="dashicons dashicons-star-filled" aria-hidden="true"></span>
              <span>View Addons Bundle</span>
            </a>
            <a href="<?php echo esc_url( $dashboard_url ); ?>" data-wizard-finish class="ecmd-btn-ghost">Not now</a>
          </div>
        </div>
      </section>

    </div>
  </main>

</div>
</div>
