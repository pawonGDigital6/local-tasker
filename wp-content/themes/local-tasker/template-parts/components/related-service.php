<div
	class="r-service-card relative overflow-hidden sm:rounded-[8px] rounded-[4px] flex items-end sm:p-6 p-[13px] md:min-h-[540px] smlr:min-h-[420px] min-h-[293px] group">
	<div class="abs-img absolute inset-0 w-full h-full">
		<?php
		// Fetch the featured image if it exists.
		if (has_post_thumbnail()) {
			the_post_thumbnail('full', array('class' => 'w-full h-full object-cover transition-transform duration-600 group-hover:scale-110 will-change-transform'));
		}
		?>
	</div>
	<div class="overlay absolute inset-0 w-full h-full pointer-events-none"
		style="background: linear-gradient(180deg, rgba(0, 0, 0, 0) 30.11%, #000000 100%);"></div>
	<!-- Content -->
	<div class="r-service-card__content wd:pb-[6px] flex flex-col sm:gap-8 gap-2.5 justify-between relative z-10">
		<div class="up">
			<h2 class="r-service-card__title sm:mb-4 mb-2 text-lt-white text-h4">
				<?php the_title(); ?>
			</h2>
			<div class="r-service-card__excerpt text-lt-white sm:tracking-[0.02em] max-sm:text-caption-sm">
				<?php
				// Limits excerpt to 15 words to keep cards uniform.
				echo wp_kses_post(wp_trim_words(get_the_excerpt(), 15, '...'));
				?>
			</div>
		</div>
		<!-- Btn -->
		<div class="btn-wrap">
			<a class="r-service-card__link group inline-flex w-fit items-center gap-2 text-caption-md max-sm:text-caption-sm font-semibold leading-6 text-lt-white md:tracking-[0.02em]"
				href="<?php echo esc_url(get_permalink()); ?>">
				<span class="text"><?php esc_html_e('Learn More', 'local-tasker'); ?></span>
				<span class="icon transition-transform duration-400 shrink-0 group-hover:rotate-45 max-sm:w-[7.5px] max-sm:pt-[1px]">
					<svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M0.75 8.75L8.75 0.75M2.75035 0.75H8.75V6.74965" stroke="white" stroke-width="1.5"
							stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</span>
			</a>
		</div>
	</div>
	<a href="<?php echo esc_url(get_permalink()); ?>" class="stretched-link"></a>
</div>