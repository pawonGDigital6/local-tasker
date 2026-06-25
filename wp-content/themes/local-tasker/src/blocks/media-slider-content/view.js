import Swiper from "swiper/bundle";
import "swiper/css/bundle";

// Loop through every instance of the component container
document.querySelectorAll(".lt-ms-content").forEach((container) => {
  // 1. Initialize Left Swiper (Scoped to this container)
  const swiper1 = new Swiper(container.querySelector(".media-slider"), {
    speed: 500,
    spaceBetween: 24,
    slidesPerView: 1,
    navigation: {
      // Finds the arrows inside THIS container only
      nextEl: container.querySelector(".slide-arrow.next"),
      prevEl: container.querySelector(".slide-arrow.prev"),
    },
  });
});
