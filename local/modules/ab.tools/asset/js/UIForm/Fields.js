/**
 * Created by dremin_s on 14.03.2017.
 */
/** @var o React */
/** @var o ReactDOM */
/** @var o is */
/** @var o $ */
"use strict";
import cn from 'classnames';
import Validator from './Validators';
import Tarnsformator from './Tarnsformator';
import MaskedInput from 'react-maskedinput';

class BaseField extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			value: '',
			error: false,
			pristine: true,
			dirty: false,
			valid: true,
			name: '',
		};
		this.change = this.change.bind(this);
	}

	setValue(data = {}) {
		let validate =  data.validValue !== undefined ? this.setValid(data.validValue) : this.setValid(data.value);

		let newState = {
			...this.state,
			value: this.transform(data.value),
			valid: validate,
			dirty: true,
			pristine: data.pristine !== undefined ? data.pristine : false,
			name: data.name === undefined ? this.props.name : data.name,
		};

		if (newState.valid === false) {
			newState.error = true;
			if (this.props.hasOwnProperty('errorMsg')) {
				newState.errorMsg = this.props.errorMsg;
			}
		} else {
			newState.error = false;
		}

		if (newState.pristine === true) {
			newState.valid = true;
			newState.error = false;
		}

		if(newState.pristine === true && validate === false){
			newState.valid = false;
		}

		if (this.props.hasOwnProperty('index')) {
			newState.index = this.props.index;
		}

		this.setState(newState);
		if (this.props.hasOwnProperty('onChange') && is.function(this.props.onChange)) {
			this.props.onChange(newState);
		}

		if(is.function(this.context.changeField)){
			this.context.changeField(newState);
		}
	}

	setValid(val) {
		if (this.props.hasOwnProperty('valid') && this.props.valid !== null && this.props.valid !== false) {
			if (val === undefined || val === null)
				val = '';

			return Validator.isValid(val, this.props.valid)
		}

		return true;
	}

	subscribeForm() {
		let $field = $(ReactDOM.findDOMNode(this));
		let $form = $field.closest('form');

		$form.on('submit', (ev) => {
			this.setValue({value: this.state.value});
		});
	}

	unSubscribeForm() {
		//todo сделать отписку событий от родительской формы
		// let $field = $(ReactDOM.findDOMNode(this));
		// let $form = $field.closest('form');
		// $form.off('submit');
	}

	transform(val) {
		let newVal = this.state.value;

		if (this.props.hasOwnProperty('transform')) {
			newVal = Tarnsformator.getTransformVal(val, this.props['transform']);
			if (newVal !== false) {
				return newVal;
			}
		}

		return val;
	}

	change(ev) {
		let {value, name} = ev.target;
		this.setValue({name, value});
	}

	componentWillReceiveProps(nextProps) {

		if (nextProps.hasOwnProperty('defaultValue') && nextProps.defaultValue !== this.props.defaultValue) {
			this.setValue({value: nextProps.defaultValue, name: nextProps.name});
		}

		if (nextProps.hasOwnProperty('value') && nextProps.value !== this.props.value) {
			this.setValue({value: nextProps.value, name: nextProps.name});
		}

	}

	componentDidMount() {
		let state = {
			name: this.props.name,
			value: ''
		};
		if (this.props.hasOwnProperty('defaultValue')) {
			state.value = this.props.defaultValue;
		}
		state.pristine = true;
		state.error = false;
		this.setValue(state);

		this.subscribeForm();
	}

	componentWillUnmount() {
		// todo сделать отписку событий от родительской формы
	}

	getFieldClass() {
		let fieldClass = '';

		if (this.state.valid !== true && !this.state.pristine)
			fieldClass = cn(this.props.className, 'control_error', this.props.errorClass);
		else
			fieldClass = this.props.className;

		return fieldClass;
	}

	getErrorMessage() {
		if (this.state.error === true && this.props.hasOwnProperty('errorMsg')) {
			if (typeof this.props.errorMsg === 'string') {
				return (
					<span className="error_field_wrap animated slideInUp">
						{this.state.errorMsg}
						<i className="fa fa-caret-down" />
					</span>
				);
			} else if (React.isValidElement(this.props.errorMsg)) {
				return this.props.errorMsg;
			}

		}

		return null;
	}
}
BaseField.contextTypes = {
	changeField: React.PropTypes.func,
	isClearForm: React.PropTypes.func
};

class String extends BaseField {
	constructor(props) {
		super(props);

	}

	static defaultProps = {
		type: 'text',
		value: '',
		errorClass: '',
		disabled: false
	};

	setValid(val) {
		if (this.props.hasOwnProperty('regExp')) {
			return Validator.regExp(val, this.props.regExp);
		}

		return super.setValid(val);
	}

	render() {

		let fieldClass = this.getFieldClass();

		return (
			<span className="field_form_wrap">
				<input type={this.props.type}
					value={this.state.value}
					name={this.props.name}
					onChange={this.change}
					className={fieldClass}
					placeholder={this.props.placeholder}
					disabled={this.props.disabled}
					maxLength={this.props.maxlength}/>
				{this.getErrorMessage()}
			</span>
		)
	}
}

class Mask extends BaseField {

	constructor(props) {
		super(props);
	}

	transform(val) {
		return val;
	}

	render() {
		let fieldClass = this.getFieldClass();
		let className = cn(fieldClass, 'data_mask');

		return  (
			<span className="field_form_wrap">
				 <MaskedInput
					 mask={this.props.mask}
					 name={this.props.name}
					 placeholder={this.props.placeholder}
					 onChange={this.change}
					 className={className}
					 defaultValue={this.props.defaultValue} disabled={this.props.disabled}
					 maxLength={this.props.maxlength}
				 />
				{this.getErrorMessage()}
			</span>
		)
	}
}
Mask.propTypes = {
	name: React.PropTypes.string.isRequired,
	mask: React.PropTypes.string.isRequired
};

