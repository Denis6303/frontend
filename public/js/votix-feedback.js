/**
 * Votix — modale de feedback (succès / erreur / info / avertissement).
 * @global VotixFeedback.show({ type, message, title? })
 */
(function (window, document) {
  'use strict';

  var TYPE_META = {
    success: { icon: 'fa-check', bg: '#e6f4ea', color: '#1a7f37' },
    error: { icon: 'fa-circle-xmark', bg: '#fdecea', color: '#c5221f' },
    info: { icon: 'fa-circle-info', bg: '#e8f0fe', color: '#1a73e8' },
    warning: { icon: 'fa-triangle-exclamation', bg: '#fef7e0', color: '#b06000' }
  };

  function applyContent(modalEl, opts) {
    var type = opts.type && TYPE_META[opts.type] ? opts.type : 'info';
    var meta = TYPE_META[type];
    var wrap = modalEl.querySelector('[data-votix-role="icon-wrap"]');
    var icon = modalEl.querySelector('[data-votix-role="icon"]');
    var titleEl = modalEl.querySelector('[data-votix-role="title"]');
    var msgEl = modalEl.querySelector('[data-votix-role="message"]');
    if (wrap) {
      wrap.style.background = meta.bg;
      wrap.style.color = meta.color;
    }
    if (icon) {
      icon.className = 'fa-solid ' + meta.icon;
    }
    if (titleEl) {
      titleEl.textContent = opts.title != null ? String(opts.title) : '';
    }
    if (msgEl) {
      msgEl.textContent = opts.message != null ? String(opts.message) : '';
    }
  }

  function show(opts) {
    if (!opts || typeof opts !== 'object') {
      return;
    }
    var modalEl = document.getElementById('votixFeedbackModal');
    if (!modalEl || !window.bootstrap || !window.bootstrap.Modal) {
      return;
    }
    applyContent(modalEl, opts);
    window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
  }

  function initFromDom() {
    var el = document.getElementById('votix-feedback-flash');
    if (!el) {
      return;
    }
    try {
      var raw = el.textContent.trim();
      if (raw) {
        show(JSON.parse(raw));
      }
    } catch (e) {
      /* ignore malformed flash */
    }
    el.remove();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFromDom);
  } else {
    initFromDom();
  }

  window.VotixFeedback = { show: show };
})(window, document);
