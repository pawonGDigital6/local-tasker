(function(){
  const GST_RATE = 0.10;
  const rowsEl = document.getElementById("rows");
  const rowTemplate = document.getElementById("rowTemplate");
  const addRowBtn = document.getElementById("addRowBtn");
  const quoteJsonInput = document.getElementById("quoteJson");
  const state = { lines: [] };

  const unique = arr => [...new Set(arr.filter(Boolean))];
  const money = value => "$" + Number(value || 0).toLocaleString("en-AU", {minimumFractionDigits:2, maximumFractionDigits:2});
  const slug = value => String(value || "").toUpperCase().replace(/[^A-Z0-9]+/g, "-").replace(/^-|-$/g, "");

  function createSearchSelect(container, placeholder, onSelect){
    const input = document.createElement("input");
    input.type = "text";
    input.placeholder = placeholder;
    input.autocomplete = "off";
    const panel = document.createElement("div");
    panel.className = "option-panel";
    container.innerHTML = "";
    container.appendChild(input);
    container.appendChild(panel);
    let options = [];
    let selected = "";

    function render(){
      const query = input.value.toLowerCase().trim();
      panel.innerHTML = "";
      const filtered = options.filter(opt => String(opt).toLowerCase().includes(query));
      if(!filtered.length){
        const empty = document.createElement("div");
        empty.className = "empty-option";
        empty.textContent = "No matches";
        panel.appendChild(empty);
        return;
      }
      filtered.forEach(opt => {
        const div = document.createElement("div");
        div.className = "option";
        div.textContent = opt;
        div.addEventListener("mousedown", e => {
          e.preventDefault();
          selected = opt;
          input.value = opt;
          panel.classList.remove("open");
          onSelect(opt);
        });
        panel.appendChild(div);
      });
    }

    input.addEventListener("focus", () => { render(); panel.classList.add("open"); });
    input.addEventListener("input", () => { selected = ""; render(); panel.classList.add("open"); });
    input.addEventListener("blur", () => {
      setTimeout(() => {
        panel.classList.remove("open");
        if(input.value && !options.includes(input.value)) input.value = selected || "";
      }, 120);
    });

    return {
      setOptions(newOptions){
        options = unique(newOptions).sort((a,b)=>String(a).localeCompare(String(b), undefined, {numeric:true}));
        selected = "";
        input.value = "";
        render();
      },
      setDisabled(disabled){
        input.disabled = disabled;
        container.classList.toggle("disabled", disabled);
      },
      clear(){ selected = ""; input.value = ""; },
      setPlaceholder(text){ input.placeholder = text; },
      setValue(value){ selected = value || ""; input.value = value || ""; }
    };
  }

  function getMatchingItem(line){
    return PRODUCT_DATA.find(item =>
      item.category === line.category &&
      item.product === line.product &&
      item.size === line.size
    );
  }

  function createRow(){
    const node = rowTemplate.content.firstElementChild.cloneNode(true);
    rowsEl.appendChild(node);

    const line = { category:"", product:"", size:"", length:"", unit:"", unitPrice:0, quantity:0, subtotal:0, sku:"", pricingMode:"", node };
    state.lines.push(line);

    const categoryEl = node.querySelector('[data-field="category"]');
    const productEl = node.querySelector('[data-field="product"]');
    const sizeEl = node.querySelector('[data-field="size"]');
    const lengthEl = node.querySelector('[data-field="length"]');
    const unitInput = node.querySelector(".unit-field");
    const qtyInput = node.querySelector(".quantity-field");
    const subtotalInput = node.querySelector(".subtotal-field");
    const removeBtn = node.querySelector(".remove-row");
    const skuHidden = node.querySelector(".sku-hidden");
    const priceHidden = node.querySelector(".price-hidden");
    const lineJsonHidden = node.querySelector(".line-json-hidden");

    const categorySelect = createSearchSelect(categoryEl, "Search category", value => {
      line.category = value;
      line.product = line.size = line.length = line.unit = "";
      line.unitPrice = line.subtotal = 0;
      productSelect.setOptions(PRODUCT_DATA.filter(x => x.category === value).map(x => x.product));
      productSelect.setDisabled(false);
      sizeSelect.setOptions([]);
      sizeSelect.setDisabled(true);
      lengthSelect.setOptions([]);
      lengthSelect.setDisabled(true);
      lengthSelect.setPlaceholder("Not required");
      unitInput.value = "";
      subtotalInput.value = money(0);
      syncLine();
    });

    const productSelect = createSearchSelect(productEl, "Search product", value => {
      line.product = value;
      line.size = line.length = line.unit = "";
      line.unitPrice = line.subtotal = 0;
      sizeSelect.setOptions(PRODUCT_DATA.filter(x => x.category === line.category && x.product === value).map(x => x.size));
      sizeSelect.setDisabled(false);
      lengthSelect.setOptions([]);
      lengthSelect.setDisabled(true);
      lengthSelect.setPlaceholder("Not required");
      unitInput.value = "";
      subtotalInput.value = money(0);
      syncLine();
    });

    const sizeSelect = createSearchSelect(sizeEl, "Search size", value => {
      line.size = value;
      line.length = "";
      const item = getMatchingItem(line);
      line.unit = item ? item.unit : "";
      line.pricingMode = item ? item.pricingMode : "";
      unitInput.value = line.unit;

      const availableLengths = item ? Object.keys(item.lengthPrices || {}) : [];

      // Business rule:
      // lm items without length pricing have already been excluded from PRODUCT_DATA.
      // Non-lm items with no length pricing are single-unit products and do not require length.
      if(availableLengths.length){
        lengthSelect.setOptions(availableLengths);
        lengthSelect.setDisabled(false);
        lengthSelect.setPlaceholder("Search length");
        lengthSelect.clear();
        line.unitPrice = 0;
      }else{
        lengthSelect.setOptions([]);
        lengthSelect.setValue("N/A");
        lengthSelect.setDisabled(true);
        lengthSelect.setPlaceholder("Not required");
        line.length = "N/A";
        line.unitPrice = item && item.salePricePerUnit != null ? Number(item.salePricePerUnit) : 0;
      }

      calculateLine();
    });

    const lengthSelect = createSearchSelect(lengthEl, "Search length", value => {
      line.length = value;
      calculateLine();
    });

    categorySelect.setOptions(PRODUCT_DATA.map(x => x.category));
    productSelect.setDisabled(true);
    sizeSelect.setDisabled(true);
    lengthSelect.setDisabled(true);
    lengthSelect.setPlaceholder("Not required");

    qtyInput.addEventListener("input", () => {
      qtyInput.value = qtyInput.value.replace(/\D/g, "");
      line.quantity = parseInt(qtyInput.value || "0", 10);
      calculateLine();
    });

    removeBtn.addEventListener("click", () => {
      if(state.lines.length === 1){
        categorySelect.clear(); productSelect.clear(); sizeSelect.clear(); lengthSelect.clear();
        qtyInput.value = "";
        Object.assign(line, {category:"", product:"", size:"", length:"", unit:"", sku:"", pricingMode:"", unitPrice:0, quantity:0, subtotal:0});
        unitInput.value = "";
        subtotalInput.value = money(0);
        syncLine();
        return;
      }
      state.lines = state.lines.filter(x => x !== line);
      node.remove();
      updateTotals();
    });

    function calculateLine(){
      const item = getMatchingItem(line);
      if(!item){
        line.unitPrice = 0;
        line.subtotal = 0;
        syncLine();
        return;
      }

      const availableLengths = Object.keys(item.lengthPrices || {});
      if(availableLengths.length){
        if(line.length && item.lengthPrices[line.length] !== undefined){
          line.unitPrice = Number(item.lengthPrices[line.length]);
        }else{
          line.unitPrice = 0;
        }
      }else{
        line.length = "N/A";
        line.unitPrice = item.salePricePerUnit != null ? Number(item.salePricePerUnit) : 0;
      }

      line.quantity = parseInt(qtyInput.value || "0", 10);
      line.subtotal = line.quantity > 0 ? line.quantity * line.unitPrice : 0;
      line.sku = [item.skuBase, line.length && line.length !== "N/A" ? slug(line.length + "M") : ""].filter(Boolean).join("-");
      syncLine();
    }

    function syncLine(){
      subtotalInput.value = money(line.subtotal);
      skuHidden.value = line.sku || "";
      priceHidden.value = line.unitPrice || 0;
      lineJsonHidden.value = JSON.stringify(sanitiseLine(line));
      updateTotals();
    }
  }

  function sanitiseLine(line){
    return {
      category: line.category,
      product: line.product,
      size: line.size,
      length: line.length,
      unit: line.unit,
      pricingMode: line.pricingMode,
      sku: line.sku,
      unitPrice: line.unitPrice,
      quantity: line.quantity,
      subtotal: line.subtotal
    };
  }

  // A length-required product with no length chosen has a unit price of 0.
  // Such a row must never reach the quote, or the customer is emailed a $0.00
  // line item. Rows without length options carry the sentinel "N/A".
  function isQuotable(line){
    return Boolean(line.category && line.product && line.size && line.length && line.quantity > 0 && line.unitPrice > 0);
  }

  function getQuote(){
    const lines = state.lines.map(sanitiseLine).filter(isQuotable);
    const exGST = lines.reduce((sum,line)=>sum + Number(line.subtotal || 0), 0);
    const gst = exGST * GST_RATE;
    const incGST = exGST + gst;
    return {
      createdAt: new Date().toLocaleString("en-AU"),
      customer: {
        name: document.getElementById("customerName").value,
        email: document.getElementById("customerEmail").value,
        phone: document.getElementById("customerPhone").value,
        projectSuburb: document.getElementById("projectSuburb").value,
        notes: document.getElementById("quoteNotes").value
      },
      lines,
      totals: { exGST, gst, incGST }
    };
  }

  function updateTotals(){
    const quote = getQuote();
    document.getElementById("grandExGST").textContent = money(quote.totals.exGST);
    document.getElementById("gstAmount").textContent = money(quote.totals.gst);
    document.getElementById("grandIncGST").textContent = money(quote.totals.incGST);
    quoteJsonInput.value = JSON.stringify(quote);
  }

  document.getElementById("printQuoteBtn").addEventListener("click", () => {
    updateTotals();
    window.print();
  });

  /* ---------------------------------------------------------------------- */
  /* Submission                                                             */
  /* ---------------------------------------------------------------------- */

  // This document is served statically by Apache, so WordPress cannot localise
  // it. footer.php appends the admin-ajax URL to the iframe src; the fallback
  // derives it from our own path so the calculator still works if opened
  // directly, including on sub-directory installs.
  function getAjaxUrl(){
    const fromQuery = new URLSearchParams(window.location.search).get("ajaxUrl");
    if(fromQuery) return fromQuery;

    const marker = window.location.pathname.indexOf("/wp-content/");
    if(marker !== -1) return window.location.origin + window.location.pathname.slice(0, marker) + "/wp-admin/admin-ajax.php";

    return window.location.origin + "/wp-admin/admin-ajax.php";
  }

  const AJAX_URL = getAjaxUrl();
  const RECAPTCHA_KEY = new URLSearchParams(window.location.search).get("recaptchaKey") || "";
  const RECAPTCHA_ACTION = "lt_quote";
  const emailBtn = document.getElementById("emailQuoteBtn");
  const statusEl = document.getElementById("quoteStatus");
  const emailBtnLabel = emailBtn.textContent;

  /* ---------------------------------------------------------------------- */
  /* reCAPTCHA v3                                                           */
  /* ---------------------------------------------------------------------- */

  // The site key only reaches us when WordPress has reCAPTCHA configured, so
  // everything below is a no-op on environments without keys (local, staging).
  let recaptchaReady = null;

  function loadRecaptcha(){
    if(!RECAPTCHA_KEY) return null;
    if(recaptchaReady) return recaptchaReady;

    document.getElementById("recaptchaNotice").hidden = false;

    recaptchaReady = new Promise((resolve, reject) => {
      const script = document.createElement("script");
      script.src = `https://www.google.com/recaptcha/api.js?render=${encodeURIComponent(RECAPTCHA_KEY)}`;
      script.async = true;
      script.onload = () => window.grecaptcha ? window.grecaptcha.ready(resolve) : reject(new Error("grecaptcha missing"));
      script.onerror = () => reject(new Error("reCAPTCHA failed to load"));
      document.head.appendChild(script);
    });

    return recaptchaReady;
  }

  // Returns a token, or "" when reCAPTCHA is switched off or unreachable. The
  // server decides what to do with an empty token; the form is never blocked
  // client-side by a Google outage.
  async function getRecaptchaToken(){
    const ready = loadRecaptcha();
    if(!ready) return "";

    try {
      await ready;
      return await window.grecaptcha.execute(RECAPTCHA_KEY, { action: RECAPTCHA_ACTION });
    } catch (error) {
      return "";
    }
  }

  // Warm the script up as soon as the popup is opened so the visitor is not
  // waiting on a cold network fetch when they click Email Quote.
  loadRecaptcha();

  // The Email Quote button sits at the top of the form but the status line
  // renders below the totals, which can be outside the iframe's viewport. Bring
  // it into view so the visitor always sees the outcome of their click. Skipped
  // when a field was focused instead, because that already scrolls.
  function setStatus(message, tone, scrollIntoView){
    statusEl.textContent = message || "";
    statusEl.classList.remove("is-error", "is-success");
    if(tone) statusEl.classList.add(tone === "error" ? "is-error" : "is-success");
    if(message && scrollIntoView !== false && statusEl.scrollIntoView){
      statusEl.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  }

  function markInvalid(el, invalid){
    if(el) el.classList.toggle("has-error", Boolean(invalid));
  }

  // Mirrors the server-side rules in LT_Quote_Calculator_Ajax::validate() so the
  // visitor gets an instant answer, but the server remains the authority.
  function validateQuote(){
    const nameEl = document.getElementById("customerName");
    const emailEl = document.getElementById("customerEmail");
    const phoneEl = document.getElementById("customerPhone");
    const name = nameEl.value.trim();
    const email = emailEl.value.trim();
    const phone = phoneEl.value.trim();

    markInvalid(nameEl, false);
    markInvalid(emailEl, false);
    markInvalid(phoneEl, false);

    if(!name){
      markInvalid(nameEl, true);
      nameEl.focus();
      return "Please enter your name.";
    }

    if(!email || !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)){
      markInvalid(emailEl, true);
      emailEl.focus();
      return "Please enter a valid email address.";
    }

    // Digits only, so "04 1234 5678" and "+61 412 345 678" both pass.
    if(phone.replace(/\D/g, "").length < 8){
      markInvalid(phoneEl, true);
      phoneEl.focus();
      return phone ? "That phone number does not look right." : "Please enter your phone number.";
    }

    // A row the visitor started but did not finish should be corrected rather
    // than silently dropped from the quote.
    for(let i = 0; i < state.lines.length; i++){
      const line = state.lines[i];
      const started = line.category || line.product || line.size || line.quantity > 0;
      if(started && !isQuotable(line)){
        focusFirstGap(line);
        return `Row ${i + 1} is incomplete. Choose a category, product, size, length and quantity, or remove the row.`;
      }
    }

    if(!getQuote().lines.length){
      focusFirstGap(state.lines[0]);
      return "Please add at least one product to your quote.";
    }

    return "";
  }

  // Put the cursor on the first thing the row is missing, so the visitor lands
  // where the fix is rather than hunting for it.
  function focusFirstGap(line){
    if(!line) return;

    const gap = ["category", "product", "size", "length"].find(field => !line[field]);
    const target = gap
      ? line.node.querySelector(`[data-field="${gap}"] input`)
      : line.node.querySelector(".quantity-field");

    if(target && !target.disabled) target.focus();
  }

  async function postForm(action, fields){
    const body = new URLSearchParams();
    body.set("action", action);
    Object.keys(fields).forEach(key => body.set(key, fields[key]));

    const response = await fetch(AJAX_URL, {
      method: "POST",
      credentials: "same-origin",
      headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
      body: body.toString()
    });

    // admin-ajax returns 4xx/5xx with a JSON body for handled failures, so parse
    // before deciding: response.ok alone would hide the real message.
    const payload = await response.json().catch(() => null);
    if(!payload) throw new Error("Unexpected response from the server.");

    return payload;
  }

  async function sendQuote(){
    const problem = validateQuote();
    if(problem){
      // validateQuote() has already focused the offending field.
      setStatus(problem, "error", false);
      return;
    }

    emailBtn.disabled = true;
    emailBtn.textContent = "Sending...";
    setStatus("Sending your quote...", null, false);

    try {
      const nonceResponse = await postForm("lt_quote_nonce", {});
      const nonce = nonceResponse && nonceResponse.data ? nonceResponse.data.nonce : "";
      if(!nonce) throw new Error("Could not start a secure session.");

      const result = await postForm("lt_send_quote", {
        nonce,
        quote: JSON.stringify(getQuote()),
        company_website: document.getElementById("companyWebsite").value,
        recaptcha_token: await getRecaptchaToken()
      });

      if(result.success){
        setStatus(result.data && result.data.message ? result.data.message : "Thanks - your quote request has been sent.", "success");
        return;
      }

      setStatus(result.data && result.data.message ? result.data.message : "We could not send your quote. Please try again.", "error");
    } catch (error) {
      setStatus("We could not reach the server. Please check your connection and try again, or email contact@localtasker.com.au.", "error");
    } finally {
      emailBtn.disabled = false;
      emailBtn.textContent = emailBtnLabel;
    }
  }

  emailBtn.addEventListener("click", sendQuote);

  addRowBtn.addEventListener("click", createRow);
  createRow();
})();
