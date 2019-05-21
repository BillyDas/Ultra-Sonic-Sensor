

setInterval(() => {
    $.ajax({url: "SpotStatus.php"}).done((output) => {
        $("#SpotStatus").html(output);
    });
}, 500);

setInterval(() => {
    $.ajax({url: "LastDuration.php"}).done((output) => {
        $("#LastDuration").html(output);
    });
}, 500);