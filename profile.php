<?php
/**
 * profile
 * 
 * @package Sngine
 * @author Zamblek
 */

// fetch bootstrap
require('bootstrap.php');

// user access
if(!$system['system_public']) {
	user_access();
}

// check username
if(is_empty($_GET['username']) || !valid_username($_GET['username'])) {
	_error(404);
}

try {

	// [1] get main profile info
	$get_profile = $db->query(sprintf("SELECT users.*, packages.name as package_name, packages.color as package_color, 
	T_Person.*, D_Position.Posicion, 
	cast(Datediff (NOW(),Birthdate)/365 as int) as Age,
	D_SeniorityLevel.SeniorityLevel as Seniority
	FROM users 
	LEFT JOIN packages ON users.user_subscribed = '1' AND users.user_package = packages.package_id 
	LEFT Join T_Person on ID_Person = user_id
	LEFT JOIN D_Position on T_Person.ID_Position_Title = D_Position.ID
	LEFT JOIN D_SeniorityLevel on D_SeniorityLevel.ID = T_Person.SeniorityLevel
	WHERE users.user_name = %s", secure($_GET['username']))) or _error(SQL_ERROR_THROWEN);
	if($get_profile->num_rows == 0) {
		_error(404);
	}
	$profile = $get_profile->fetch_assoc();

	
	
					$get_educationinfo = $db->query(sprintf("SELECT 
															T_Education.ID,
															T_Education.ID_Person, 
															D_University.University,
															D_EducationGrade.Grade,
															D_MacroSubject.MacroSubject,
															D_Country.Name as Country,
															D_Language.Idioma, 
															T_Education.StartDate,
															T_Education.EndDate,
															T_Education.AverageExams_Mark,
															Case when T_Education.Experimental_Thesis = 1 then 'Sì' else 'No' end as Experimental_Thesis,
															round(T_Education.Completion_Percentage*100,0) as Completion_Percentage,
															round(datediff (enddate, startdate)/365,0) as CompletionTime,
															Year(T_Education.StartDate) as StartYear,
															Year(T_Education.EndDate) as EndYear
															
						from T_Education
						Join users on users.user_id = T_Education.ID_Person
						Join D_University on D_University.ID = T_Education.ID_University
						Join D_EducationGrade on D_EducationGrade.ID_Grade = T_Education.ID_GradeType
						Join D_MacroSubject on D_MacroSubject.ID = T_Education.ID_MacroSubject
						Join D_Country on D_Country.ID = T_Education.ID_Country
						Join D_Language on D_Language.ID = T_Education.ID_Language
						WHERE users.user_name = %s", secure($_GET['username']))) or _error(SQL_ERROR_THROWEN);
					if($get_educationinfo->num_rows == 0) {
							_error(404);
						}
					// $educationinfo = $get_educationinfo->fetch_assoc();

					if($get_educationinfo->num_rows > 0) {
								while($education = $get_educationinfo->fetch_assoc()) {
									$educations[] = $education;
								}
							}
	

		

	/* check if banned by the system */
	if($user->banned($profile['user_id'])) {
		_error(404);
	}
	/* check if blocked by the viewer */
	if($user->blocked($profile['user_id'])) {
		_error(404);
	}
	/* check username case */
	if(strtolower($_GET['username']) == strtolower($profile['user_name']) && $_GET['username'] != $profile['user_name']) {
		redirect('/'.$profile['user_name']);
	}
	/* get profile picture */
	$profile['user_picture_default'] = ($profile['user_picture'])? false : true;
	$profile['user_picture'] = User::get_picture($profile['user_picture'], $profile['user_gender']);
	/* get the connection &  mutual friends */
	if($user->_logged_in && $profile['user_id'] != $user->_data['user_id']) {
		/* get the connection */
		$profile['we_friends'] = (in_array($profile['user_id'], $user->_data['friends_ids']))? true: false;
		$profile['he_request'] = (in_array($profile['user_id'], $user->_data['friend_requests_ids']))? true: false;
		$profile['i_request'] = (in_array($profile['user_id'], $user->_data['friend_requests_sent_ids']))? true: false;
		$profile['i_follow'] = (in_array($profile['user_id'], $user->_data['followings_ids']))? true: false;
		/* get mutual friends */
		$profile['mutual_friends_count'] = $user->get_mutual_friends_count($profile['user_id']);
		$profile['mutual_friends'] = $user->get_mutual_friends($profile['user_id']);
	}
	
	// [2] get view content
	switch ($_GET['view']) {

		case '':

			// $educations = get_educations();
			/* assign variables */
			$smarty->assign('educations', $educations);
			$smarty->assign('get', "educations");

			$universities = $user->get_university();
			/* assign variables */
			$smarty->assign('universities', $universities);

			$macrosubjects = $user->get_macrosubject();
			/* assign variables */
			$smarty->assign('macrosubjects', $macrosubjects);

			$grades = $user->get_gradetype();
			/* assign variables */
			$smarty->assign('grades', $grades);

			$countries = $user->get_countries();
			/* assign variables */
			$smarty->assign('countries', $countries);

			$idiomas = $user->get_idiomas();
			/* assign variables */
			$smarty->assign('idiomas', $idiomas);

			$completamientos = $user->get_completamientos();
			/* assign variables */
			$smarty->assign('completamientos', $completamientos);

			/* get followers count */
			$profile['followers_count'] = count($user->get_followers_ids($profile['user_id']));

			// get custom fields
			$smarty->assign('custom_fields', $user->get_custom_fields( array("get" => "profile", "user_id" => $profile['user_id']) ));

			/* get friends */
			$profile['friends'] = $user->get_friends($profile['user_id']);			
			if(count($profile['friends']) > 0) {
				$profile['friends_count'] = count($user->get_friends_ids($profile['user_id']));
			}

			/* get photos */
			$profile['photos'] = $user->get_photos($profile['user_id']);

			/* get pages */
			$profile['pages'] = $user->get_pages( array('user_id' => $profile['user_id'], 'results' => $system['min_results_even']) );
			
			/* get groups */
			$profile['groups'] = $user->get_groups( array('user_id' => $profile['user_id'], 'results' => $system['min_results_even']) );

			/* get events */
			$profile['events'] = $user->get_events( array('user_id' => $profile['user_id'], 'results' => $system['min_results_even']) );

			/* get pinned post */
			$pinned_post = $user->get_post($profile['user_pinned_post']);
			$smarty->assign('pinned_post', $pinned_post);

			/* prepare publisher */
			$smarty->assign('market_categories', $user->get_market_categories());
			$smarty->assign('feelings', get_feelings());
			$smarty->assign('feelings_types', get_feelings_types());

			/* get posts */
			$posts = $user->get_posts( array('get' => 'posts_profile', 'id' => $profile['user_id']) );
			/* assign variables */
			$smarty->assign('posts', $posts);
			break;

		case 'friends':
			/* get friends */
			$profile['friends'] = $user->get_friends($profile['user_id']);			
			if(count($profile['friends']) > 0) {
				$profile['friends_count'] = count($user->get_friends_ids($profile['user_id']));
			}
			break;

		case 'photos':
			/* get photos */
			$profile['photos'] = $user->get_photos($profile['user_id']);
			break;

		case 'albums':
			/* get albums */
			$profile['albums'] = $user->get_albums($profile['user_id']);
			break;

		case 'album':
			/* get album */
			$album = $user->get_album($_GET['id']);
			if(!$album || $album['in_group'] || $album['user_type'] == "page" || ($album['user_type'] == "user" && $album['user_id'] != $profile['user_id'])) {
				_error(404);
			}
			/* assign variables */
			$smarty->assign('album', $album);
			break;

		case 'followers':
			/* get followers count */
			$profile['followers_count'] = count($user->get_followers_ids($profile['user_id']));
			/* get followers */
			if($profile['followers_count'] > 0) {
				$profile['followers'] = $user->get_followers($profile['user_id']);
			}
			break;

		case 'followings':
			/* get followings count */
			$profile['followings_count'] = count($user->get_followings_ids($profile['user_id']));
			/* get followings */
			if($profile['followings_count'] > 0) {
				$profile['followings'] = $user->get_followings($profile['user_id']);
			}
			break;

		case 'likes':
			/* get pages */
			$profile['pages'] = $user->get_pages( array('user_id' => $profile['user_id']) );
			break;

		case 'groups':
			/* get groups */
			$profile['groups'] = $user->get_groups( array('user_id' => $profile['user_id']) );
			break;

		case 'events':
			/* get events */
			$profile['events'] = $user->get_events( array('user_id' => $profile['user_id']) );
			break;

		default:
			_error(404);
			break;
	}

	// [3] profile visit notification
	if($_GET['view'] == "" && $user->_logged_in && $system['profile_notification_enabled']) {
		$user->post_notification( array('to_user_id'=>$profile['user_id'], 'action'=>'profile_visit') );
	}

	// recent rearches
	if(isset($_GET['ref']) && $_GET['ref'] == "qs") {
		$user->add_search_log($profile['user_id'], 'user');
	}

} catch (Exception $e) {
	_error(__("Error"), $e->getMessage());
}

// page header
page_header($profile['user_firstname']." ".$profile['user_lastname']);

// assign variables
$smarty->assign('profile', $profile);
$smarty->assign('view', $_GET['view']);

// page footer
page_footer("profile");

?>