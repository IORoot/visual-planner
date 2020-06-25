<?php
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="vp.png">
<link rel="manifest" href="vp_manifest.json">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto" type="text/css"/>
<link rel="stylesheet" href="vip.css" type="text/css"/>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
<script src="https://apis.google.com/js/api.js"></script>
<script src="https://apis.google.com/js/platform.js"></script>
<script src="vp_config.js" type='text/javascript'></script>
<script src="vip_oauth.js" type='text/javascript'></script>
<script src="vip_lib.js" type='text/javascript'></script>
<script src="vp.js" type='text/javascript'></script>
<style>
body {
	font-family: Arial;
	height: 100vh;
	margin: 0;
	min-width: 600px;
	min-height: 400px;
	display: flex;
	flex-direction: column;
}
#account {
	font-family: Roboto;
	font-size: 0.8em;
	line-height: 2em;
	margin-right: 1em;
	text-align: right;
}
#banner {
	font-family: Roboto;
	font-size: 0.8em;
	text-align: center;
	line-height: 3em;
	min-height: 3em;
	background-color: #DAE4EB;
	position: relative;
}
#banner #bantxt {
	font-weight: 800;
	opacity: 0.6;
}
#buttons {
	top: 0;
	right: 1em;
	height: 100%;
	position: absolute;
}
#buttons .toolbtn {
	height: 100%;
	display: inline-flex;
	align-items: center;
	width: 2em;
}
.toolbtn img {
	opacity: 0.5;
}
.toolbtn img:hover {
	opacity: 1;
}
.staticbtn {
	position: fixed;
	top: 0.5em;
	right: 0.5em;
}
#home {
	flex-grow: 1;
	display: flex;
	flex-direction: column;
}
#grid {
	flex-grow: 1;
}
#calendarbar {
	font-size: 0.6em;
	min-height: 2.8em;
	background-color: gainsboro;
}
table {
	width: 100%;
	border-spacing: 1px;
	table-layout: fixed;
}
td.printhdr {
	text-align: center;
	padding: 1em;
}
td.printcell {
	padding: 0.2em;
}
td.printcellnum {
	font-size: 0.8em;
	text-align: center;
	width: 2em;
}
div.printevt {
	padding: 0.2em 0;
	word-wrap: break-word;
}
#settings {
	padding-left: 2em;
	padding-bottom: 4em;
}
#settings .sub {
	min-width: 50vw;
	padding-top: 1em;
	padding-left: 2em;
	padding-right: 2em;
}
#settings .group {
	padding-top: 3em;
}
#settings input[type=number] {
	width: 4em;
}
#settings .monthnames {
	width: 30em;
}
@media only print
{
	.noprint {
		display: none;
	}
}
</style>
</head>
<body ng-app="vp" ng-controller="main" ng-cloak>

<div ng-show="view=='home' || view=='settings'">
	<div id="account" class="noprint">{{sign_msg}}</div>
	<div id="banner" class="noprint">
		<span id="bantxt">{{settings.banner_text}}</span>
		<div id="buttons">
			<button ng-hide="form.$pristine || busy" ng-click="onclickSave()">Save</button>
			<button ng-show="(view=='settings') && !busy" ng-click="onclickCancel()">Cancel</button>
			<div class="toolbtn" ng-show="(view=='home') && !busy">
				<img src="print.png" title="Print View" draggable="false" ng-click="onclickPrintView()">
			</div>
			<div class="toolbtn" ng-show="(view=='home') && !busy">
				<img src="settings.png" title="Settings" draggable="false" ng-click="onclickSettings()">
			</div>
			<span ng-show="busy"><i>Saving...</i></span>
		</div>
	</div>
</div>
<div id="home" ng-show="view=='home'">
	<div id="grid"></div>
	<div id="calendarbar"></div>
