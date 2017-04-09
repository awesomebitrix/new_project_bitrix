/**
 * Created by dremin_s on 14.03.2017.
 */
/** @var o React */
/** @var o ReactDOM */
/** @var o is */
/** @var o $ */
"use strict";
import { combineReducers, createStore, applyMiddleware, compose} from 'redux';

const myMiddleware = (store) => {
	return (next) => {
		return (action) => {
			// action.params = store.getState().Params;
			return next(action);
		};
	};
};

export default function configureStore(initialState = {}) {

	const Store = {
		Data(state = initialState, action){

			switch (action.type) {
				case'ACTION_TEST':
					return {...state, items: action.items};
				default:
					return state;
			}
		},
		Loader(state = {show: false, text: false}, action){
			switch (action.type) {
				case 'SWITCH_LOADER':
					return {...state, show: action.show, text: action.text};
				default:
					return state;
			}
		},
	};

	let storeBuilder, env = process.env.NODE_ENV;
	const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;

	if (env === 'dev') {
		storeBuilder = createStore(
			combineReducers(Store),
			composeEnhancers(applyMiddleware(myMiddleware))
		);
	} else {
		storeBuilder = createStore(
			combineReducers(Store),
			composeEnhancers(applyMiddleware(myMiddleware))
		);
	}

	return storeBuilder;
}
