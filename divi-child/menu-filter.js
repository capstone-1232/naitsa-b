jQuery(document).ready(function($) {
    // Initially show all menu items
    $('.menu-category').show();

    // Event delegation for dynamically added elements
    $(document).on('click', '.cat-list-item', function(e) {
        e.preventDefault();
        
        console.log("Link clicked"); // Check if the link click event is triggered
        
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
            $('.menu-category-' + slug).show();
        }
    });
});