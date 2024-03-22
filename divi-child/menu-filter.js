jQuery(document).ready(function($){
  // $('.menu-category').show();
  $(".menu-category").hide();
  $(".menu-category-burgers-more").show(); // CHANGE THIS TO WHATEVER CATEGORY YOU WANT AS DEFAULT

  $('.cat-list-item[data-slug="burgers-more"]').addClass("active"); // CHANGE THIS TO WHATEVER CATEGORY YOU WANT AS DEFAULT

  $(document).on("click", ".cat-list-item", function (e) {
    e.preventDefault();

    var slug = $(this).data("slug");

    // remove the 'active' class from all category links
    $(".cat-list-item").removeClass("active");

    // on click add the active class to the clicked category link
    $(this).addClass("active");

    // if all: show all menu items
    if (slug === "") {
      $(".menu-category").show();
    } else {
      // else: hide all menu items not in the selected category
      $(".menu-category").hide();
      $(".menu-category-" + slug).show();
    }
  });
});
