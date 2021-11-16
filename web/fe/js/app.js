$(window).scroll(function () {
    $('video').each(function () {
        if ($(this).is(":in-viewport")) {
            $(this)[0].play();
        } else {
            $(this)[0].pause();
        }
    });
});

$(document).ready(function () {
    $('.navbar-toggle').click(function () {
        $('.navigation').stop(true, true).toggleClass('open');
        $(this).toggleClass('open');
    });
    setTimeout(function () {
        $('body').addClass('loaded');
        setTimeout(function () {
            $('#loader-wrapper').css('display', 'none');
            $('body').addClass('loaded');
        }, 1000);
    }, 3000);

});