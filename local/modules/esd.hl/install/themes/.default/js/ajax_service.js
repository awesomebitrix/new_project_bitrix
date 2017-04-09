angular.module('ajax.service', ['ngResource'])
	.provider('EAjax', function(){

		var options = {
			alertTime: false,
			preLoader: true
		};

		this.setUrl = function(val){
			var regexp = /[?]+/;
			var testUrl = regexp.test(val);
			if(testUrl){
				options.url = val + '&sessid='+ BX.bitrix_sessid();
			} else {
				options.url = val + '?sessid='+ BX.bitrix_sessid();
			}
		};
		this.setPreLoader = function(isLoader){
			options.preLoader = isLoader;
		};

		var _this = this;

		this.$get = [
			'$http','$q','$rootScope','$timeout','MsgForm',
			function($http, $q, $rootScope, $timeout, MsgForm) {

				var CAjax = function(){
					this.alerts = [];
					this.errors = {};

					var AjaxNow = this;

					this.getServiceUrl = function() {
						return options.url;
					};
					this.save = function(data){
						var defer = $q.defer();

						if(options.preLoader)
							$rootScope.$broadcast('showWait','show');

						AjaxNow.alerts = [];
						AjaxNow.errors = {};

						$http.post(this.get('url'), data, {cache: false}).success(function (result) {
							//console.info(result);
							var ajaxResult = {};
							if (result.DATA) {
								ajaxResult.DATA = result.DATA;
							} else {
								ajaxResult.DATA = null;
							}
							if (result.INFO || result.ERRORS) {
								ajaxResult.INFO = result.INFO;
								ajaxResult.ERRORS = result.ERRORS;
								var errors;

								if(ajaxResult.INFO)
									errors = ajaxResult.INFO;
								else if(ajaxResult.ERRORS)
									errors = ajaxResult.ERRORS;

								if(result.INFO && result.INFO.danger)
									AjaxNow.errors = MsgForm.getErrors(result.INFO.danger);

								AjaxNow.alerts = MsgForm.getMessages(errors);
								MsgForm.setEvent(AjaxNow);
							} else {
								ajaxResult.INFO = null;
								ajaxResult.ERRORS = null;
							}
							if(options.preLoader)
								$rootScope.$broadcast('showWait', false);

							defer.resolve(ajaxResult);
						});

						return defer.promise;
					};

					this.get = function(key){
						return options[key];
					};

					this.parseUrlQuery = function(path) {
						var data = {};
						if (path) {
							var pair = (path.substr(0)).split('&');
							for (var i = 0; i < pair.length; i++) {
								var param = pair[i].split('=');
								data[param[0]] = param[1];
							}
						}
						return data;
					};

					this.addParaToUrl = function(arParam) {
						var url = this.get('url'), newUrl = '';
						var obPath = this.parseUrlQuery(url);
						angular.forEach(arParam, function(val, key){
							obPath[key] = val;
						});
						newUrl = this.decodeUrl(obPath);
						options['url'] = newUrl;
						return newUrl;
					};

					this.decodeUrl = function(data){
						var url = '';
						angular.forEach(data, function(val, key){
							if(key != '' && val != undefined)
								url += key + '=' + val + '&';
						});
						return url;
					};

					this.addOption = function(key, val){
						options[key] = val;
					};

					this.getAlerts = function(){
						return this.alerts;
					};

					this.closeAlert = function(index){
						this.alerts.splice(index, 1);
						$rootScope.$broadcast('setAlerts', this.alerts);
					};

					this.getErrorField = function(){
						return this.errors;
					};
				};

				return new CAjax($http, options);
			}];
	})
	.factory('MsgForm', ['$timeout','$rootScope', function($timeout, $rootScope){
		var ErrorField = {}, arMessages = [];
		return {
			getErrors: function(errors){
				ErrorField = {};
				angular.forEach(errors, function(val, code){
					ErrorField[code] = val;
				});
				return ErrorField;
			},
			getMessages: function(messages){
				arMessages = [];
				angular.forEach(messages, function(value, type){
					if(type != 'danger'){
						var strMess = '';
						angular.forEach(value, function(msg, k){
							strMess += msg;
						});
						arMessages.push({
							type: type,
							msg: strMess
						});
					}
				});
				return arMessages;
			},
			setEvent: function(obEventData){
				$rootScope.$broadcast('setAlerts', obEventData);
			}
		}
	}]);