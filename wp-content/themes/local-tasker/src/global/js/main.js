import domReady from '@wordpress/dom-ready';
import navigation from './components/navigation';
import searchPop from './components/search-popup';

domReady(() => {
	navigation.init();
	searchPop.init();
});
