jQuery(document).ready(function($){

// FAQ PAGE - QUICK LINKS - MOBILE MENU
    var mobilemenu = $('#mobilemenu');
    mobilemenu.waypoint(function() {
        mobilemenu.toggleClass("animated lightSpeedIn js-mobilemenu");

    }, {offset: '15%'});


    // modal
    var watchstory = $('#youtubeAutoplayToggle');
    $('.close').on('click', function (e) {
        $("#story iframe").attr("src", null);
    });
    watchstory.on('click', function (e) {
        $("#story iframe").attr("src", 'https://www.youtube.com/embed/StTQzAE3orI?autoplay=1&showinfo=0&controls=0');
    });









    




});

