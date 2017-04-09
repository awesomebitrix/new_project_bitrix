/**
 * Created by dremin_s on 14.03.2017.
 */
/** @var o React */
/** @var o ReactDOM */
/** @var o is */
/** @var o $ */
"use strict";
import Ajax from 'preloader/RestService';

const Rest = new Ajax({
	baseURL: '/rest'
});

const mapStateToProps = (state) => {
	return state;
};

const mapDispatchToProps = (dispatch) => {
	return {
		api: {
			startLoad(text = false){
				dispatch({type: 'SWITCH_LOADER', show: true, text: text});
			},

			stopLoad(){
				dispatch({type: 'SWITCH_LOADER', show: false, text: false});
			},

		}
	}
};

export {mapStateToProps, mapDispatchToProps};