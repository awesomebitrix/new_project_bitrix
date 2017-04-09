/**
 * Created by dremin_s on 17.03.2017.
 */
/** @var o React */
/** @var o ReactDOM */
/** @var o is */
/** @var o $ */
"use strict";

const Tarnsformator = {

	getTransformVal(value, fn, params){
		if(this.hasOwnProperty(fn)){
			return this[fn](value);
		}

		return false;
	},

	toUpperCase(val = ''){
		return val.toUpperCase();
	},

	toLowerCase(val = ''){
		return val.toLowerCase();
	},

	trim(val = ''){
		return val.trim();
	},

	toNumber(val = 0){
		if(typeof val === 'number'){
			return parseInt(val);
		}

		return false;
	}
};

export default Tarnsformator;