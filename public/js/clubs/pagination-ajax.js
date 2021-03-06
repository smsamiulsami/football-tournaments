$(function() {

    $(document).on('click', '.pagination-clubs-list a', function (e) {
        e.preventDefault();

        var url = $(this).attr('href');
        var sortBy = $('.active-sort').attr('data-sort-by');
        var direction = $('.active-sort').attr('data-direction-current');
        getListWithData(url, sortBy, direction);
        window.history.pushState("", "", url);
    });

    $(document).on('click', '.pagination-clubs-searchable-cards a', function (e) {
        e.preventDefault();

        var url = $(this).attr('href');
        getClubCardsWithPagination(url);
    });
});