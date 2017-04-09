/**
 * Created by dremin_s on 07.03.2017.
 */
/** @var o React */
/** @var o ReactDOM */
/** @var o is */
/** @var o $ */
"use strict";
import Store from './Store';
import {Provider} from 'react-redux';
import Review from './Review';

if(window.online1c === undefined){
	window.online1c = {
		reviews: {}
	};
} else if(window.online1c === 'object' && !window.online1c.hasOwnProperty('reviews')){
	window.online1c.reviews = {};
}

window.online1c.reviews.init = (params = {}) => {
	ReactDOM.render(
		<Provider store={Store(params)}><Review /></Provider>,
		BX('o1c_review_wr')
	);
};

$(function () {
	if(BX('o1c_review_wr') !== null){
		online1c.reviews.init();
	}
});