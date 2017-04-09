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
	baseURL: '/rest/admin/components'
});

const mapStateToProps = (state) => {
	return state;
};


const mapDispatchToProps = (dispatch) => {

	const startLoad = (text = false) => {
		dispatch({type: 'SWITCH_LOADER', show: true, text: text});
	};

	const stopLoad = () => {
		dispatch({type: 'SWITCH_LOADER', show: false, text: false});
	};

	return {
		api: {

			getNamespace(data){
				let folder = '';
				if(typeof data === 'object'){
					folder = data.value.id;
				} else if(typeof data === 'string'){
					folder = data;
				}

				Rest.get('/getNamespaceList', {params: {folder: folder}}).then(res => {
					dispatch({type: 'NAMESPACE_LIST', items: res.data.DATA});
				});
			},

			submitForm:(data) => {
				dispatch({type: 'SUBMIT_FORM', data});
				if(data.valid === true){
					startLoad('Создание компонента...');

					Rest.post('/create', data.values).then(res => {
						if(res.data.STATUS === 1){
							let newName = '';
							if(data.values.hasOwnProperty('NAMESPACE_NEW') && data.values.NAMESPACE_NEW.length > 0){
								newName = data.values.NAMESPACE_NEW;
							} else {
								newName = data.values.NAMESPACE;
							}
							newName += ':' + data.values.NAME;
							swal('', 'Создан новый компонент '+ newName + ' в папке '+ data.values.FOLDER, 'success');
						}
						stopLoad();
					});
				}
			},

			useNpm(data){
				dispatch({type: 'USE_NPM', value: data.value});
			}
		}
	}
};

export {mapStateToProps, mapDispatchToProps};