/**
 * Created by dremin_s on 16.03.2017.
 */
/** @var o React */
/** @var o ReactDOM */
/** @var o is */
/** @var o $ */
"use strict";
import Valid from 'validator';

Valid.isArray = function (val) {
	return val instanceof Array;
};
Valid.isString = function (val) {
	return val instanceof String;
};

const Validator = {

	isValid(value, validator) {
		let v = 0, vFunc;
		if(Valid.isArray(validator)){
			validator.forEach((el) => {
				vFunc = this.getValidateFunc(el);
				if(vFunc.name !== null && !vFunc.name(value, vFunc.param)){
					v++;
				}
			});
		} else if(Valid.isString(validator)){
			vFunc = this.getValidateFunc(validator);
			if(vFunc.name !== null && !vFunc.name(value, vFunc.param)){
				v++;
			}
		}

		return v === 0;
	},

	regExp(value, regexp){
		let reg = new RegExp(regexp, 'ig');
		return reg.test(value);
	},

	getValidateFunc(validatorName){
		switch (validatorName){
			case 'isRequired':
				return {
					name: Valid.isLength,
					param: {min:1}
				};
				break;

			default:
				return {
					name: Valid.hasOwnProperty(validatorName) ? Valid[validatorName] : null
				};
				break;
		}
	}

};

export default Validator;