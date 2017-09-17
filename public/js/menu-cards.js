$(function () {

    /*
    Handle back button in browser - dynamic loaded content
     */
    if (window.history && window.history.pushState) {

        $(window).on('popstate', function() {
            location.reload();
        });
    }

    $(document).on('click', '.menu-link', function (e) {
        e.preventDefault();

        $('.navbar-nav > .nav-item').removeClass('active');
        $(this).parent().addClass('active');

        displayDynamicContent($(this), $('.menu-card'), $('.jumbotron'));

    });

    $(document).on('click', '.menu-card', function () {
        displayDynamicContent($(this), $('.menu-card'));
    });
});

function displayDynamicContent(trigger, tiles, jumbotrons) {

    if (tiles !== undefined) {
        tiles.hide();
    }

    if (jumbotrons !== undefined) {
        jumbotrons.hide();
    }

    $('#loading').css('display', 'block');

    var url = trigger.attr('href');

    handleAjaxRequest(url);

    window.history.pushState("", "", url);
}

function handleAjaxRequest(url) {

    $.ajax({
        type: "GET",
        url: url,
        cache: false,

        success: function (data) {

            $('#content').html(data);
            $('.menu-card').addClass('animated zoomInUp');
            $('.jumbotron').addClass('animated zoomInUp');

            window.setTimeout(function(){
                removeAnimation();
            }, 800);

            $('#loading').hide();
        }
    });
}

function removeAnimation() {
    $('.menu-card').removeClass('animated zoomInUp');
    $('.jumbotron').removeClass('animated zoomInUp');
}