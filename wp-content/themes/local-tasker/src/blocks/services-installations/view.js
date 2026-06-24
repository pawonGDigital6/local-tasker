import Swiper from "swiper/bundle";
import "swiper/css/bundle";

// Loop through every instance of the component container
document.querySelectorAll(".lt-service-installation").forEach((container) => {

	// 1. Initialize Left Swiper (Scoped to this container)
	const swiper1 = new Swiper(container.querySelector(".lt-left-slide"), {
		speed: 500,
		spaceBetween: 24,
		slidesPerView: 1,
		pagination: {
			// Finds the pagination inside THIS container only
			el: container.querySelector(".lt-slide-pagination"),
			clickable: true,
		},
	});

	// 2. Initialize Right Swiper (Scoped to this container)
	const swiper2 = new Swiper(container.querySelector(".lt-right-slide"), {
		speed: 500,
		spaceBetween: 24,
		slidesPerView: 1,
		navigation: {
			// Finds the arrows inside THIS container only
			nextEl: container.querySelector(".slide-arrow.next"),
			prevEl: container.querySelector(".slide-arrow.prev"),
		},
	});

	// 3. Sync them together for this specific instance
	swiper1.controller.control = swiper2;
	swiper2.controller.control = swiper1;
});

