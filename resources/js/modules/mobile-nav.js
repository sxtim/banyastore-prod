function mobileNav() {
    document.addEventListener("DOMContentLoaded", function() {
        const mobileNavBtn = document.getElementById("mobile-nav-btn");
        const mobileNav = document.querySelector('.mobile-nav');
        const navIcon = document.querySelector('.nav-icon');

        if (!mobileNavBtn || !mobileNav || !navIcon) {
            return;
        }

        mobileNavBtn.addEventListener("click", function() {
            const isOpen = mobileNav.classList.toggle('mobile-nav--open');
            navIcon.classList.toggle('nav-icon--active');
            mobileNavBtn.setAttribute('aria-expanded', String(isOpen));
            document.body.classList.toggle('no-scroll');
        })
    })
}
export default mobileNav;
