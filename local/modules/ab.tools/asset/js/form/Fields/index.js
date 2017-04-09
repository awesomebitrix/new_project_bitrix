/**
 * Created by dremin_s on 24.01.2017.
 */
/** @var o React */
/** @var o ReactDOM */
/** @var o is */
/** @var o $ */
"use strict";
import className from 'classnames';

const Transform = {
	toLowerCase (value, old){
		return value.toLowerCase();
	},

	toUpperCase (value, old){
		return value.toUpperCase();
	},

	toNumber (value, old){
		let num = Number(value);
		return isNaN(num) ? old : num;
	},

	trim (value, old){
		return value.trim();
	}
};

const Validator = {

	isValid(value, data) {
		this.value = value;
		this.noValid = 0;

		if (is.array(data)) {
			data.forEach((code) => {

				if (!is.regexp(code) && is.function(this[code])) {

					if (this[code]() === false)
						this.noValid++;

				} else if (is.regexp(code) === true) {

					if (this.regExp(code) === false)
						this.noValid++;

				}
			});
		} else if (is.string(data)) {
			if (is.function(this[data]) && !is.regexp(data)) {

				if (this[data]() === false)
					this.noValid++;

			} else if (is.function(this[data]) && is.regexp(data)) {

				if (this.regExp(data) === false)
					this.noValid++;
			}
		}

		return this.noValid <= 0;
	},

	required(){
		return this.value.length > 0;
	},

	email(){
		return is.email(this.value.toLowerCase());
	},

	num(){
		return /^[0-9]+$/.test(this.value);
	},

	string(){
		return /^[A-Za-zА-Яа-я]+$/.test(this.value);
	},

	regExp(re){
		return re.test(this.value);
	},
};

class ErrorField extends React.Component {
	constructor(props) {
		super(props);

	}

	static defaultProps = {
		data: [],
		show: false
	};

	render() {
		if (this.props.show !== true) {
			return null;
		}

		let classWrap = className('error_field_wrap', this.props.className);
		return (
			<span className={classWrap}>
				{this.props.data.map((el, k) => {
					return (<span className="error_field_item" key={'error_field_' + k}>{el.msg}</span>);
				})}
			</span>
		);
	}
}


class TextField extends React.Component {
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

	static defaultProps = {
		type: 'text',
		value: ''
	};

	change(ev) {
		let {value, name} = ev.target;
		let newState = {
			...this.state,
			value: this.transform(value),
			valid: this.validate(value),
			dirty: true,
			pristine: false,
			name: name,
		};

		if(this.props.hasOwnProperty('index')){
			newState.index = this.props.index;
		}

		this.setState(newState);
		if (this.props.hasOwnProperty('onChange') && is.function(this.props.onChange)) {
			this.props.onChange(newState);
		}

	}

	transform(value) {
		let newValue = value;
		if (this.props.transform instanceof Array) {
			this.props.transform.forEach((code) => {
				if (Transform.hasOwnProperty(code) && is.function(Transform[code])) {
					newValue = Transform[code](newValue, this.state.value);
				}
			});
		}

		return newValue;
	}

	validate(value) {
		return Validator.isValid(value, this.props.valid)
	}

	componentWillReceiveProps(nextProps) {
		if (nextProps.hasOwnProperty('value') && nextProps.value !== this.props.value) {
			let value = this.transform(nextProps.value);
			this.setState({
				...this.state,
				value: value,
				valid: this.validate(value),
				dirty: true,
				pristine: false,
				name: nextProps.name
			});
		}
	}

	getErrorMsg(errors) {
		let data = [];
		if (is.string(errors)) {
			data.push({msg: errors});
		} else if (is.array(errors)) {
			data = errors.map((el) => {
				return {msg: el};
			});
		}

		return <ErrorField data={data} show={!this.state.valid} />
	}

	componentDidMount () {
		if (this.props.hasOwnProperty('defaultValue')) {
			let newState = {
				...this.state,
				value: this.props.defaultValue,
				valid: this.validate(this.props.defaultValue),
				dirty: true,
				pristine: false,
				name: this.props.name
			};
			this.setState(newState);

			if (this.props.hasOwnProperty('onChange') && is.function(this.props.onChange)) {
				this.props.onChange(newState);
			}
		}
	}

	render() {
		let fieldClass = '';
		if(this.state.valid !== true)
			fieldClass = className(this.props.className, 'error_field_', this.props.errorClass);
		else
			fieldClass = this.props.className;

		return (
			<span className="field_form">
				<input {...this.props} value={this.state.value} onChange={this.change} className={fieldClass} />
				{this.getErrorMsg(this.props.errors)}
			</span>
		);
	}
}

class SelectField extends React.Component{
	constructor(props){
		super(props);

		this.state = {
			selected: {id: null, label: null, index: null}
		};

		this.setSelected = this.setSelected.bind(this);
	}

	static defaultProps = {
		values: [],
		id: BX.util.getRandomString(6) + '_'
	};

