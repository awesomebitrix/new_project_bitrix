/**
 * Created by GrandMaster on 10.03.17.
 */
/** @var o React */
/** @var o ReactDOM */
/** @var o is */
/** @var o $ */
"use strict";
import { RandString } from 'Tools';
// import options from './options';

class Form extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			valid: true,
			pristine: true,
			submited: false,
			submitedFail: false,
			errors: [],
			values: {},
			fields: {}
		};

		this.change = this.change.bind(this);
		this.submit = this.submit.bind(this);

		this.formState = this.state;
	}

	static defaultProps = {
		noValidate: 'novalidate',
		autoComplete: 'off',
		className: '',
		method: 'post',
		name: '',
		id: '',
		isClear: false
	};

	change(ev) {
		ev.preventDefault();

		let newState = {
			...this.state,
			submited: true
		};

		setTimeout(() => {
			newState = Object.assign(newState, this.setValidateForm(this.state.fields));
			if (this.props.hasOwnProperty('onChange')) {
				this.props.onChange(newState);
			}
			this.setState(newState);
		});
	}

	submit(ev) {
		ev.preventDefault();

		let newState = {
			...this.state,
			submited: true
		};

		setTimeout(() => {
			newState = Object.assign(newState, this.setValidateForm(this.state.fields));
			if (this.props.hasOwnProperty('onSubmit')) {
				this.props.onSubmit(newState);
			}
			// this.setState(newState);
		});
	}

	setValidateForm(fields){
		let inValid = 0, errors = [], newState = {};

		$.each(fields, (code, field) => {
			if(field.valid === false){
				inValid++;
				errors.push({[code]: field.errorMsg});
			}
		});

		if(inValid > 0){
			newState.valid = false;
			newState.submitedFail = true;
			newState.errors = errors;
		} else {
			newState.errors = [];
			newState.valid = true;
			newState.submitedFail = false;
		}

		return newState;
	}

	getChildContext() {
		return {
			changeField: (dataField) => {

				let {fields, values} = this.state;
				fields[dataField.name] = dataField;
				values[dataField.name] = dataField.value;
				if(dataField.value instanceof Object && dataField.value.hasOwnProperty('id')){
					values[dataField.name] = dataField.value.id;
				}

				let newState = {
					...this.state,
					fields,
					values
				};

				newState = Object.assign(newState, this.setValidateForm(fields));

				this.setState(newState);

				if (this.props.hasOwnProperty('onChange')) {
					this.props.onChange(newState);
				}
			},
			isClearForm: (status = false)=> {
				if (this.props.hasOwnProperty('isClear')){
					return this.props.isClear;
				}

				return status
			}
		}
	}

	render() {

		return (
			<form noValidate={this.props.noValidate}
				autoComplete={this.props.autoComplete}
				method={this.props.method}
				className={this.props.className}
				id={this.props.id}
				name={this.props.name}
				onSubmit={this.submit}
				onChange={this.change}>

				{this.props.children}

			</form>
		);
	}
}
Form.childContextTypes = {
	changeField: React.PropTypes.func,
	isClearForm: React.PropTypes.func
};

export default Form;