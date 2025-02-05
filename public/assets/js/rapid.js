//Enable tooltip
const tooltipTriggerList = document.querySelectorAll(
    '[data-bs-toggle="tooltip"]'
);
const tooltipList = [...tooltipTriggerList].map(
    (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
);

function updateSidebarSize() {
    const bodyElement = $("body");
    const currentSize = bodyElement.attr("data-sidebar-size");

    // Check the window width
    if ($(window).width() < 768) {
        if (currentSize !== "sm") {
            bodyElement.attr("data-sidebar-size", "sm");
            $("#toggleButton i")
                .removeClass("bi-chevron-left")
                .addClass("bi-chevron-right");
        }
    } else {
        // Reset to 'lg' if needed (adjust as per your initial setup)
        if (currentSize !== "lg") {
            bodyElement.attr("data-sidebar-size", "lg");
            $("#toggleButton i")
                .removeClass("bi-chevron-right")
                .addClass("bi-chevron-left");
        }
    }
}
function toggleFullscreen() {
    const doc = document.documentElement;
    if (!document.fullscreenElement) {
        doc.requestFullscreen();
        $("#toggleFullScreen i")
            .removeClass("bi-arrows-angle-expand")
            .addClass("bi-arrows-angle-contract");
    } else if (document.exitFullscreen) {
        document.exitFullscreen();
        $("#toggleFullScreen i")
            .removeClass("bi-arrows-angle-contract")
            .addClass("bi-arrows-angle-expand");
    }
}

$(document).ready(function () {
    $("#searchBtn").on("click", function () {
        if ($("#searchInput").css("width") === "40px") {
            $("#searchInput").css("width", "400px").focus();
            $("#searchInput input").attr("placeholder", "Search...");
            $("#searchInput input").css("padding-inline", "1.75rem");
            $("#searchExit").show();
            $("#searchBtn i")
                .removeClass("bi-search text-dark")
                .addClass("bi-mic text-dark bg-primary-subtle");
            $("#searchInput").css("backgroundColor", "transparent");
        }
    });


    $("#inputsearchHeader").on("click", function () {
        if ($("#searchInput").css("width") === "40px") {
            $("#searchInput").css("width", "400px").focus();
            $("#searchInput input").attr("placeholder", "Search...");
            $("#searchInput input").css("padding-inline", "1.75rem");
            $("#searchExit").show();
            $("#searchBtn i")
                .removeClass("bi-search text-dark")
                .addClass("bi-mic text-dark bg-primary-subtle");
            $("#searchInput").css("backgroundColor", "transparent");
        }
    });
    $("#searchExit").on("click", function () {
        if ($("#searchInput").css("width") === "400px") {
            $("#searchInput").css("width", "40px");
            $("#searchInput input").removeAttr("placeholder", "search...");
            $("#searchInput input").css("padding-inline", "0rem");
            $("#searchExit").hide();
            $("#searchBtn i")
                .removeClass("bi-mic text-theme")
                .addClass("bi-search text-dark");
            $("#searchInput").css("backgroundColor", "#cfe2ff");
        }
    });

    updateSidebarSize();
    // Check on resize
    $(window).resize(function () {
        updateSidebarSize();
    });

    $("#toggleButton").click(function () {
        const bodyElement = $("body");
        const currentSize = bodyElement.attr("data-sidebar-size"); // Use attr to get the latest value
        const iconElement = $("#toggleButton i");

        // Toggle the sidebar size and icon
        if (currentSize === "lg") {
            bodyElement.attr("data-sidebar-size", "sm");
            iconElement.removeClass("bi-chevron-left").addClass("bi-chevron-right");
        } else {
            bodyElement.attr("data-sidebar-size", "lg");
            iconElement.removeClass("bi-chevron-right").addClass("bi-chevron-left");
        }
    });

    $("#toggleFullScreen").click(function () {
        toggleFullscreen();
    });
    $("#searchInput input").focus(function () {
        $(".searchOverlay").show();
    });
    $("#searchInput input").blur(function () {
        $(".searchOverlay").hide();
    });
});
