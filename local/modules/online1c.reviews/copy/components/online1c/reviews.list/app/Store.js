/** @var o window **/
"use strict";
import {combineReducers, createStore, applyMiddleware, compose} from 'redux';

const addParamsRequest = (store) => {
	return (next) => {
		return (action) => {
			// action.params = store.getState().Params;
			return next(action);
		};
	};
};

export default function configureStore(dataTable) {

	const Store = {
		Data(state = {items: {}, clearForm: false}, action){
			let formComment = state.Form;
			if (formComment === undefined) {
				formComment = {};
			}
			switch (action.type) {
				case 'GET_LIST':
					return {...state, items: action.items};

				case 'SHOE_FORM':
					return {...state, showForm: !action.bShow};

				case 'CHANGE_FORM':
					formComment[action.data.name] = action.data;
					return {...state, Form: formComment};

				case 'VALID_FORM':
					let form = action.form;
					$.each(form, (code, field) => {
						if(field.valid === false && field.pristine === true){
							field.pristine = false;
							field.dirty = true;
						}
						form[code] = field;
					});
					return {...state, Form: form};

				case 'GET_PARAMETERS':
					return {...state, Params: action.Params};

				case 'CLEAR_FORM':
					return {...state, clearForm: action.clearForm};

				case 'LIKE_UPDATE':
					let index = null, newState = state.items;
					if(state.items.ITEMS instanceof Array && state.items.ITEMS.length > 0){
						state.items.ITEMS.filter((el, i) => {
							if(el.ID === action.data.ID){
								index = i;
							}
						});
					}

					if(index !== null){
						newState.ITEMS[index] = Object.assign(newState.ITEMS[index], action.data);
					}

					return {...state, items: newState};

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


	};

	const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;

	let storeBuilder, env = process.env.NODE_ENV;
	if (env === 'dev') {
		storeBuilder = createStore(
			combineReducers(Store),
			composeEnhancers(applyMiddleware(addParamsRequest))
		);
	} else {
		storeBuilder = createStore(
			combineReducers(Store),
			compose(applyMiddleware(addParamsRequest))
		);
	}

	return storeBuilder;
}