//Scroll Animation init
AOS.init({
    duration: 1000,
});
$(window).on('load', function() {
    AOS.refresh();
});

$(document).ready(function() {

    //Menu
    $('.nav-icon').click(function() {
        $('.nav-icon').toggleClass('active');
        $('.menu').toggleClass('menu-active');
        $('body').toggleClass('menu-open');
    });

    //Sidebar List
    $('.sidebar-box').each(function() {
        $(this).find('.sidebar-list-headding').click(function() {
            $(this).parent().find('ul.sidebar-list').slideToggle('fast');
            $(this).find('i').toggleClass('fa-plus fa-minus');
            return false;
        });
        $(this).find('ul.sidebar-list li a').click(function() {
            $(this).next('ul').slideToggle('fast');
            //return false;
        });
    });

    $('#sidebar-destinatari').find('ul.sidebar-list').slideToggle('fast');
    $('#sidebar-destinatari').find('i').toggleClass('fa-plus fa-minus');

    //Text Slider
    /*     $('.text-slider').slick({
            dots: true,
            arrows: false,
            infinite: true,
            speed: 500,
            fade: true,
            cssEase: 'linear',
            adaptiveHeight: true,
            autoplay: true,
            autoplaySpeed: 5000
        }); */

    $('.sidebar-wrap').scrollToFixed();


});