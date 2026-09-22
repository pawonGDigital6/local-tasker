import Swiper from "swiper/bundle";
document.querySelectorAll(".lt-testimonials").forEach((container) => {
	const testimonialSlider = new Swiper(container.querySelector(".testimonial-slider"), {
		speed: 500,
		spaceBetween: 12,
		slidesPerView: "auto",
		loop: true,
		navigation: {
			// Finds the arrows inside THIS container only
			nextEl: container.querySelector(".slide-arrow.next"),
			prevEl: container.querySelector(".slide-arrow.prev"),
		},
		breakpoints: {
			768: {
				spaceBetween: 38,
			},
		},
	});
});

