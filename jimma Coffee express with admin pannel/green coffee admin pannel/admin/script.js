const header = document.querySelector('header');
function fixedNavbar() {
    header.classList.toggle('scrolled', window.pageYOffset > 0)
}
fixedNavbar();
window.addEventListener('scroll', fixedNavbar);

let menu = document.querySelector('#menu-btn');

menu.addEventListener('click', function(){
    let nav = document.querySelector('.navbar');
    nav.classList.toggle('active');
})

const userBtn = document.querySelector('#user-btn');
userBtn.addEventListener('click', function(){
	const userBox = document.querySelector('.profile-detail');
	userBox.classList.toggle('active');
})