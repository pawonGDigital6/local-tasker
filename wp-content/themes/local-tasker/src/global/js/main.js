import domReady from '@wordpress/dom-ready';
import navigation from './components/navigation';

domReady(() => {
	navigation.init();
});
