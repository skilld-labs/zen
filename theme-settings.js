$(document).ready( function() {
  // Breadcrumb
  $('#edit-zen-breadcrumb').change(
    function() {
      div = $('#div-zen-breadcrumb');
      if ($('#edit-zen-breadcrumb').val() == 'no') {
        div.slideUp('slow');
      } else if (div.css('display') == 'none') {
        div.slideDown('slow');
      }
    }
  );
  if ($('#edit-zen-breadcrumb').val() == 'no') {
    $('#div-zen-breadcrumb').css('display', 'none');
  }
} );
