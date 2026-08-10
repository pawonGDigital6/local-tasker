/**
 * Area Calculator — block view script.
 *
 * The block reuses the theme-wide calculator component, so this file only has
 * to boot it. `initAreaCalculators()` skips any card that is already running
 * (e.g. one booted by the product-single bundle), and every instance is scoped
 * to its own root element — multiple blocks on one page never collide.
 *
 * @package local-tasker
 * @since   1.0.0
 */
import { autoInitAreaCalculators } from '../../global/js/components/area-calculator';

autoInitAreaCalculators();
