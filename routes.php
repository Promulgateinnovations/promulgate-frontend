<?php

use Pecee\Http\Request;
use Pecee\SimpleRouter\Exceptions\NotFoundHttpException;
use Pecee\SimpleRouter\SimpleRouter as Route;

/**
 * The default namespace for route-callbacks, so we don't have to specify it each time.
 * Can be overwritten by using the namespace config option on your routes.
 */
Route::setDefaultNamespace('\Promulgate\Controllers');

Route::get('/homepage', 'IndexController@showHomePage', [
	'middleware' => Promulgate\Middlewares\UserAuth::class,
])->name('homepage')
;

Route::get('/', 'IndexController@showHomePage', [
	'middleware' => Promulgate\Middlewares\UserAuth::class,
])->name('homepage')
;

Route::get('/privacy', 'IndexController@showPrivacy', [])->name('privacy');
Route::get('/terms', 'IndexController@showTerms', [])->name('terms');

Route::match(['get', 'post'], '/login', 'IndexController@showLogin', [
	'middleware' => Promulgate\Middlewares\UserAuth::class,
])->name('user_login')
;

Route::get('/logout', 'IndexController@showLogout', [
	'middleware' => Promulgate\Middlewares\UserAuth::class,
])->name('user_logout')
;
Route::post('/login/ajax', 'IndexController@processAjax')->name('login_ajax');

Route::group([
	'prefix'     => '/admin',
	'middleware' => Promulgate\Middlewares\UserAuth::class,
], function()
{

	Route::post('/ajax', 'AdminController@processAjax')->name('admin_ajax');

	Route::get('/organization', 'AdminController@showOrganizationDetails')
	     ->name('admin_organization')
	;
	Route::get('/organization/create', 'AdminController@CreateOrganizationDetails')
	     ->name('admin_organization_createnew')
	;
	// Route::get('/agency', 'AgencyController@showAgencyDetails')
	// ->name('admin_agency')
;
// Route::post('/agency', 'AgencyController@processAjax')->name('admin_ajax');

	Route::group([
		'prefix' => '/team',
	], function()
	{
		Route::get('/', 'AdminController@showTeam')
		     ->name('admin_team_list')
		;

		Route::get('/add', 'AdminController@showAddTeamMember')
		     ->name('admin_team_add_new_member')
		;
	});

	Route::get('/business', 'AdminController@showBusiness')
	     ->name('admin_business')
	;

	Route::get('/connections', 'AdminController@showConnections')
	     ->name('admin_connections')
	;

});




Route::group([
	'prefix'     => '/agency',
	'middleware' => Promulgate\Middlewares\UserAuth::class,
], function()
{

	Route::post('/ajax', 'AgencyController@processAjax')->name('agency_ajax');
	
	Route::get('/details', 'AgencyController@showAgencyDetails')
	->name('agency_details');

	Route::get('/agencyMember', 'AgencyController@showAgencyTeamMembers')
		     ->name('agency_team_members')
		;
	Route::get('/addMember', 'AgencyController@showAddAgencyMember')
			->name('agency_add_new_member')
	;

});

Route::group([
	'prefix'     => '/campaign',
	'middleware' => Promulgate\Middlewares\UserAuth::class,
], function()
{

	Route::post('/ajax', 'CampaignController@processAjax')->name('campaign_ajax');

	Route::get('/initiation', 'CampaignController@showInitiation')
	     ->name('campaign_initiation')
	;

	Route::get('/strategy_definition/{campaign_id?}', 'CampaignController@showStrategyDefinition')
	     ->name('campaign_strategy_definition')
	     ->where(['campaign_id' => '[a-zA-z0-9-]+'])
	;

	Route::get('/target_viewers/{campaign_id}', 'CampaignController@showTargetViewers')
	     ->name('campaign_target_viewers')
	     ->where(['campaign_id' => '[a-zA-z0-9-]+'])
	;

	Route::get('/channel_selection/{campaign_id}', 'CampaignController@showChannelSelection')
	     ->name('campaign_channel_selection')
	     ->where(['campaign_id' => '[a-zA-z0-9-]+'])
	;

	Route::get('/content_curation/{campaign_id}', 'CampaignController@showContentCuration')
	     ->name('campaign_content_curation')
	     ->where(['campaign_id' => '[a-zA-z0-9-]+'])
	;

	Route::get('/calendar/{campaign_id}', 'CampaignController@showCalendar')
	     ->name('campaign_calendar')
	     ->where(['campaign_id' => '[a-zA-z0-9-]+'])
	;
});

Route::group([
	'prefix'     => '/analytics',
	'middleware' => Promulgate\Middlewares\UserAuth::class,
], function()
{

	Route::post('/ajax', 'AnalyticsController@processAjax')->name('analytics_ajax');

	Route::get('/channels', 'AnalyticsController@showChannelsAnalysis')
	     ->name('analytics_channels')
	;

	Route::get('/competitors', 'AnalyticsController@showCompetitorAnalysis')
	     ->name('analytics_competitors')
	;

	Route::get('/mentions', 'AnalyticsController@showMentionsAnalysis')
	     ->name('analytics_mentions')
	;


	Route::get('/campaigns', 'AnalyticsController@showCampaignsAnalysis')
	     ->name('analytics_campaigns')
	;

	Route::get('/campaign_analysis/{campaign_id}', 'AnalyticsController@showCampaignsAnalysisDetails')
	     ->name('analytics_campaign_analysis')
	     ->where(['campaign_id' => '[a-zA-z0-9-]+'])
	;

	Route::get('/views', 'AnalyticsController@showViewershipAnalysis')
	     ->name('analytics_views')
	;

	Route::get('/youtube', 'AnalyticsController@showYoutubeAnalysis')
	     ->name('analytics_YouTube')
	;

});

Route::group([
	'prefix'     => '/oauth/callback',
	'middleware' => Promulgate\Middlewares\UserAuth::class,
], function()
{
	Route::get('/linkedin', 'OauthCallbackController@processLinkedIn')
	     ->name('oauth_linkedin_callback')
	;

});

/** ERROR/EXCEPTION handlers */
Route::error(function(Request $request, \Exception $exception)
{

	if($exception instanceof NotFoundHttpException && $exception->getCode() === 404) {

		response()->httpCode(404);
		$request->setRewriteCallback('ErrorController@showNotFound');
		return $request;
	}

});

Route::start();
