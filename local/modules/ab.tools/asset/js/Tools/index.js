/** @var o React */
/** @var o ReactDOM */
/** @var o $ */

const print_r = (arr, level) => {
	let print_red_text = "";
	if (!level) level = 0;
	let level_padding = "";
	for (let j = 0; j < level + 1; j++) level_padding += "    ";
	if (typeof(arr) == 'object') {
		for (let item in arr) {
			let value = arr[item];
			if (typeof(value) == 'object') {
				print_red_text += level_padding + "'" + item + "' :\n";
				print_red_text += print_r(value, level + 1);
			}
			else
				print_red_text += level_padding + "'" + item + "' : \"" + value + "\"\n";
		}
	}

	else  print_red_text = "===>" + arr + "<===(" + typeof(arr) + ")";
	return print_red_text;
};


class Print extends React.Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<code className={this.props.className}>
				<pre>{print_r(this.props.data)}</pre>
			</code>
		);
	}
}

const animateCss = function() {
	return $.fn.extend({
		animateCss: function (animationName) {
			let animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
			this.addClass('animated ' + animationName).one(animationEnd, function() {
				$(this).removeClass('animated ' + animationName);
			});

			return this;
		}
	});
}(jQuery);


const RandString = (length = 8, charsString = '') => {
	let chars = '';

	switch (charsString){
		case 'number':
		case 'num':
			chars = '0123456789';
			break;
		case 'string':
		case 'str':
			chars = 'ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz';
			break;
		case 'all':
		case '':
			chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz';
			break;
		default:
			chars = charsString;
	}
	let charQty = chars.length;

	length = parseInt(length);
	if(isNaN(length) || length <= 0)
	{
		length = 8;
	}

	let result = "";
	for (let i = 0; i < length; i++)
	{
		result += chars.charAt(Math.floor(Math.random() * length));
	}

	return result;
};



export {
	print_r,
	Print,
	RandString,
	animateCss
};
