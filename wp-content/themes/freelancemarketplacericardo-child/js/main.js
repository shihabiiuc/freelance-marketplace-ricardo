jQuery(document).ready(function($){

// Front page -trusted by
    var trust = $('.trusted');
    trust.waypoint(function() {
        trust.addClass("animated zoomIn");
        trust.removeClass("js-ini-position");
    }, {offset: '80%'});


    // modal -watch his story
    var watchstory = $('#youtubeAutoplayToggle');
    $('.close').on('click', function (e) {
        $("#story iframe").attr("src", null);
    });
    watchstory.on('click', function (e) {
        $("#story iframe").attr("src", 'https://www.youtube.com/embed/StTQzAE3orI?autoplay=1&showinfo=0&controls=0');
    });









    




});

