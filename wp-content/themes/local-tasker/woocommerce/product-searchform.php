<?php
/**
 * The template for displaying product search forms
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<form role="search" method="get" class="woocommerce-product-search flex max-sm:flex-wrap w-full max-w-[460px] bg-white md:h-[52px]" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label class="screen-reader-text" for="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>">
        <?php esc_html_e( 'Search for:', 'woocommerce' ); ?>
    </label>
    
    <input 
        type="search" 
        id="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>" 
        class="search-field w-full pt-1 px-4 rounded-none bg-transparent focus:outline-none border-none focus:border-amber-400 font-primary text-sm max-sm:h-[45px]" 
        placeholder="<?php echo esc_attr__( 'Search products&hellip;', 'woocommerce' ); ?>" 
        value="<?php echo get_search_query(); ?>" 
        name="s" 
    />
    
    <button type="submit" value="<?php echo esc_attr__( 'Search', 'woocommerce' ); ?>" class="text-caption-md shrink-0 font-primary text-lt-white font-medium px-5 pt-[17px] pb-[14px] pb-4 bg-lt-brand transition-colors duration-320 hover:bg-lt-text-secondary cursor-pointer leading-none max-sm:h-[45px] max-sm:w-full">
        <?php echo esc_html__( 'Search', 'woocommerce' ); ?>
    </button>
    
    <input type="hidden" name="post_type" value="product" />
</form>