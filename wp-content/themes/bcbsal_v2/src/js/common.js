/* main navigation bar toggler start */

const mainNavToggleBtn = document.getElementById('main-nav-toogle-btn');
const mainNavUl = document.querySelector('.main-nav-ul');

mainNavToggleBtn.addEventListener('click', () => {
  mainNavUl.classList.toggle('show-main-nav');
});
const srcBtn = document.getElementById('search-text');
const searchTn = document.querySelector('.input-text');

srcBtn.addEventListener('click', () => {
  searchTn.classList.toggle('input-show');
});
// dropdown js
const dropBtn = document.getElementById('dropdown');
const dropDownContent = document.querySelector('.dropdown-content');
const dropIcon = document.querySelector('.nav-bottom-img-1');

dropBtn.addEventListener('click', () => {
  dropDownContent.classList.toggle('dropdown-content-show');
  dropIcon.classList.toggle('nav-bottom-img');
});

const buttons = document.querySelectorAll('[data-carousel-button]');

buttons.forEach((button) => {
  button.addEventListener('click', () => {
    const offset = button.dataset.carouselButton === 'next' ? 1 : -1;
    const slides = button
      .closest('[data-carousel]')
      .querySelector('[data-slides]');
    const activeSlide = slides.querySelector('[data-active]');
    let newIndex = [...slides.children].indexOf(activeSlide) + offset;
    if (newIndex < 0) newIndex = slides.children.length - 1;
    if (newIndex >= slides.children.length) newIndex = 0;

    slides.children[newIndex].dataset.active = true;
    delete activeSlide.dataset.active;
  });
});
