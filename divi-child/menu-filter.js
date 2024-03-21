jQuery(document).ready(function($) {
    // Initially show all menu items
    $('.menu-category').show();

    // When a category link is clicked
    $('.cat-list-item').on('click', function(e) {
        e.preventDefault();
        
        // Get the category slug
        var slug = $(this).data('slug');
        
        // Remove the 'active' class from all category links
        $('.cat-list-item').removeClass('active');
        
        // Add the 'active' class to the clicked category link
        $(this).addClass('active');
        
        // If 'All' is clicked, show all menu items
        if (slug === '') {
            $('.menu-category').show();
        } else {
            // Otherwise, hide all menu items not in the selected category
            $('.menu-category').hide();
            $('.menu-category.' + slug).show();
        }
    });
});