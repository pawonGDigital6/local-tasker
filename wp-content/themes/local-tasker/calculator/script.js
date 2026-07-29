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

  function getQuote(){
    const lines = state.lines.map(sanitiseLine).filter(line => line.category && line.product && line.size && line.quantity > 0);
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

  function buildQuoteSummary(quote){
    const lines = quote.lines.map((line,idx)=>
      `${idx+1}. ${line.category} | ${line.product} | ${line.size} | Length: ${line.length || "N/A"} | Unit: ${line.unit} | Qty: ${line.quantity} | Price: ${money(line.unitPrice)} | Subtotal: ${money(line.subtotal)} | SKU: ${line.sku}`
    ).join("\n");
    return [
      "Quote request", "",
      `Created: ${quote.createdAt}`,
      `Name: ${quote.customer.name || ""}`,
      `Email: ${quote.customer.email || ""}`,
      `Phone: ${quote.customer.phone || ""}`,
      `Project suburb: ${quote.customer.projectSuburb || ""}`,
      "", "Products:", lines || "No products selected.", "",
      `Grand Total excluding GST: ${money(quote.totals.exGST)}`,
      `GST: ${money(quote.totals.gst)}`,
      `Grand Total including GST: ${money(quote.totals.incGST)}`,
      "", `Notes: ${quote.customer.notes || ""}`
    ].join("\n");
  }

  document.getElementById("printQuoteBtn").addEventListener("click", () => {
    updateTotals();
    window.print();
  });

  document.getElementById("emailQuoteBtn").addEventListener("click", () => {
    const quote = getQuote();
    const subject = encodeURIComponent("LVL & MGP10 Quote Request");
    const body = encodeURIComponent(buildQuoteSummary(quote));
    window.location.href = `mailto:sales@example.com?subject=${subject}&body=${body}`;
  });

  addRowBtn.addEventListener("click", createRow);
  createRow();
})();