class Select extends BaseField {
	constructor(props) {
		super(props);

		this.state = {
			value: '',
			error: false,
			pristine: true,
			dirty: false,
			valid: true,
			name: '',
			items: []
		};
	}

	static defaultProps = {
		errorClass: '',
		name: '',
		id: '',
		items: []
	};

	// setValid(value){
	// 	return true;
	// }

	setValue(data = {}, items = []) {

		if (items.length === 0) {
			items = this.compileItems();
		}

		let value = items.filter((el) => {
			return el.id === data.value;
		}).shift();

		if (value === undefined)
			value = {id: null, label: null};

		super.setValue({value, name: data.name, validValue: value.id});
	}

	change(ev) {
		let {name, value} = ev.target;
		this.setValue({name, value})
	}

	compileItems() {
		let stateItems = [];

		if (this.props.items.length === 0) {
			stateItems = React.Children.map(this.props.children, (el) => {
				return {id: el.props.value, label: el.props.children};
			});
			this.setState({items: stateItems});
		} else {
			stateItems = this.props.items;
			this.setState({items: stateItems});
		}

		return stateItems;
	}

	componentDidMount() {

		let items = this.compileItems();

		if (this.props.hasOwnProperty('defaultValue')) {
			if (this.props.defaultValue !== false)
				this.setValue({value: this.props.defaultValue, name: this.props.name}, items);
		}
	}

	componentWillReceiveProps(nextProps) {
		if (nextProps.hasOwnProperty('items') && nextProps.items.length !== this.props.items.length) {
			this.setState({items: nextProps.items});
		}

		if (nextProps.hasOwnProperty('value') && nextProps.value.id !== this.props.value.id) {
			this.setValue({value: nextProps.value, name: nextProps.name});
		}

		if (nextProps.hasOwnProperty('selected')
			&& nextProps.hasOwnProperty('items')
			&& nextProps.items.length > 0
			&& !this.state.value.hasOwnProperty('id')) {
			this.setValue({value: nextProps.selected, name: nextProps.name});
		}

	}

	render() {
		let options = [];

		if(this.state.items !== undefined){
			if (this.state.items.length > 0) {
				options = this.state.items.map((el, i) => {
					if (typeof el === 'object') {
						let selected = this.state.value.id === el.id ? 'selected' : false;
						return <option selected={selected} key={this.props.name + el.id} value={el.id}>{el.label}</option>
					} else if (typeof el === 'string') {
						let selected = this.state.value.id === i ? 'selected' : false;
						return <option selected={selected} key={this.props.name + i} value={i}>{el}</option>
					}
				});
			} else {
				options = this.props.children;
			}

		}

		let fieldClass = this.getFieldClass();

		return (
			<span className="field_form_wrap">
				<select name={this.props.name} id={this.props.id} onChange={this.change} className={fieldClass}>
					{options}
				</select>
				{this.getErrorMessage()}
			</span>
		);
	}
}

class Checkbox extends BaseField {
	constructor(props) {
		super(props);
	}

	change(ev) {
		let {value, name} = ev.target;
		if (this.state.value === value) {
			if (typeof value === 'string') {
				value = false;
			} else if (typeof value === 'number') {
				value = 0;
			} else {
				value = !this.state.value;
			}
		} else {
			value = this.props.value;
		}
		this.setValue({name, value});
	}

	componentDidMount() {
		if (this.props.hasOwnProperty('checked')) {
			this.setValue({value: this.props.value, name: this.props.name});
		}
	}

	render() {
		let fieldClass = this.getFieldClass();
		let checked = this.state.value === this.props.value ? 'checked' : false;

		return (
			<span className="field_form_wrap">
				<input type="checkbox" name={this.props.name}
					className={fieldClass} id={this.props.id}
					onChange={this.change} value={this.props.value} disabled={this.props.disabled} checked={checked} />
				{this.getErrorMessage()}
			</span>
		);
	}
}

class RadioBox extends Checkbox {
	constructor(props) {
		super(props);

	}

	render() {
		let fieldClass = this.getFieldClass();
		let checked = this.state.value === this.props.value ? 'checked' : false;

		return (
			<input type="radio" name={this.props.name}
				className={fieldClass} id={this.props.id}
				onChange={this.change} value={this.props.value}
				disabled={this.props.disabled} checked={checked} />
		);
	}
}

class Text extends BaseField {
	constructor(props) {
		super(props);

	}

	static defaultProps = {
		value: '',
		errorClass: '',
		disabled: false,
		cols: 30,
		rows: 10
	};

	change(ev) {
		super.change(ev);

		if (ev.target.clientHeight < ev.target.scrollHeight) {
			this.setState({height: ev.target.scrollHeight + 20});
		}

	}

	render() {
		let fieldClass = this.getFieldClass();
		let styleHeight = {};
		if (this.state.height !== undefined) {
			styleHeight.height = this.state.height + 'px';
		}
		return (
			<span className="field_form_wrap">
				<textarea value={this.state.value} style={styleHeight}
					name={this.props.name}
					onChange={this.change}
					className={fieldClass}
					disabled={this.props.disabled}
					cols={this.props.cols} rows={this.props.rows} placeholder={this.props.placeholder} />
				{this.getErrorMessage()}
			</span>
		)
	}
}

export { String, Select, Checkbox, RadioBox, Text, Mask };