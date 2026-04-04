(function () {
  var el = document.querySelector('#views-graphic');
  if (!el || el.getAttribute('data-votix-dashboard-chart') === '1') {
    return;
  }
  if (typeof Chartist === 'undefined') {
    return;
  }
  new Chartist.Line('#views-graphic', {
    labels: ['Mon', 'Tue', 'Wed', 'Thur', 'Fri', 'Sat', 'Sun'],
    series: [
      [5, 9, 7, 8, 6, 4, 8]
    ]
  }, {
    low: 0,
    showArea: true,
    fullWidth: true,
    distributeSeries: true
  });
})();
