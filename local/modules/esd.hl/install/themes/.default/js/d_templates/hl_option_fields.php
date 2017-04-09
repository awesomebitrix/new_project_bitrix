<div class="set_hl_fields">
	<alert ng-repeat="alert in block.alerts" type="{{alert.type}}">{{alert.msg}}</alert>
	<div class="btn-group">
		<a href="javascript:void(0)" class="adm-btn adm-btn-menu" id="pw_select_hl">{{block.current}}</a>
	</div>
	<div class="clear_20"></div>
	<table class="table table-striped table-hover table-bordered table_esd" ng-show="block.fields">
		<thead>
		<tr class="warning">
			<th>asdasdas</th><th>ааа</th>
		</tr>
		</thead>
		<tbody>
		<tr ng-repeat="row in block.fields">
			<td>{{row.EDIT_FORM_LABEL}} [{{row.USER_TYPE.DESCRIPTION}} - {{row.USER_TYPE.BASE_TYPE}}]</td>
			<td>
				<select	ng-model="row.fieldEdit"
						ng-options="item as item.title for item in block.types track by item.type">
				</select>
			</td>
		</tr>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="2">
				<div class="hl_btn_row_group">
					<button type="button" role="button" class="btn btn-success"
							ng-click="saveFieldsItems()" ng-show="!block.process">Выбрать блок</button>
				</div>
			</td>
		</tr>
		</tfoot>
	</table>
</div>