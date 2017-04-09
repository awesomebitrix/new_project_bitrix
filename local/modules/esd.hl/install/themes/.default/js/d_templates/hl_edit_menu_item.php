<div class="hl_opt_menu_item">
	<form class="form-inline" ng-if="hlMenuItem.change" ng-submit="saveMenuItem()">
		<div class="form-group">
			<input type="text" class="form-control" value="" ng-model="hlMenuItem.item" ng-init="hlMenu"/>
		</div>
		<button type="submit" class="btn btn-default btn-sm">OK</button>
	</form>
	<span class="hl_item_menu_name">{{hlMenuItem.item}}</span>
	<a href="javascript:" class="esd_hl_ajax_link" ng-click="changeItem()">
		<span class="item_link">фывфыв</span>
	</a>
</div>