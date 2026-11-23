/* Hiiumaa Kino — website refresh: menu, hero refresh, programme filters, relative day labels. */
(function () {
  'use strict';

  var WD = ['P', 'E', 'T', 'K', 'N', 'R', 'L'];
  var WD_LONG = ['Pühapäev', 'Esmaspäev', 'Teisipäev', 'Kolmapäev', 'Neljapäev', 'Reede', 'Laupäev'];
  var MON = ['jaan', 'veebr', 'märts', 'apr', 'mai', 'juuni', 'juuli', 'aug', 'sept', 'okt', 'nov', 'dets'];
  var pad = function (n) { return String(n).padStart(2, '0'); };
  var ymd = function (d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); };
  var parseLocal = function (s) { // "2026-11-17T19:00" in the cinema's local time
    var m = s.split(/[-T:]/).map(Number);
    return new Date(m[0], m[1] - 1, m[2], m[3] || 0, m[4] || 0);
  };
  var now = new Date();
  var today = ymd(now);
  var tomorrow = ymd(new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1));
  var relDay = function (key) { return key === today ? 'Täna' : key === tomorrow ? 'Homme' : null; };

  /* ---- Mobile menu ---- */
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.getElementById('primary-menu');
  if (toggle && nav) {
    var label = toggle.querySelector('.nav-toggle__label');
    var setOpen = function (open) {
      toggle.setAttribute('aria-expanded', String(open));
      nav.classList.toggle('is-open', open);
      if (label) label.textContent = open ? 'Sulge' : 'Menüü';
    };
    toggle.addEventListener('click', function () { setOpen(toggle.getAttribute('aria-expanded') !== 'true'); });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') { setOpen(false); toggle.focus(); }
    });
    document.addEventListener('click', function (e) {
      if (!nav.contains(e.target) && !toggle.contains(e.target)) setOpen(false);
    });
  }

  /* ---- Relative day labels (Täna / Homme) in the visitor's clock ---- */
  function labelDays(root) {
    (root || document).querySelectorAll('[data-rel-day]').forEach(function (el) {
      var key = el.getAttribute('data-rel-day');
      var d = parseLocal(key);
      var rel = relDay(key);
      var short = el.getAttribute('data-rel-style') === 'short';
      el.textContent = rel || (short ? WD[d.getDay()] : WD[d.getDay()] + ' ' + d.getDate() + '.' + pad(d.getMonth() + 1));
      el.classList.toggle('is-soon', !!rel);
    });
  }

  /* ---- Screenings data, read from the programme markup ---- */
  var showEls = Array.prototype.slice.call(document.querySelectorAll('.showtime'));
  var screenings = showEls.map(function (li) {
    var film = li.closest('.film');
    return {
      el: li, film: film, start: parseLocal(li.dataset.start), key: li.dataset.day,
      href: li.querySelector('a').href, venue: li.querySelector('.showtime__venue').textContent.trim()
    };
  }).sort(function (a, b) { return a.start - b.start; });

  // Screenings that have already started are dropped from the list.
  screenings.forEach(function (s) { if (s.start < now) { s.el.remove(); s.past = true; } });
  screenings = screenings.filter(function (s) { return !s.past; });
  document.querySelectorAll('.films > .film').forEach(function (f) { if (!f.querySelector('.showtime')) f.remove(); });

  /* ---- Hero: always the next screening that hasn't started ---- */
  var hero = document.querySelector('.hero');
  if (hero && screenings.length && parseLocal(hero.dataset.heroStart) < now) {
    var n = screenings[0], f = n.film.dataset, rel = relDay(n.key);
    var when = (rel || WD_LONG[n.start.getDay()] + ', ' + n.start.getDate() + '. ' + MON[n.start.getMonth()]) +
      ' kell ' + pad(n.start.getHours()) + ':' + pad(n.start.getMinutes());
    hero.querySelector('.hero__title a').textContent = f.title;
    hero.querySelector('.hero__title a').href = f.href;
    hero.querySelector('[data-hero-when]').textContent = when;
    hero.querySelector('[data-hero-venue]').textContent = n.venue;
    hero.querySelector('[data-hero-meta]').textContent = f.meta;
    hero.querySelector('[data-hero-desc]').textContent = f.desc;
    hero.querySelector('[data-hero-buy]').href = n.href;
    hero.querySelector('[data-hero-info]').href = f.href;
    hero.querySelector('.hero__backdrop').src = f.backdrop;
    var list = hero.querySelector('.next-list');
    if (list) {
      list.innerHTML = '';
      screenings.slice(1, 5).forEach(function (s) {
        var li = document.createElement('li');
        li.innerHTML = '<a class="next-item"><time><span></span> <b></b></time><span class="next-item__title"></span><span class="next-item__venue"></span></a>';
        li.querySelector('a').href = s.href;
        li.querySelector('time span').setAttribute('data-rel-day', s.key);
        li.querySelector('time b').textContent = pad(s.start.getHours()) + ':' + pad(s.start.getMinutes());
        li.querySelector('.next-item__title').textContent = s.film.dataset.title;
        li.querySelector('.next-item__venue').textContent = s.venue;
        list.appendChild(li);
      });
    }
  }
  labelDays();

  /* ---- Programme filters ---- */
  var films = Array.prototype.slice.call(document.querySelectorAll('.films > .film'));
  if (!films.length) return;
  var filters = document.querySelector('.filters');
  var status = document.querySelector('.filters__status');
  var empty = document.querySelector('.empty');
  var note = document.querySelector('.section-head__note');
  var venueBtns = document.querySelectorAll('[data-filter-venue]');
  var dayBtns = document.querySelectorAll('[data-filter-day]');
  var state = { venue: 'all', day: 'all' };

  // Days that are already over disappear from the chips.
  dayBtns.forEach(function (b) {
    if (b.dataset.filterDay !== 'all' && !screenings.some(function (s) { return s.key === b.dataset.filterDay; })) b.remove();
  });
  dayBtns = document.querySelectorAll('[data-filter-day]');
  venueBtns.forEach(function (b) {
    var n = count(b.dataset.filterVenue, 'all');
    var c = b.querySelector('.chip__count');
    if (c) c.textContent = String(n);
    if (b.dataset.filterVenue !== 'all' && n === 0) b.hidden = true; // venue has nothing left
  });

  // Compatible with the old ?venue=<slug> links.
  var qs = new URLSearchParams(location.search);
  if (qs.get('venue') && document.querySelector('[data-filter-venue="' + qs.get('venue') + '"]')) state.venue = qs.get('venue');

  function count(venue, day) {
    return screenings.filter(function (s) {
      return (venue === 'all' || s.el.dataset.venue === venue) && (day === 'all' || s.key === day);
    }).length;
  }

  function apply() {
    var shows = 0, filmsShown = 0;
    films.forEach(function (film) {
      var visible = 0;
      film.querySelectorAll('.showtime').forEach(function (st) {
        var ok = (state.venue === 'all' || st.dataset.venue === state.venue) && (state.day === 'all' || st.dataset.day === state.day);
        st.hidden = !ok;
        if (ok) visible++;
      });
      film.hidden = visible === 0;
      shows += visible;
      if (visible) filmsShown++;
    });
    venueBtns.forEach(function (b) { b.setAttribute('aria-pressed', String(b.dataset.filterVenue === state.venue)); });
    dayBtns.forEach(function (b) {
      b.setAttribute('aria-pressed', String(b.dataset.filterDay === state.day));
      // Days with nothing at the chosen venue are hidden rather than leading to an empty list.
      b.hidden = b.dataset.filterDay !== 'all' && count(state.venue, b.dataset.filterDay) === 0;
    });
    if (empty) empty.hidden = shows !== 0;
    films.forEach(function (film) {
      var c = film.querySelector('.film__count');
      var n = film.querySelectorAll('.showtime:not([hidden])').length;
      if (c) c.textContent = n + (n === 1 ? ' seanss' : ' seanssi');
    });
    if (note) {
      var venuesShown = {};
      screenings.forEach(function (s) { if (!s.el.hidden) venuesShown[s.el.dataset.venue] = 1; });
      var nv = Object.keys(venuesShown).length;
      note.textContent = shows + (shows === 1 ? ' seanss' : ' seanssi') + ', ' + filmsShown + (filmsShown === 1 ? ' film' : ' filmi') + ', ' +
        nv + (nv === 1 ? ' asukoht' : ' asukohta') + (state.venue === 'all' && state.day === 'all' ? ' üle saare' : ' valitud');
    }
    if (status) {
      status.textContent = '';
      if ((state.venue !== 'all' || state.day !== 'all') && shows) {
        var sr = document.createElement('span');
        sr.className = 'sr-only';
        sr.textContent = 'Näitan ' + shows + ' ' + (shows === 1 ? 'seanss' : 'seanssi') + ', ' + filmsShown + ' ' + (filmsShown === 1 ? 'film' : 'filmi') + '. ';
        status.appendChild(sr);
        var r = document.createElement('button');
        r.type = 'button'; r.textContent = 'Tühista filtrid';
        r.addEventListener('click', resetAll);
        status.appendChild(r);
      }
    }
  }
  function scrollToList() {
    var top = document.getElementById('kava').getBoundingClientRect().top;
    if (top < 0) document.getElementById('kava').scrollIntoView();
  }
  function resetAll() { state.venue = 'all'; state.day = 'all'; syncUrl(); apply(); }
  function syncUrl() {
    var url = new URL(location.href);
    if (state.venue === 'all') url.searchParams.delete('venue'); else url.searchParams.set('venue', state.venue);
    history.replaceState(null, '', url);
  }

  venueBtns.forEach(function (b) {
    b.addEventListener('click', function () {
      state.venue = b.dataset.filterVenue;
      if (state.day !== 'all' && count(state.venue, state.day) === 0) state.day = 'all';
      syncUrl(); apply(); scrollToList();
      var row = document.querySelector('.chips--scroll');
      if (row) { row.scrollTo({ left: 0 }); row.dispatchEvent(new Event('scroll')); }
    });
  });
  dayBtns.forEach(function (b) {
    b.addEventListener('click', function () { state.day = b.dataset.filterDay; apply(); scrollToList(); });
  });
  var reset = document.querySelector('[data-filter-reset]');
  if (reset) reset.addEventListener('click', resetAll);

  // Shadow when the filter bar is stuck under the header; fade hint only while chips can scroll further.
  if (filters && 'IntersectionObserver' in window) {
    var sentinel = document.createElement('div');
    sentinel.setAttribute('aria-hidden', 'true');
    filters.parentNode.insertBefore(sentinel, filters);
    var headerH = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--header-h'), 10) || 64;
    new IntersectionObserver(function (e) { filters.classList.toggle('is-stuck', !e[0].isIntersecting); },
      { rootMargin: '-' + (headerH + 9) + 'px 0px 0px 0px' }).observe(sentinel);
  }
  document.querySelectorAll('.chips').forEach(function (c) {
    var upd = function () { c.classList.toggle('at-end', c.scrollLeft + c.clientWidth >= c.scrollWidth - 4); };
    c.addEventListener('scroll', upd, { passive: true });
    window.addEventListener('resize', upd);
    upd();
  });

  apply();
})();
