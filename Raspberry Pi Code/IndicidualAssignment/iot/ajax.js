

setInterval(() => {
    $.ajax({url: "SpotStatus.php"}).done((output) => {
        $("#SpotStatus").html(output);
    });
}, 1000);

setInterval(() => {
    $.ajax({url: "LastDuration.php"}).done((output) => {
        $("#LastDuration").html(output);
    });
}, 1000);

setInterval(() => {
    $.ajax({url: "Price.php"}).done((output) => {
        $("#Price").html(output);
    });
}, 1000);