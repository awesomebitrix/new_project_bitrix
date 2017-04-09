/**
 * Created by dremin_s on 14.03.2017.
 */
/** @var o React */
/** @var o ReactDOM */
/** @var o is */
/** @var o $ */
"use strict";
import { combineReducers, createStore } from 'redux';

export default function configureStore(initialState = {namespaces: [], useNpm: null}) {

	const Store = {
		Data(state = initialState, action){

			switch (action.type) {
				case'NAMESPACE_LIST':
					return {...state, namespaces: action.items};
				case 'USE_NPM':
					let bUse = action.value;
					if (bUse !== 'Y') {
						bUse = false;
					} else {
						bUse = true;
					}
					return {...state, useNpm: bUse}
			}

			return state;
		},
		Loader(state = {show: false, text: false}, action){
			switch (action.type) {
				case 'SWITCH_LOADER':
					return {...state, show: action.show, text: action.text};
				default:
					return state;
			}
		},
		FormCreate(state = {}, action){
			switch (action.type) {
				case 'SUBMIT_FORM':
					return {...state, data: action.data};
				default:
					return state;
			}
		}
	};

	let storeBuilder, env = process.env.NODE_ENV;
	if (env === 'dev') {
		storeBuilder = createStore(
			combineReducers(Store),
			window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__()
		);
	} else {
		storeBuilder = createStore(
			combineReducers(Store),
		);
	}

	return storeBuilder;
}