	setSelected(ev){
		let index = this.getIndexById(ev.target.value);
		let selected = this.props.values[index];
		selected.index = index;

		this.setState({selected});
		if(is.function(this.props.onChange)){
			this.props.onChange(selected);
		}
	}

	validate(value) {
		return Validator.isValid(value, this.props.valid)
	}

	componentWillReceiveProps(next){
		if(next.hasOwnProperty('selected') && next.selected.id !== this.state.selected.id){
			next.selected.index = this.getIndexById(next.selected.id, next.values);

			this.setState({selected: next.selected});
			if(is.function(this.props.onChange)){
				this.props.onChange(next.selected);
			}
		}
	}

	getIndexById(id, arValues = []){
		let index = null;
		if(arValues.length == 0)
			arValues = this.props.values;

		if(id != undefined){
			arValues.forEach((el, k) => {
				if(el.id == id) {
					index = k;
				}
			});
		}

		return index;
	}

	render(){
		return (
			<select {...this.props} onChange={this.setSelected}>
				{this.props.values.map((el, k) => {
					let key = el.id != undefined ?  this.props.id + el.id : this.props.id + k;
					let selected = this.props.selected.id === el.id ? 'selected' : null;

					return (<option key={key} selected={selected} value={el.id}>{el.label}</option>);
				})}
			</select>
		);
	}
}

class RadioField extends React.Component{
	constructor(props){
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

	validate(value) {
		return Validator.isValid(value, this.props.valid)
	}

	change(ev){
		let {value, name} = ev.target;
		let newState = {
			...this.state,
			value: value,
			valid: this.validate(value),
			dirty: true,
			pristine: false,
			name: name,
		};

		if(this.props.hasOwnProperty('index')){
			newState.index = this.props.index;
		}

		this.setState(newState);
		if (this.props.hasOwnProperty('onChange') && is.function(this.props.onChange)) {
			this.props.onChange(newState);
		}

	}

	componentWillReceiveProps(nextProps) {
		if (nextProps.hasOwnProperty('value') && nextProps.value !== this.props.value) {
			let value = nextProps.value;
			this.setState({
				...this.state,
				value: value,
				valid: this.validate(value),
				dirty: true,
				pristine: false,
				name: nextProps.name
			});
		}
	}

	componentDidMount () {
		if (this.props.hasOwnProperty('checked')) {
			let newState = {
				...this.state,
				value: this.props.value,
				valid: this.validate(this.props.value),
				dirty: true,
				pristine: false,
				name: this.props.name
			};
			this.setState(newState);

			if (this.props.hasOwnProperty('onChange') && is.function(this.props.onChange)) {
				this.props.onChange(newState);
			}
		}
	}

	static defaultProps = {
		value: false,
		name: '',
		classLabel: '',
		check:'',
	};

	render(){

		let check = this.props.check === this.state.value ? 'checked' : false;

		return (
			<input type="radio" name={this.props.name} className={this.props.className}
				id={this.props.id} checked={check} value={this.props.value} onChange={this.change}/>
		);
	}
}

class TextAreaField extends React.Component {
	constructor(props){
		super(props);

		this.state = {
			value: '',
			error: false,
			pristine: true,
			dirty: false,
			valid: true,
			name: '',
		};

		this.changeText = this.changeText.bind(this);
	}

	validate(value) {
		return Validator.isValid(value, this.props.valid)
	}

	changeText(ev) {
		let newState = {
			...this.state,
			valid: this.validate(ev.target.value),
			dirty: true,
			pristine: false,
			name: ev.target.name,
			value: ev.target.value
		};

		this.setState(newState);
		if (this.props.hasOwnProperty('onChange') && is.function(this.props.onChange)) {
			this.props.onChange(newState);
		}
	}

	getErrorMsg(errors) {
		let data = [];
		if (is.string(errors)) {
			data.push({msg: errors});
		} else if (is.array(errors)) {
			data = errors.map((el) => {
				return {msg: el};
			});
		}

		return <ErrorField data={data} show={!this.state.valid && !this.state.pristine} />
	}

	componentDidMount () {
		if (this.props.hasOwnProperty('defaultText')) {
			let newState = {
				...this.state,
				value: this.props.defaultText,
				valid: this.validate(this.props.defaultText),
				dirty: this.props.defaultText !== '',
				pristine:  this.props.defaultText === '',
				name: this.props.name
			};
			this.setState(newState);

			if (this.props.hasOwnProperty('onChange') && is.function(this.props.onChange)) {
				this.props.onChange(newState);
			}
		}
	}

	render(){
		let fieldClass = '';
		if(this.state.valid !== true)
			fieldClass = className(this.props.className, 'error_field_', this.props.errorClass);
		else
			fieldClass = this.props.className;

		return (
			<span className="field_form">
				<textarea onKeyPress={this.changeText} className={fieldClass} {...this.props}>{this.state.value}</textarea>
				{this.getErrorMsg(this.props.errors)}
			</span>
		);
	}
}



export {ErrorField};
export {SelectField};
export {TextField};
export {RadioField};
export {TextAreaField};