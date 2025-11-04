/**
 * Global prototype enhancements can live here.
 * For now we enable Bootstrap tooltips if present.
 */
$(document).ready(() => {
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.forEach((tooltipTriggerEl) => {
    new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
