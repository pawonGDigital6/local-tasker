LVL & MGP10 PRODUCTION CALCULATOR — STOCK LOGIC UPDATED

Generated from:
LVL_MGP10_PricingAndLengthOptions.xlsx

Detected worksheet:
Sheet1

Detected header row:
2

Records included in calculator:
96

Excluded lm rows with no valid length pricing:
5

Non-lm rows using Price per Unit because no length pricing exists:
49

Business rules implemented:
1. Unit = lm + blank/no length price = not stocked.
2. lm items with no valid length prices are excluded from the calculator entirely.
3. Non-lm items with no valid length prices remain available.
4. Non-lm items without length pricing calculate from Price per Unit.
5. Blank, #N/A and non-numeric length cells are never shown as length options.

Email Quote (WordPress integration):
The original standalone build opened a mailto: link, which never reached the
business and depended on the visitor having a desktop mail client. It now posts
to WordPress instead:

  Endpoint  wp-admin/admin-ajax.php
  Actions   lt_quote_nonce, lt_send_quote
  Handler   inc/class-quote-calculator-ajax.php
  Embedded  footer.php (iframe, with the admin-ajax URL on the query string)

Who receives the quote (first match wins):
  1. LT_QUOTE_RECIPIENT in wp-config.php. Per environment, never shipped in the
     theme, so staging and live can differ. Comma separate for several people:
       define( 'LT_QUOTE_RECIPIENT', 'you@agency.com, sales@client.com' );
  2. Theme Option > Contact Settings > Email  (ACF field cm_email)
  3. The WordPress admin email.

The customer is also sent a copy of their own quote. To turn that off:

  add_filter( 'lt_quote_send_customer_copy', '__return_false' );

reCAPTCHA v3 is automatic. The keys are read from Contact Form 7
(Contact > Integration > reCAPTCHA), so there is nothing extra to configure. If
no keys are set, reCAPTCHA is skipped entirely and the form still works - that
is what keeps local and staging usable. Optional overrides:

  define( 'LT_QUOTE_RECAPTCHA_SITEKEY', '...' );   // ignore CF7's keys
  define( 'LT_QUOTE_RECAPTCHA_SECRET',  '...' );
  add_filter( 'lt_quote_recaptcha_threshold', fn() => 0.3 );  // default 0.5

Remember to add every domain (live, staging, and localhost if you test there)
to the reCAPTCHA admin console, or Google returns a browser error and the
submission is refused.

Prices are re-derived server-side from data.js, so editing data.js updates both
the on-screen calculator and the emailed quote. Keep the file's shape intact:
a single JS assignment wrapping a JSON array.
