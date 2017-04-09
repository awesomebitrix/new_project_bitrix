/**
 * Created by dremin_s on 07.03.2017.
 */
/** @var o React */
/** @var o ReactDOM */
/** @var o is */
/** @var o $ */
"use strict";
import { connect } from 'react-redux'
import { mapStateToProps, mapDispatchToProps } from './Contoller';
import Icon from 'UI/Icon';
import classNames from 'classnames';
import FormReview from './ReviewForm';
import Preloader from 'preloader/Preloader';

class ReviewItem extends React.Component {
	constructor(props) {
		super(props);

		this.addLike = this.addLike.bind(this);
		this.addDisLike = this.addDisLike.bind(this);

	}

	static defaultProps = {
		item: {},
		arParams: {}
	};

	getHtml(html = '') {
		return {__html: html};
	}

	getRatingVal(val = 0) {
		let stars = [], limit = 5, isNulls = limit - val;
		for(let i = 1; i <= val; i++){
			stars.push(<Icon name="star" />);
		}

		for(let i = 1; i <= isNulls; i++){
			stars.push(<Icon name="star-o" />);
		}

		return stars;
	}

	addLike(element = null){
		// console.info(element, this.props);
		if(element !== null && element !== undefined){
			this.props.addLike({
				id: element.ID,
				status: '+'
			});
		}
	}

	addDisLike(element = null){
		if(element !== null && element !== undefined){
			if(this.props.arParams.hasOwnProperty('DISLIKE_START')){
				let cnt = parseInt(this.props.arParams.DISLIKE_START);
				if(cnt > 0 && element.LIKE <= cnt){
					swal('','Сначала должно набраться '+ cnt + ' лайков, а потом можно ставить дизлайки :)', 'error');
					return;
				}
			}

			this.props.addDisLike({
				id: element.ID,
				type: '-'
			});
		}
	}

	render() {

		const Item = this.props.item;
		const {arParams} = this.props;

		if (is.empty(Item)) {
			return null;
		}

		let classBlockReview = classNames('text_block', {'col-md-11': arParams.SHOW_AVATARS === 'Y'});

		return (
			<div className="review_item col-md-12">
				{arParams.SHOW_AVATARS === 'Y' &&
				<div className="col-md-1">
					<div className="review__avatar icon-user">
						{Item.AVATAR !== undefined ?
							<img src={Item.AVATAR.src} className="img-responsive" />
							:
							<Icon name="user" noMargin={true} />
						}

					</div>
				</div>
				}
				<div className={classBlockReview}>
					<div className="header__review">
						<span className="name__user">{Item.FIO}</span>
						<span className="star_block">{this.getRatingVal(Item.RATING_VAL)}</span>
					</div>
					<div className="date-review"><span className="date-review__text">{Item.DATE_CREATE}</span></div>
					<div className="review__text">
						{Item.ADVANTAGE.length > 0 &&
						<div className="review-positive aspect-b">
							<span className="review-positive__title">Плюсы</span>
							<div dangerouslySetInnerHTML={this.getHtml(Item.ADVANTAGE)} />
						</div>
						}
						{Item.DISADVANTAGE.length > 0 &&
						<div className="review-negative aspect-b">
							<span className="review-negative__title">Минусы</span>
							<div dangerouslySetInnerHTML={this.getHtml(Item.DISADVANTAGE)} />
						</div>
						}
						<div className="aspect-b">
							<span className="review-message__title">Комментарий</span>
							<div dangerouslySetInnerHTML={this.getHtml(Item.TEXT)} />
						</div>
						{arParams.SHOW_LIKES === 'Y' &&
						<div className="raiting-b">
							<span className="raiting__minus icon-dislike" onClick={this.addDisLike.bind(this, Item)}>
								<Icon name="thumbs-o-down" />{Item.DISLIKE}
							</span>
							<span className="raiting__plus icon-like" onClick={this.addLike.bind(this, Item)}>
								<Icon name="thumbs-o-up" />{Item.LIKE}
							</span>
						</div>
						}
					</div>
				</div>
			</div>
		);
	}
}
ReviewItem.propTypes = {
	item: React.PropTypes.object,
	arParams: React.PropTypes.object,
	addDisLike: React.PropTypes.func,
	addLike: React.PropTypes.func,
};

export {ReviewItem};

class Review extends React.Component {
	constructor(props) {
		super(props);
	}

	static defaultProps = {};

	componentWillReceiveProps(nextProps) {
		if (nextProps.Data.showForm !== this.props.Data.showForm) {
			if (nextProps.Data.showForm === true) {
				$('#review_form').slideDown(400, function () {
					let offsetTop = $(this).offset().top;
					$('html, body').animate({scrollTop: offsetTop}, 300);
				});
			} else {
				$('#review_form').slideUp(200);
			}
		}
	}

	componentDidMount() {
		this.props.getParameters();
		if (is.empty(this.props.Data.items)) {
			this.props.getReviewList(1);
		}
	}

	render() {
		// console.info(this.props);
		const {Data} = this.props;
		const Items = Data.items;
		const arParams = this.props.Data.Params;

		if (is.empty(Items)) {
			return <Preloader {...this.props.Loader} />;
		}

		if (this.props.Data.Params === undefined)
			return null;

		return (
			<div>
				<div className="reviews-block">
					<h4>{Items.NAME_BLOCK} <span className="title-b__sub__text">({Items.CNT_FORMAT})</span></h4>
					{arParams.LIST_ONLY !== 'Y' &&
					<div className="add-review-b" style={{display: classNames({'none': Data.showForm})}}>
						<a href="javascript:" className="btn btn-success" onClick={this.props.showCommentForm.bind(this, Data.showForm)}>
							<Icon name="pencil" />Написать
						</a>
					</div>
					}

					{Items.ITEMS &&
					Items.ITEMS.map((el) => {
						return <ReviewItem item={el} arParams={this.props.Data.Params}
							addLike={this.props.actionLike} addDisLike={this.props.actionLike}/>;
					})
					}
				</div>

				<div className="reply-b" id="review_form">
					{Data.showForm === true && arParams.LIST_ONLY !== 'Y' && <FormReview />}
				</div>
				{Items.ITEMS && arParams.LIST_ONLY !== 'Y' &&
				<div className="add-review-b" style={{display: classNames({'none': Data.showForm})}}>
					<a href="javascript:" className="btn btn-success" onClick={this.props.showCommentForm.bind(this, Data.showForm)}>
						<Icon name="pencil" />Написать
					</a>
				</div>
				}
			</div>
		)
	}
}

export default connect(mapStateToProps, mapDispatchToProps)(Review);