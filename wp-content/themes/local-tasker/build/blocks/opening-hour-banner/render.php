<?php
/**
 * ACF Block template.
 *
 * @param array $block The block settings and attributes.
 */

// Support custom "anchor" values.
$anchor = '';
if (!empty($block['anchor'])) {
	$anchor = 'id="' . esc_attr($block['anchor']) . '" ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'acf-block lt-oh relative overflow-hidden bg-lt-brand sm:py-14 py-16 text-lt-white';

if (!empty($block['className'])) {
	$class_name .= ' ' . $block['className'];
}

if (!empty($block['align'])) {
	$class_name .= ' align' . $block['align'];
}

$class_name .= generate_block_settings_classnames()['class_name'];

// Block Style.
$style_attr = '';
$block_style = generate_block_settings_classnames()['block_style'];

if ($block_style !== '') {
	$style_attr = 'style="' . $block_style . '"';
}

// ==================================== Dynamic Fields
$oh_pre_title = get_field('oh_pre_title');
$oh_sec_title = get_field('oh_sec_title');
$oh_sec_text = get_field('oh_sec_text');
$oh_location = get_field('oh_location');
$oh_add_text = get_field('oh_add_text');
?>
<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
	<!-- Decorative shapes  -->
	<span aria-hidden="true" class="pointer-events-none absolute top-0 right-0 hidden lg:block">
		<svg width="480" height="422" viewBox="0 0 480 422" fill="none" xmlns="http://www.w3.org/2000/svg">
			<circle cx="260" cy="172.32" r="260" fill="white" fill-opacity="0.06" />
			<circle cx="380" cy="320" r="140" fill="white" fill-opacity="0.04" />
		</svg>
	</span>
	<div class="wd:container-bx container relative z-10">
		<div
			class="flex flex-wrap w-full flex-col items-center sm:gap-[50px] gap-[45px] lg:flex-row lg:items-center lg:justify-between lg:gap-4">
			<!-- Intro -->
			<div class="flex lg:w-[58.8%] w-full flex-col gap-11 text-center lg:text-left">
				<div class="flex flex-col items-center sm:gap-4 gap-[10px] lg:items-start">
					<?php if ($oh_pre_title): ?>
						<!-- Pre Title -->
						<span
							class="inline-flex items-center rounded-full bg-lt-white/18 sm:px-4 px-6 sm:py-1.5 py-[7px] text-[11px] font-bold uppercase tracking-[0.06em] text-lt-white max-md:text-[10px]">
							<?php echo esc_html($oh_pre_title); ?>
						</span>
					<?php endif; ?>
					<?php if ($oh_sec_title): ?>
						<!-- Section Title -->
						<h2
							class="font-semi-ext text-[2rem] font-bold leading-[1.62] tracking-[-0.8px] text-lt-white lg:text-[2.5rem] lg:leading-[1.3]">
							<?php echo esc_html($oh_sec_title); ?>
						</h2>
					<?php endif; ?>
					<?php if ($oh_sec_text): ?>
						<!-- Section Content -->
						<div class="mb-0 text-caption-md leading-[1.55] text-lt-white/82 lg:text-body wd:max-w-[635px]">
							<?php echo $oh_sec_text; ?>
						</div>
					<?php endif; ?>
				</div>
				<!-- Address And Location -->
				<address class="flex flex-col items-center gap-[11px] not-italic lg:items-start">
					<?php if ($oh_location): ?>
						<span class="mb-0 flex items-center gap-2 text-[15px] font-semibold text-lt-white max-md:text-[12px]">
							<span class="w-6 h-6 inline-flex shrink-0 text-lt-white" aria-hidden="true">
								<svg width="14" height="20" viewBox="0 0 14 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path
										d="M7 9.5C6.33696 9.5 5.70107 9.23661 5.23223 8.76777C4.76339 8.29893 4.5 7.66304 4.5 7C4.5 6.33696 4.76339 5.70107 5.23223 5.23223C5.70107 4.76339 6.33696 4.5 7 4.5C7.66304 4.5 8.29893 4.76339 8.76777 5.23223C9.23661 5.70107 9.5 6.33696 9.5 7C9.5 7.3283 9.43534 7.65339 9.3097 7.95671C9.18406 8.26002 8.99991 8.53562 8.76777 8.76777C8.53562 8.99991 8.26002 9.18406 7.95671 9.3097C7.65339 9.43534 7.3283 9.5 7 9.5ZM7 0C5.14348 0 3.36301 0.737498 2.05025 2.05025C0.737498 3.36301 0 5.14348 0 7C0 12.25 7 20 7 20C7 20 14 12.25 14 7C14 5.14348 13.2625 3.36301 11.9497 2.05025C10.637 0.737498 8.85652 0 7 0Z"
										fill="white" />
								</svg>
							</span>
							<span><?php echo esc_html($oh_location); ?></span>
						</span>
					<?php endif; ?>
					<?php if ($oh_phone_number = get_field('oh_phone_number')): ?>
						<a href="tel:<?php echo esc_html($oh_phone_number); ?>"
							class="flex items-center gap-2 text-[15px] font-semibold text-lt-white transition-opacity duration-300 hover:opacity-80 focus-visible:opacity-80 max-md:text-[12px]">
							<span class="w-6 h-6 inline-flex shrink-0 text-lt-white" aria-hidden="true">
								<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path
										d="M16.4168 20.0086H16.7868C17.0968 19.9986 17.3868 19.8386 17.5668 19.5786L19.8368 16.3086C19.9868 16.0886 20.0468 15.8186 19.9968 15.5486C19.9723 15.417 19.9216 15.2916 19.8478 15.18C19.7739 15.0683 19.6784 14.9726 19.5668 14.8986L14.6568 11.6286C14.2468 11.3586 13.6968 11.4186 13.3668 11.7786L11.4868 13.8086C10.7268 13.3586 9.45683 12.5486 8.45683 11.5486C7.45683 10.5486 6.64683 9.2786 6.19683 8.5286L8.22683 6.6486C8.58683 6.3186 8.65683 5.7686 8.37683 5.3586L5.10683 0.448597C4.95683 0.228597 4.72683 0.0685966 4.46683 0.0185966C4.19683 -0.0314034 3.92683 0.0185967 3.70683 0.178597L0.436826 2.4386C0.176826 2.6186 0.0168259 2.9086 0.00682586 3.2186C-0.0231741 3.9286 -0.153174 10.2586 4.79683 15.1986C9.25683 19.6586 14.8368 19.9986 16.4168 19.9986V20.0086Z"
										fill="white" />
								</svg>
							</span>
							<span><?php echo esc_html($oh_phone_number); ?></span>
						</a>
					<?php endif; ?>
				</address>
			</div>
			<!-- Opening hours -->
			<div class="cols lg:w-[34.8%] w-full">
				<div class="mx-auto w-full max-w-[23.75rem] lg:mx-0 lg:w-[23.75rem]">
					<div class="rounded-[1.25rem] bg-lt-white/[0.12] sm:p-6 p-5 px-6 pb-8">
						<p class="mb-0 text-[11px] font-bold uppercase tracking-[0.1em] text-lt-white/65"> Opening Hours </p>
						<div class="my-3 h-px w-full bg-lt-white/20"></div>
						<ul class="m-0 flex list-none flex-col gap-[17px] p-0" role="list">
							<?php if (have_rows('oh_lists')): ?>
								<?php while (have_rows('oh_lists')):
									the_row();
									$oh_list_day = get_sub_field('oh_list_day');
									$oh_list_hours = get_sub_field('oh_list_hours');
									?>
									<li
										class="flex items-center justify-between gap-4 text-[13px] leading-none text-lt-white/85 max-md:text-[12px]">
										<?php if ($oh_list_day): ?>
											<span><?php echo $oh_list_day; ?></span>
										<?php endif; ?>
										<?php if ($oh_list_hours): ?>
											<span class="text-right"><?php echo $oh_list_hours; ?></span>
										<?php endif; ?>
									</li>
								<?php endwhile; ?>
							<?php endif; ?>
						</ul>
					</div>
					<?php if ($oh_add_text): ?>
						<div class="m-0 mt-[6px] mb-0 text-[11px] text-lt-white/50"> <?php echo esc_html($oh_add_text); ?> </div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>