var HL = {};

HL.OptionApp = angular.module('OptionApp',['ngResource','ui.bootstrap','ajax.service']);
HL.OptionApp.config(function(EAjaxProvider){
	EAjaxProvider.setUrl('/local/modules/esd.hl/tools/ajax_admin.php');
});
HL.OptionApp.directive('hlMenu',['EAjax', function(EAjax){
	return {
		scope:{hlMenu:'@'},
		templateUrl: '/bitrix/js/esd.hl/d_templates/hl_edit_menu_item.php',
		replace: true,
		restrict: 'A',
		link: function($scope, element, attr){
			$scope.hlMenuItem.ID = attr.block;
			//$scope.hlMenuItem.entity = attr.entity;
		},
		controller: function($scope, EAjax){
			$scope.hlMenuItem = {
				change: false,
				item: $scope.hlMenu
			};

			$scope.saveMenuItem = function(){
				$scope.changeItem();
				var post = {
					DATA: $scope.hlMenuItem,
					CLASS: 'Esd\\HL\\Options',
					ACTION: 'changeMenu'
				};
				EAjax.save(post).then(function(result){
					if(result.INFO){
						$scope.infoResult = result.INFO;
						console.info($scope.infoResult);
						angular.forEach($scope.infoResult, function(value, k){
							var type = 'danger';
							if(value.type)
								type = value.type;

							$.bootstrapGrowl(value.msg, {
								type: type, // (null, 'info', 'error', 'success')
								offset: {from: 'top', amount: 100}, // 'top', or 'bottom'
								align: 'right', // ('left', 'right', or 'center')
								width: 300 // (integer, or 'auto')
							});
						});
					} else if(result.DATA){
						$.bootstrapGrowl('Данные сохранены', {
							type: 'success', // (null, 'info', 'error', 'success')
							offset: {from: 'top', amount: 100}, // 'top', or 'bottom'
							align: 'right', // ('left', 'right', or 'center')
							width: 300 // (integer, or 'auto')
						});
					}
				});
			};

			$scope.changeItem = function(){
				if($scope.hlMenuItem.change){
					$scope.hlMenuItem.change = false;
				} else {
					$scope.hlMenuItem.change = true;
				}
			};
		}
	}
}]);

HL.OptionApp.directive('hlMenuUrl',['EAjax', function(EAjax){
	return {
		scope:{hlMenuUrl:'@'},
		templateUrl: '/bitrix/js/esd.hl/d_templates/hl_edit_menu_item.php',
		replace: true,
		restrict: 'A',
		link: function($scope, element, attr){
			$scope.hlMenuItem.ID = attr.block;
			//$scope.hlMenuItem.entity = attr.entity;
		},
		controller: function($scope, EAjax){
			$scope.hlMenuItem = {
				change: false,
				item: $scope.hlMenuUrl
			};

			$scope.saveMenuItem = function(){
				$scope.changeItem();
				var post = {
					DATA: $scope.hlMenuItem,
					CLASS: 'Esd\\HL\\Options',
					ACTION: 'changeUrl'
				};
				//console.info(post);
				EAjax.save(post).then(function(result){
					if(result.INFO){
						$scope.infoResult = result.INFO;
						angular.forEach($scope.infoResult, function(value, k){
							var type = 'danger';
							if(value.type)
								type = value.type;

							$.bootstrapGrowl(value.msg, {
								type: type, // (null, 'info', 'error', 'success')
								offset: {from: 'top', amount: 100}, // 'top', or 'bottom'
								align: 'right', // ('left', 'right', or 'center')
								width: 300 // (integer, or 'auto')
							});
						});
					} else if(result.DATA){
						$.bootstrapGrowl('Данные сохранены', {
							type: 'success', // (null, 'info', 'error', 'success')
							offset: {from: 'top', amount: 100}, // 'top', or 'bottom'
							align: 'right', // ('left', 'right', or 'center')
							width: 300 // (integer, or 'auto')
						});
					}
				});
			};

			$scope.changeItem = function(){
				if($scope.hlMenuItem.change){
					$scope.hlMenuItem.change = false;
				} else {
					$scope.hlMenuItem.change = true;
				}
			};
		}
	}
}]);

HL.OptionApp.directive('hlFields',['EAjax', function(EAjax){
	return {
		scope: true,
		templateUrl: '/bitrix/js/esd.hl/d_templates/hl_option_fields.php',
		replace: true,
		restrict: 'AE',
		link: function($scope, element, attr){
			var obMenuBlocks = [];
			$scope.$watch('block.items', function(){
				if($scope.block.items){
					angular.forEach($scope.block.items, function(val, key){
						obMenuBlocks.push({
							TEXT: val.TITLE,
							SHOW_TITLE: false,
							ONCLICK: function(){
								$scope.getFields(val.ID);
								$scope.block.current = val.TITLE;
								$scope.block.currentId = val.ID;
							}
						});
					});
				}
			});

			element.find('#pw_select_hl').click(function(ev){
				BX.adminShowMenu(this, obMenuBlocks, {active_class: 'adm-btn-active'});
			});


		},
		controller: function($scope, EAjax){
			$scope.block = {
				current: 'Выбрать блок',
				currentId: null,
				items: {},
				fields: false,
				types: [],
				process: false,
				alerts:[]
			};

			$scope.getBlocks = function(){
				EAjax.save({CLASS:'Esd\\HL\\Options',ACTION:'getBlocks'}).then(function(result){
					$scope.block.items = result.DATA;
				});
			};

			$scope.getFields = function(id){
				EAjax.save({CLASS:'Esd\\HL\\Options',ACTION:'getOptionFields',DATA:{ ID:id }}).then(function(result){
					$scope.block.fields = result.DATA.FIELDS;
					$scope.block.types = result.DATA.TYPES;
				});
			};

			$scope.saveFieldsItems = function()
			{
				$scope.block.process = true;
				var post = {
					CLASS: 'Esd\\HL\\Options',
					ACTION: 'saveFieldsParam',
					DATA: {
						HL: $scope.block.currentId,
						ITEMS: {}
					}
				};
				angular.forEach($scope.block.fields, function(field, k){
					post['DATA']['ITEMS'][k] = field.fieldEdit;
				});

				EAjax.save(post).then(function(result){
					$scope.block.alerts = [];
					if(result.DATA){
						$scope.block.alerts.push(result.DATA);
					}
					$scope.block.process = false;
				});
			};

			$scope.getBlocks();
		}
	}
}]);

HL.OptionApp.directive('switcher', ['EAjax', function(EAjax){
	return {
		scope: {switcher: '@'},
		restrict: 'AE',
		link: function($scope, element, attr){
			element.find('.toggle').toggles({
				checkbox: element.find('.checkme'),
				on:attr.check
			}).click(function(){
				var check = element.find('.toggle-on').hasClass('active');
				var post = {
					CLASS:'Esd\\HL\\Options',
					ACTION: 'setOptionLog',
					DATA: {ID: $scope.switcher, CHECK: check}
				};
				EAjax.save(post).then(function(result){
					if(result.INFO){
						$scope.infoResult = result.INFO;
						angular.forEach($scope.infoResult, function(value, k){
							var type = 'danger';
							if(value.type)
								type = value.type;
							$.bootstrapGrowl(value.msg, {
								type: type, // (null, 'info', 'error', 'success')
								offset: {from: 'top', amount: 100}, // 'top', or 'bottom'
								align: 'right', // ('left', 'right', or 'center')
								width: 300 // (integer, or 'auto')
							});
						});
					}
				});
			});
		}
	}
}]);