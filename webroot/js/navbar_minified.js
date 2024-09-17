document.addEventListener('DOMContentLoaded', () => {
    window.onscroll = function () {
        var navbar = document.getElementById("navbar");
        if (window.scrollY > 100) {
            navbar.classList.add("navbar-shrink");
        } else {
            navbar.classList.remove("navbar-shrink");
        }
    };
    // Initialize Bootstrap popover
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    })
});