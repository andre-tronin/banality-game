$(function () {
    var storiesInterval = 1 * 1000,
        game = '';
    var getGameStatus = function () {
        $.ajax({
            type: "GET",
            url: window.location.pathname + "/game-status"
        }).done(function (data) {
            if (game !== '' && game !== data) {
                location = '';
            }
            game = data;
        }).fail(function () {
            console.error('error');
        }).always(function () {
            // Schedule the next request after this one completes,
            // even after error
            setTimeout(getGameStatus, storiesInterval);
        });
    }

    getGameStatus();
});