</div>
<div id="printview" ng-show="view=='print'" ng-style="{'font-size': printinfo.fontsize}">
	<div class="toolbtn staticbtn noprint">
		<img src="close.png" title="Close" draggable="false" ng-click="onclickClosePrintView()">
	</div>
	<table>
		<thead>
			<tr><td class="printhdr" ng-repeat="hdr in printinfo.cols">{{hdr}}</td></tr>
		</thead>
		<tbody>
			<tr ng-repeat="row in printinfo.rows">
				<td class="printcell" ng-repeat="cell in row.cells" ng-style="{'background-color': cell.colour}">
					<table ng-repeat="day in cell.days">
						<tr>
							<td class="printcellnum">{{day.num}}</td>
							<td><div class="printevt" ng-repeat="evt in day.evts"><span ng-style="{color: evt.colour}">&#x25fc;</span>{{evt.title}}</div></td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div id="settings" ng-show="view=='settings'">
	<div class="sub group"><b>Account</b>
		<div class="sub">{{sign_msg}}</div>
		<div class="sub" ng-show="g_signbtn_ok"><div id="g_signbtn" class="g-signin2" data-width="300" data-height="60" data-longtitle="true"></div></div>
		<div class="sub" ng-show="signed_in"><a href="https://myaccount.google.com/permissions" target="_blank">Permissions</a></div>
		<div class="sub" ng-show="signed_in"><a href="https://drive.google.com/drive/my-drive" target="_blank">Manage Application Data</a>  (Drive > Settings > Manage Apps)</div>
		<div class="sub" ng-hide="signed_in || g_signbtn_ok"><button ng-click="onclickSignIn();">Sign In</button></div>
		<div class="sub" ng-show="signed_in"><button ng-click="onclickSignOut();">Sign Out</button></div>
	</div>
	<form name="form" ng-show="signed_in">
	<div class="sub group"><b>View</b>
		<div class="sub"><select ng-model="settings.vipconfig.multi_col_count"  ng-options="x for (x, y) in multi_col_count_options"></select>
			Number of columns to display.  More columns can be scrolled into view using the mouse wheel or cursor keys.
		</div>
		<div class="sub"><select ng-model="settings.vipconfig.multi_col_count_portrait"  ng-options="x for (x, y) in multi_col_count_options"></select>
			Number of columns to display in portrait mode on mobile devices.
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.auto_scroll" type="checkbox">
			Auto-scroll to current month.  If not selected, the view will always start at the beginning of the year.
			<div class="sub"><input ng-model="settings.vipconfig.auto_scroll_offset" type="number" min="-12" max="12">
				Auto-scroll offset.  For example, -2 will cause the date range to start from two months ago, 0 will start with the current month.
			</div>
			<div class="sub"><input ng-model="settings.vipconfig.first_month" type="number" min="1" max="12">
				First month of year.
			</div>
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.weekends">
			Weekends.  (0=Sun, 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat)
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.first_day_of_week" type="number" min="0" max="6">
			First day of week.  (0=Sun, 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat)
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.align_weekends" type="checkbox">
			Align weekends.
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.font_scale" type="number" min="0.2">
			Font scale.
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.past_opacity" type="number" min="0" max="1">
			Opacity of past months.  (1 = opaque)
		</div>
		<div class="sub"><input ng-model="settings.banner_text">
			Banner text.
		</div>
		<div class="sub"><input class="monthnames" ng-model="settings.vipconfig.month_names">
			Month names.
		</div>
	</div>
	<div class="sub group"><b>Events</b>
		<div class="sub"><input ng-model="settings.vipconfig.show_event_time" type="checkbox">
			Show event time.
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.show_event_title" type="checkbox">
			Show event title.
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.show_event_marker" type="checkbox">
			Show event marker.
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.colour_event_title" type="checkbox">
			Use event colour for title text.
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.proportional_events" type="checkbox">
			Display event markers in proportion to their actual duration.  Events outside the visible event range will not be shown.
			<div class="sub"><input ng-model="settings.vipconfig.proportional_start_hour" type="number" min="0" max="24">
				Start hour of visible event range.
			</div>
			<div class="sub"><input ng-model="settings.vipconfig.proportional_end_hour" type="number" min="0" max="24">
				End hour of visible event range.
			</div>
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.show_all_day_events" type="checkbox">
			Show 'all day' events.
			<div class="sub"><input ng-model="settings.vipconfig.single_day_as_multi_day" type="checkbox">
				Single day as multi-day (vertical bar).
			</div>
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.show_timed_events" type="checkbox">
			Show 'timed' events.
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.multi_day_as_single_day" type="checkbox">
			Show multi-day events as single-day events on each day.
			<div class="sub"><input ng-model="settings.vipconfig.first_day_only" type="checkbox">
				First day only.
			</div>
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.marker_width" type="number" min="0.2">
			Width of event marker.
		</div>
		<div class="sub"><input ng-model="settings.vipconfig.multi_day_opacity" type="number" min="0" max="1">
			Opacity of multi-day event.  (1 = opaque)
		</div>
	</div>
	</form>
	<div class="sub group" style="text-align: center;">
		<div class="sub">This software project is funded by voluntary donation.</div>
		<div class="sub"><form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
			<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
			</form>
		</div>
	</div>
	<div class="sub group"><a href="https://groups.google.com/group/visual-planner-discuss" target="_blank">visual-planner discussion group</a></div>
</div>

<script>
	var app = angular.module("vp", []);
	app.controller("main", vp_main);
</script>

</body>
</html>
