import domReady from '@wordpress/dom-ready';
import navigation from './components/navigation';
import searchPop from './components/search-popup';
import relatedCarousel from './components/global-carousel'

domReady(() => {
	navigation.init();
	searchPop.init();
	relatedCarousel();
});
