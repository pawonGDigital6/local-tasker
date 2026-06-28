import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
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


// Binds Fancybox to all elements containing the data-fancybox attribute
Fancybox.bind("[data-fancybox]", {
  // Your custom configuration options go here
  Infinite: true,
  Images: {
    Protected: true,
  }
});

