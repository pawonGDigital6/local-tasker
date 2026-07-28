import domReady from '@wordpress/dom-ready';
import navigation from './components/navigation';
import quotePop from './components/quote-popup';
import searchPop from './components/search-popup';
import { relatedCarousel, projectGallery } from "./components/global-carousel";
import fancyBoxInit from './components/global-fancybox';

domReady(() => {
	navigation.init();
	quotePop.init();
	searchPop.init();
	relatedCarousel();
	fancyBoxInit();
	projectGallery();
});
