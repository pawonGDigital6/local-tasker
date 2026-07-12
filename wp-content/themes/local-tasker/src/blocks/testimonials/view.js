import Swiper from "swiper/bundle";
import "swiper/css/bundle";

new Swiper(".testimonial-slider", {
  speed: 500,
  spaceBetween: 12,
  slidesPerView: "auto",
  loop: true,
  breakpoints: {
    768: {
      spaceBetween: 38,
    },
  },
});

