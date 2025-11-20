document.addEventListener('DOMContentLoaded', () => {
  if (window.bootstrap && typeof bootstrap.Tooltip === 'function') {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((trigger) => {
      new bootstrap.Tooltip(trigger);
    });
  }

  const currencySelect = document.querySelector('[data-currency-switcher]');
  if (currencySelect) {
    currencySelect.addEventListener('change', () => {
      currencySelect.form?.submit();
    });
  }

  document.querySelectorAll('[data-carbon-calculator]').forEach((calculator) => {
    const perUnit = parseFloat(calculator.dataset.carbonPerUnit || '0');
    if (!perUnit || Number.isNaN(perUnit)) {
      return;
    }

    const quantityInput = calculator.querySelector('[data-carbon-qty]');
    const totalOutput = calculator.querySelector('[data-carbon-total]');
    const treesOutput = calculator.querySelector('[data-carbon-trees]');
    const commuteOutput = calculator.querySelector('[data-carbon-commute]');

    if (!quantityInput || !totalOutput || !treesOutput || !commuteOutput) {
      return;
    }

    const updateTotals = () => {
      const qty = Math.max(parseInt(quantityInput.value, 10) || 1, 1);
      const total = perUnit * qty;
      totalOutput.textContent = total.toFixed(2);
      treesOutput.textContent = (total / 0.059).toFixed(1);
      commuteOutput.textContent = (total / 0.192).toFixed(1);
    };

    quantityInput.addEventListener('input', updateTotals);
    updateTotals();
  });
});
