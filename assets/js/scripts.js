
((($) => {
    $('.menu-item-has-children > a').on('click', function (e) {
        e.preventDefault();
        $(this).parent().toggleClass('active');
    });
})(jQuery));
