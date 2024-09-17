document.addEventListener('DOMContentLoaded', () => {
    window.addEventListener('scroll', function () {
        var navbar = document.getElementById("logo-icon");
        var hero = document.getElementsByClassName("col-md-5-custom")[0];
        if (window.scrollY > 100) {
            navbar.classList.remove("circle-shadow");
            hero.classList.remove("z-index-fix");
        } else {
            navbar.classList.add("circle-shadow");
            hero.classList.add("z-index-fix");
        }
    });
});
