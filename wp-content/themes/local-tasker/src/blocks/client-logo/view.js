import Swiper from "swiper/bundle";
import "swiper/css/bundle";

// Loop through every instance of the component container
document.querySelectorAll(".lt-client-logo").forEach((container) => {
  // 1. Initialize Left Swiper (Scoped to this container)
  const swiper1 = new Swiper(
    container.querySelector(".lt-client-logo__carousel"),
    {
      loop: true, // Essential for infinite continuous scrolling
      slidesPerView: 3, // Allows slides to retain their natural content width
      spaceBetween: 50, // Uniform gap between your marquee items
      speed: 4000, // Duration (in ms) to transition across one slide (lower = faster)
      allowTouchMove: false, // Prevents manual user swiping from breaking the flow
      autoplay: {
        delay: 0, // Crucial: 0ms delay keeps the marquee moving instantly
        disableOnInteraction: false, // Ensures it keeps moving if it loses focus
      },
      breakpoints: {
        768: {
          slidesPerView: 4,
          spaceBetween: 40,
        },
        1025: {
          slidesPerView: 6,
          spaceBetween: 50,
        },
        1200: {
          slidesPerView: 7,
          spaceBetween: 50,
        },
      },
    },
  );
});
