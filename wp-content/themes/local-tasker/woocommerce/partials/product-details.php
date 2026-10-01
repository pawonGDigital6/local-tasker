<?php
/**
 * Product details — just the product's short description (excerpt), as-is.
 *
 * @package local-tasker
 */

defined('ABSPATH') || exit;

global $product;

$excerpt = (string) $product->get_short_description();

if ('' === trim(wp_strip_all_tags($excerpt))) {
	return;
}
?>
<section class="lt-product-details bg-lt-white py-12 md:py-20"
	aria-label="<?php esc_attr_e('Product details', 'local-tasker'); ?>">
	<div class="container">
		<div class="lt-spec-table max-w-[1300px] mx-auto rounded-2xl border border-[#cccccc] overflow-hidden">
			<!-- Header -->
			<div class="text-center px-6 py-5 bg-[#E8E8E8] border-b border-[#cccccc]">
				<h2 class="text-body font-semibold font-semi-ext text-lt-text-primary m-0 leading-none relative top-[2px]">
					<?php esc_html_e('Details', 'local-tasker'); ?>
				</h2>
			</div>
			<!-- Spec table (from the product short description WYSIWYG) -->
			<div class="lt-spec-table__body">
				<?php echo wp_kses_post($excerpt); ?>
			</div>
			<div class="table-foot table-foot text-center px-6 py-5 bg-[#E8E8E8] h-[57px]"></div>
		</div>
	</div>
</section>
<!-- Download File -->
<?php
$downloadable_file = get_field('downloadable_file');
if ($downloadable_file): ?>
	<div class="wrapper" style="margin-bottom: 80px;">
		<div class="container">
			<div class="inner  max-w-[1300px] mx-auto">
				<div class="text-center px-6 py-5 bg-[#E8E8E8] border-b border-[#cccccc]" style="border-radius: 16px 16px 0 0;">
					<h2 class="text-body font-semibold font-semi-ext text-lt-text-primary m-0 leading-none relative top-[2px]">
						Download </h2>
				</div>
				<div class="btn-wrap">
					<a class="flex items-center justify-between gap-2 border-b border-[#cccccc] py-6" href="<?php echo esc_url($downloadable_file['url']); ?>"
						download>
						<span class="text font-semibold"><?php echo esc_html($downloadable_file['filename']); ?></span>
						<span class="icon shrink-0">
							<svg xmlns="http://www.w3.org/2000/svg" width="18" height="21" viewBox="0 0 18 21" fill="none">
								<path
									d="M11 0.5V4.5C11 5.03043 11.2107 5.53914 11.5858 5.91421C11.9609 6.28929 12.4696 6.5 13 6.5H17M9 16.5V10.5M9 16.5L6 13.5M9 16.5L12 13.5M12 0.5H3C2.46957 0.5 1.96086 0.710714 1.58579 1.08579C1.21071 1.46086 1 1.96957 1 2.5V18.5C1 19.0304 1.21071 19.5391 1.58579 19.9142C1.96086 20.2893 2.46957 20.5 3 20.5H15C15.5304 20.5 16.0391 20.2893 16.4142 19.9142C16.7893 19.5391 17 19.0304 17 18.5V5.5L12 0.5Z"
									stroke="#151515" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</span>
					</a>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>