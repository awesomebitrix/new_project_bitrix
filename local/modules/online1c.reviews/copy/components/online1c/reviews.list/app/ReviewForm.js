/**
 * Created by dremin_s on 14.03.2017.
 */
/** @var o React */
/** @var o ReactDOM */
/** @var o is */
/** @var o $ */
"use strict";
import { connect } from 'react-redux'
import { mapStateToProps, mapDispatchToProps } from './Contoller';
import Icon from 'UI/Icon';
import { Field, Form } from "UIForm";
import Preloader from 'preloader/Preloader';

class MainForm extends React.Component {
	constructor(props) {
		super(props);

		this.saveComment = this.saveComment.bind(this);
	}

	saveComment(form) {
		this.props.saveComment(form);
	}

	showField(code = '') {
		let res = {show: false, required: false};

		if (this.props.Data.Params) {
			if (this.props.Data.Params.hasOwnProperty('FIELDS')) {
				let codeFields = this.props.Data.Params.FIELDS, test;
				if (!BX.type.isArray(code)) {
					codeFields = [];
					$.each(this.props.Data.Params.FIELDS, (k, el) => {
						codeFields.push(el);
					})
				}

				test = codeFields.filter((el) => {
					return el === code;
				});

				if (test instanceof Array && test.length > 0) {
					res.show = true;
				}
			}

			if (this.props.Data.Params.hasOwnProperty('REQUIRED_FIELDS')) {
				let codeFields = this.props.Data.Params.REQUIRED_FIELDS;
				if (!BX.type.isArray(code)) {
					codeFields = [];
					$.each(this.props.Data.Params.REQUIRED_FIELDS, (k, el) => {
						codeFields.push(el);
					})
				}
				let required = codeFields.filter((el) => {
					return el === code;
				});

				if (required instanceof Array && required.length > 0) {
					res.required = ['isRequired'];
				}
			}
		}


		return res;
	}

	render() {

		const Data = this.props.Data.items;

		if (this.props.Data.Params === undefined)
			return null;

		let fioPlaceholder = Data.CURRENT_USER.LOGIN;
		if(is.empty(fioPlaceholder) || is.null(fioPlaceholder)){
			fioPlaceholder = 'Предаствтесь, пожалуйста';
		}

		return (
			<Form name="review_form_data" id="review_form_data" onSubmit={this.saveComment}>
				<Preloader {...this.props.Loader} />
				<div className="col-md-2">
					{/*
					 <div className="review__avatar icon-user">
					 <Icon name="user" noMargin={true} />
					 </div>
					 */}
				</div>
				<div className="col-md-10">
					{this.showField('FIO').show === true &&
					<div className="name__user review_form">
						<Field.String name="FIO" placeholder={fioPlaceholder}
							defaultValue={Data.CURRENT_USER.LOGIN} className="user_name_form"
							valid={this.showField('FIO').required} errorMsg="Предаствтесь, пожалуйста" />
					</div>
					}
					{this.showField('RATING_VAL').show === true &&
					<div className="user-eval">
						<span>Оцените товар:</span>
						<div className="stars-b">
							<div className="star-rating__wrap">

								<Field.RadioBox className="star-rating__input" id="star-rating-5" name="RATING_VAL" value="5" />
								<label className="star-rating__ico star-o fa-lg r1-unit" htmlFor="star-rating-5" title="5 out of 5 stars" />

								<Field.RadioBox className="star-rating__input" id="star-rating-4" name="RATING_VAL" value="4" />
								<label className="star-rating__ico star-o fa-lg r1-unit" htmlFor="star-rating-4" title="4 out of 5 stars" />

								<Field.RadioBox className="star-rating__input" id="star-rating-3" name="RATING_VAL" value="3" />
								<label className="star-rating__ico star-o fa-lg r1-unit" htmlFor="star-rating-3" title="3 out of 5 stars" />

								<Field.RadioBox className="star-rating__input" id="star-rating-2" name="RATING_VAL" value="2" />
								<label className="star-rating__ico star-o fa-lg r1-unit" htmlFor="star-rating-2" title="2 out of 5 stars" />

								<Field.RadioBox className="star-rating__input" id="star-rating-1" name="RATING_VAL" value="1" />
								<label className="star-rating__ico star-o fa-lg r1-unit" htmlFor="star-rating-1" title="1 out of 5 stars" />
							</div>
						</div>
					</div>
					}

					{this.showField('ADVANTAGE').show === true &&
					<Field.Text name="ADVANTAGE" id="review__msg__plus" className="review__msg__text"
						cols="30" rows="10" valid={this.showField('ADVANTAGE').required}
						placeholder="Плюсы:" errorMsg="Напишите сообщение" />
					}
					{this.showField('DISADVANTAGE').show === true &&
					<Field.Text name="DISADVANTAGE" id="review__msg__minus" className="review__msg__text"
						cols="30" rows="10" valid={this.showField('DISADVANTAGE').required}
						placeholder="Минусы:" errorMsg="Напишите сообщение" />
					}
					{this.showField('TEXT').show === true &&
					<Field.Text name="TEXT" id="review__msg__plus" className="review__msg__message"
						cols="30" rows="10" valid={this.showField('TEXT').required}
						placeholder="Комментарий:" errorMsg="Напишите сообщение" />
					}
					<div className="review_button_wrap">
						<button type="submit" className="btn btn-success">Ответить</button>
						&nbsp;&nbsp;
						<button type="button" className="btn btn-default" onClick={this.props.showCommentForm.bind(this, this.props.Data.showForm)}>
							Отменить
						</button>
					</div>
				</div>
			</Form>
		)
	}
}

export default connect(mapStateToProps, mapDispatchToProps)(MainForm);