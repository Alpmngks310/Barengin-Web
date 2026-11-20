(() => {
  'use strict';
  const $ = (selector, scope = document) => scope.querySelector(selector);
  const $$ = (selector, scope = document) => [...scope.querySelectorAll(selector)];

  function toast(message) {
    const stack = $('#toastStack');
    if (!stack) return;
    const node = document.createElement('div');
    node.className = 'toast-message';
    node.textContent = message;
    stack.appendChild(node);
    window.setTimeout(() => node.remove(), 3500);
  }

  $$('[data-toast]').forEach((element) => element.addEventListener('click', () => toast(element.dataset.toast || '')));
  $$('[data-toast-link]').forEach((element) => element.addEventListener('click', (event) => { event.preventDefault(); toast(element.dataset.toastLink || ''); }));

  $$('[data-toggle-password]').forEach((button) => button.addEventListener('click', () => {
    const input = document.getElementById(button.dataset.togglePassword);
    if (!input) return;
    input.type = input.type === 'password' ? 'text' : 'password';
  }));

  const activityTabs = $$('[data-activity-tab]');
  activityTabs.forEach((tab) => tab.addEventListener('click', () => {
    activityTabs.forEach((item) => item.classList.toggle('active', item === tab));
    $$('[data-activity-panel]').forEach((panel) => panel.classList.toggle('active', panel.dataset.activityPanel === tab.dataset.activityTab));
  }));


  const reportType = $('#reportTarget');
  const reportTargetId = $('[data-report-target-id]');
  const syncReportTarget = () => {
    if (!reportTargetId || !reportType) return;
    reportTargetId.name = reportType.value === 'user' ? 'reported_user_id' : 'activity_id';
  };
  if (reportType) { reportType.addEventListener('change', syncReportTarget); syncReportTarget(); }

  const revealItems = $$('[data-reveal]');
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => entries.forEach((entry) => {
      if (entry.isIntersecting) { entry.target.classList.add('revealed'); observer.unobserve(entry.target); }
    }), { threshold: 0.12 });
    revealItems.forEach((item) => observer.observe(item));
  } else revealItems.forEach((item) => item.classList.add('revealed'));

  const interestForm = $('.interest-form');
  if (interestForm) interestForm.addEventListener('submit', (event) => {
    const selected = $$('input[name="category_ids[]"]:checked', interestForm).length;
    if (selected < 3 || selected > 6) {
      event.preventDefault();
      toast('Pilih minimal 3 dan maksimal 6 kategori minat.');
    }
  });

  const registerForm = $('#registerForm');
  if (registerForm) registerForm.addEventListener('submit', (event) => {
    const password = $('#registerPassword')?.value || '';
    const confirm = $('#registerConfirm')?.value || '';
    if (password !== confirm) {
      event.preventDefault();
      toast('Konfirmasi password belum sama.');
      $('#registerConfirm')?.focus();
    }
  });
})();
