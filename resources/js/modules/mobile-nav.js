function mobileNav() {
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("mobile-nav-btn").addEventListener("click", function() {
            document.querySelector('.mobile-nav').classList.toggle('mobile-nav--open');
            document.querySelector('.nav-icon').classList.toggle('nav-icon--active');
            document.body.classList.toggle('no-scroll');
        })
    })
}
export default mobileNav;
