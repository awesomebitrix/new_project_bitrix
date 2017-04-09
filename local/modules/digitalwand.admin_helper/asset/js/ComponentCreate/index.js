/**
 * Created by dremin_s on 14.03.2017.
 */
/** @var o React */
/** @var o ReactDOM */
/** @var o is */
/** @var o $ */
"use strict";
import { connect, Provider } from "react-redux";
import Store from "./Store";
import { mapDispatchToProps, mapStateToProps } from "./Controller";
import cn from "classnames";
import { Field, Form } from "UIForm";
import Preloader from 'preloader/Preloader';

class Creator extends React.Component {
	constructor(props) {
		super(props);

	}

	render() {
		console.info(this.props);
		let leftCol = 3, rightCol = 9,
			smLeftCol = 'col-sm-' + leftCol,
			smRightCol = 'col-sm-' + rightCol,
			leftOffset = cn('col-sm-offset-' + leftCol, smRightCol),
			classLabel = cn(smLeftCol, 'control-label');

		const {Data} = this.props;
		const action = this.props.api;

		let namespaces = [{id: null, label: ' - Выбрать -'}];
		Data.namespaces.forEach((el, i) => {
			namespaces.push({id: el, label: el});
		});

		let showNpmParams = {};

		if(Data.useNpm === null){
			showNpmParams.style = {display: 'none'};
		}
		if(Data.useNpm === true){
			showNpmParams.style = {display: 'block'};
			showNpmParams.className = 'animated fadeInUp';
		} else {
			showNpmParams.className = 'animated fadeOutDown';
			showNpmParams.style = {display: 'none'};
		}

		let validateReactApp = Data.useNpm === true ? ['isRequired'] : false;

		return (
			<div className="create_component container">
				<Preloader {...this.props.Loader} />
				<div className="row">
					<Form className="form-horizontal" id="form_component" onSubmit={action.submitForm}>
						<div className="form-group">
							<label htmlFor="cmp_folder" className={classLabel}>Корневая папка</label>
							<div className={smRightCol}>
								<Field.Select name="FOLDER" id="cmp_folder"
									onChange={action.getNamespace} className="form-control"  defaultValue="local">
									<option value='local'>local</option>
									<option value='bitrix'>bitrix</option>
								</Field.Select>
							</div>
						</div>
						{namespaces.length > 0 &&
						<div className="form-group">
							<label htmlFor="cmp_namespace_new" className={classLabel}>Просранство компонента</label>
							<div className={smRightCol}>
								<Field.Select items={namespaces} className="form-control" name="NAMESPACE" id="cmp_namespace" />
								<br />
								<Field.String className="form-control" id="cmp_namespace_new"
									placeholder="Если его еще нет, то можно создать" name="NAMESPACE_NEW"/>
							</div>
						</div>
						}
						<div className="form-group">
							<label htmlFor="cmp_name" className={classLabel}>Название компонента</label>
							<div className={smRightCol}>
								<Field.String className="form-control" id="cmp_name" name="NAME"
									valid={['isRequired']} errorMsg="Поле должно содержать только латинские символы и знаки . и _ "
									regExp="^[A-Za-z0-9._]+$"/>
							</div>
						</div>
						<div className="form-group">
							<label htmlFor="cmp_class" className={classLabel}>Класс компонента</label>
							<div className={smRightCol}>
								<Field.String className="form-control" id="cmp_class"
									placeholder="\Esd\Main\MyClass" name="CLASS_NAME"
									valid={['isRequired']} errorMsg="Поле должно содержать только латинские символы и знак \"
									regExp="^[A-Za-z0-9\\]+$"/>
							</div>
						</div>
						<div className="form-group">
							<label htmlFor="cmp_template" className={classLabel}>Шаблон</label>
							<div className={smRightCol}>
								<Field.String className="form-control" id="cmp_template"
									placeholder=".default" name="TEMPLATE" defaultValue=".default"
									regExp="^[a-zA-Z0-9._]+$" errorMsg="Название папки должно содержать только латинские буквы и знаки . и _ "/>
							</div>
						</div>
						<div className="form-group">
							<div className={leftOffset}>
								<div className="checkbox">
									<label>
										<Field.Checkbox name="USE_NPM" id="cmp_npm" value="Y" onChange={action.useNpm}/>
										Создать болванку для react.js
									</label>
								</div>
							</div>
						</div>
						<div className={showNpmParams.className} style={showNpmParams.style}>
							<div className="heading_title">
								Праметры react-приложения
							</div>
							<div className="form-group">
								<label htmlFor="cmp_npm_name" className={classLabel}>Название приложения</label>
								<div className={smRightCol}>
									<Field.String className="form-control" id="cmp_npm_name"
										placeholder="App" name="APP_NAME" valid={validateReactApp} errorMsg="Введите имя приложения"/>
								</div>
							</div>
							<div className="form-group">
								<div className={leftOffset}>
									<div className="checkbox">
										<label>
											<Field.Checkbox name="ADD_REDUX" id="cmp_npm" value="Y"/>
											Создать болванку для redux
										</label>
									</div>
								</div>
							</div>
						</div>
						<div className="form-group">
							<div className={leftOffset}>
								<button type="submit" className="btn btn-success">Создать компонент</button>
							</div>
						</div>
					</Form>
				</div>
			</div>
		);
	}
}

const CreatorWrap = connect(mapStateToProps, mapDispatchToProps)(Creator);

$(function () {
	ReactDOM.render(<Provider store={Store()}><CreatorWrap /></Provider>, BX('admin_component_create'));
});