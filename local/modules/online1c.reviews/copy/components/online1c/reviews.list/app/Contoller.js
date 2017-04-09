/**
 * Created by dremin_s on 01.03.2017.
 */
/** @var o React */
/** @var o ReactDOM */
/** @var o is */
/** @var o $ */
"use strict";
import Rest from 'preloader/RestService';

const Ajax = new Rest({
	baseURL: '/rest/reviews/ajax'
});

const mapStateToProps = (state) => {
	return state;
};

const mapDispatchToProps = (dispatch) => {
	return {
		startLoad(text = false){
			dispatch({type: 'SWITCH_LOADER', show: true, text: text});
		},

		stopLoad(){
			dispatch({type: 'SWITCH_LOADER', show: false, text: false});
		},

		getParameters() {
			Ajax.get('/getParams').then(res => {
				if (res.data.STATUS === 1) {
					dispatch({type: 'GET_PARAMETERS', Params: res.data.DATA});
				}
			});
		},

		getReviewList(page = 1){
			this.startLoad();
			Ajax.get('/getList', {params: {page}}).then(res => {
				dispatch({type: 'GET_LIST', items: res.data.DATA});
				this.stopLoad();
			});
		},

		showCommentForm(bShow = false){
			dispatch({type: 'SHOE_FORM', bShow});
		},

		changeForm(data = {}){
			if (data.hasOwnProperty('value') && data.hasOwnProperty('name')) {
				dispatch({type: 'CHANGE_FORM', data});
			}
		},

		saveComment(data){
			if (data.valid === true) {
				this.startLoad('Сохранение комментария...');
				Ajax.post('/saveComment', data.values).then(res => {
					if (res.data.STATUS === 1) {
						let text = '';
						if (res.data.DATA.ACTIVE !== 'Y') {
							text = 'Спасибо! Ваш комментарий проходит модерацию.'
						}
						swal('Комментарий сохранен', text, 'success');
						this.getReviewList(1);
						this.showCommentForm(true);
					}
					this.stopLoad();
				});
			}
		},

		actionLike(element = null){
			if(element !== null){
				Ajax.post('/like', element).then(res => {
					if(res.data.STATUS === 1){
						dispatch({type: 'LIKE_UPDATE', data: res.data.DATA});
					}
				});
			}
		},

	}
};
export { mapStateToProps };
export { mapDispatchToProps };