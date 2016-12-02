(function($) {
  $(document).ready(function() {
    $('.music-uncheck').on('click', function(e) {
      e.preventDefault();
      $('form input[type="checkbox"]').prop("checked", false);
    });
    $('.music-check').on('click', function(e) {
      e.preventDefault();
      $('form input[type="checkbox"]').prop("checked", true);
    });
  })
}(jQuery));