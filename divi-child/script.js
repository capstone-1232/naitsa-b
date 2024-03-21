$('.cat-list-item').on('click', function() {
    $('.cat-list-item').removeClass('active');
    $(this).addClass('active');
  
    $.ajax({
      type: 'POST',
      url: '/wp-admin/admin-ajax.php',
      dataType: 'html',
      data: {
        action: 'filter_menu',
        category: $(this).data('slug'),
      },
      success: function(res) {
        $('.menu-tiles').html(res);
      }
    })
  });