/**
 * Created by Grandmaster.
 */
/** @var o React */
/** @var o ReactDOM */
/** @var o is */
/** @var o $ */
"use strict";
import { connect, Provider } from "react-redux";
import Store from "./Store";
import { mapDispatchToProps, mapStateToProps } from "./Controller";

class #APP_NAME# extends React.Component {
	constructor(props) {
		super(props);

	}

	componentWillReceiveProps(nextProps){

	}

	componentDidMount () {

	}

	render() {

		console.info(this.props);

		return (
			<div className="test_component">
				<h3>TEST APP</h3>
			</div>
		);
	}
}

const MyAppWrap = connect(mapStateToProps, mapDispatchToProps)(MyApp);

$(function () {
	ReactDOM.render(<Provider store={Store()}><#APP_NAME# /></Provider>, BX('root_node'));
});