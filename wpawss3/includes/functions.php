<?php

require WPS3_PLUGIN_DIR.'/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

function records_list() {
    check_ajax_referer('wpawss3', 'security');
    $result['success'] = false;
    $result['data'] = [
        'message' => 'Network error.',
    ];
 	
// 	$servername = get_option('wpawss3_host');
// 	$username = get_option('wpawss3_username');
// 	$password = get_option('wpawss3_password');
//  $dbname = get_option('wpawss3_db_name');
	
	
    $dbname = get_option('wpawss3_db_name');
	$servername = "localhost:3306";
	$username = 'wpDataTables';
	$password = 'd903kdas;l390-f$jki43 i-0233kd023;% IKO3($*#kjdl';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=processing", $username, $password);
		
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		try {
            $s3 = new S3Client([
                'version' => get_option('wpawss3_aws_version'),
				'region'  => get_option('wpawss3_aws_region'),
				'credentials' => [
					'key'    => get_option('wpawss3_aws_key'),
					'secret' => get_option('wpawss3_aws_secret_key')
				]
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL; 
        }
		
		$userId = 1;
		if( is_user_logged_in() ) {
			$userId = get_current_user_id();
		}
		
        // set the PDO error mode to exception
        $sql_stmt = "SELECT 
                    PF.`folderName`, PFS.`label` as `status`, PU.`label` as idUser, BIN_TO_UUID(PF.`idFolder`) as idFolder, PFI.label as isPublic 
                    FROM prs_folders PF 
                    INNER JOIN prs_folders_ispublic PFI ON PFI.id = PF.isPublic
                    INNER JOIN prs_users PU ON PU.id = PF.idUser
                    INNER JOIN prs_folders_status PFS ON PFS.id = PF.status
					WHERE PF.status = 1 AND PF.idUser = $userId
                    "; 
        $stmt = $conn->prepare($sql_stmt);
        $stmt->execute();
        $result = $stmt->fetchAll();
		
        foreach($result as $key=>$value) {
            $isPublic = ($value['isPublic'] == 'Public') ? 'public/' : 'private/';
            $userlogin = get_current_user_id().'/';
            $folder = $value['folderName'].'/';
            $path = $isPublic.$userlogin.$folder;
            $objects = $s3->listObjects([
                'Bucket' => AWS_S3_BUCKET,
                'Prefix' => $path
            ]);
            $result[$key]['process'] = 0;
            if (isset($objects['Contents'])) {
                if (count($objects['Contents']) > 0) {
                    $result[$key]['process'] = 1;
                }
            }
        }
        wp_send_json_success( $result );
    } catch (\Exception $ex) {
        $result['success'] = false;
        $result['data'] = [
            'message' => $ex->getMessage(),
        ];
        wp_send_json_error( $result );
    }
    
    wp_send_json_error( $result );
    die();
}
add_action('wp_ajax_records_list', 'records_list');
add_action('wp_ajax_nopriv_records_list', 'records_list');

function magic_funcs() {
	check_ajax_referer('wpawss3', 'security');
	$insertdata = [];
    $result = [];
	// default response
	$result['success'] = false;
	$result['data'] = [
		'message' => 'Network error.',
	];

	if (!empty($_POST) && !empty($_POST['newfoldername'])) {    
		$newFolderName = $_POST['newfoldername'];
		$skip = $_POST['skip'];
		$bucket = $_POST['bucket'];
		$response = MagicWP::createFolderCB($newFolderName, $bucket, $skip);
		if ($response['success']) {
			$folderNameArr = explode("/", $newFolderName);
			$response['data'] = [
                'success' => true,
				'folderName' => $folderNameArr[count($folderNameArr)-2],
				'folderPath' => $newFolderName
			];
			wp_send_json_success($response['data']);
		} else {
			wp_send_json_error($response['msg']);
		}
		die;
	}

	if(!empty($_POST['wpawss3_getDBFolderList'])){
       $bucket = $_POST['wpawss3_bucket'];
		$desti = $_POST['wpawss3_desti'];
        $response = MagicWP::getAllFolderDB($bucket, $desti);
        if ($response['data']) {
			
            $response['success'] = true;
			wp_send_json_success($response);
		} else {
			$response['msg'] = 'Folder not found';
			wp_send_json_error($response['msg']);
		}
		die;
    }
	
	if(!empty($_POST['wpawss3_getfileList'])){
       $bucket = $_POST['wpawss3_bucket'];
		$desti = $_POST['wpawss3_desti'];
		$folderhas = $_POST['wpawss3_folderhas'];
        $response = MagicWP::getAllFileDB($bucket, $desti, $folderhas);
        if ($response['data']) {
			
            $response['success'] = true;
			wp_send_json_success($response);
		} else {
			$response['msg'] = 'File not found';
			wp_send_json_error($response['msg']);
		}
		die;
    }
	
	if (!empty($_POST['wpawss3_getFolderList'])) {
		$bucket = $_POST['wpawss3_bucket'];
		$desti = $_POST['wpawss3_desti'];
        $response = MagicWP::getAllFolderCB($bucket, $desti);
//         print_r($response);
		$arr = [];
		if ($response) {
            $response['success'] = true;
			wp_send_json_success($response);
		} else {
			wp_send_json_error($response['msg']);
		}
		die;
	}
	
	wp_send_json_error( $result );
}

add_action('wp_ajax_magic_funcs', 'magic_funcs');
add_action('wp_ajax_nopriv_magic_funcs', 'magic_funcs');


function create_folder() {
    check_ajax_referer('wpawss3', 'security');
    $RESULTS = [];
    $result['success'] = false;
	$result['data'] = [
		'message' => 'Network error.',
    ];
    try {
		$userId = 1;
		if( is_user_logged_in() ) {
			$userId = get_current_user_id();
        }
        
        $servername = get_option('wpawss3_host');
        $username = get_option('wpawss3_username');
        $password = get_option('wpawss3_password');
        $dbname = get_option('wpawss3_db_name');
		
		$MyConnection = new mysqli($servername, $username, $password, $dbname, 3306);
		
		$par_idFolder_HEX = $_POST['formData'][0]['value'];
		$par_folderName = $_POST['formData'][0]['value'];
		$par_ispublic = $_POST['formData'][1]['value'];
        $par_idUser = $userId;
        $par_status = 1;
        
		mysqli_multi_query($MyConnection, "CALL get_constants()");
		if(mysqli_multi_query($MyConnection, "CALL CRUD_prs_folders(@CRUD_CREATE, '$par_idFolder_HEX', '$par_folderName', @PAR_NONE, $par_ispublic, $par_idUser, $par_status, @PAR_NONE)")) {
			$result['success'] = true;
			$result['data'] = [
				'message' => 'Folder Created Successfully.',
			];
			wp_send_json_success( $result );
			mysqli_close($MyConnection);
		} else {
			$result['success'] = false;
			$result['data'] = [
                'server_error' => mysqli_error($MyConnection),
				'message' =>  'Internal Server Error.',
			];
			wp_send_json_error( $result );
			exit;
		}
        
    } catch (\Exception $ex) {
        $result['success'] = false;
        $result['data'] = [
            'message' => $ex->getMessage(),
        ];
        wp_send_json_error( $result );
    }

    wp_send_json_error( $result );
    die();
}
add_action('wp_ajax_create_folder', 'create_folder');
add_action('wp_ajax_nopriv_create_folder', 'create_folder');

// Get idAppParSaf or idAppParCmp based on SAFD or COMP selection.

function get_id_app_par() {
	check_ajax_referer('wpawss3', 'security');
	
    $response = [];
	// default response
	$response['success'] = false;
	$response['data'] = [
		'message' => 'Network error.',
	];
	
	$servername = get_option('wpawss3_host');
	$username = get_option('wpawss3_username');
	$password = get_option('wpawss3_password');
	$dbname = get_option('wpawss3_db_name');

	$MyConnection = new mysqli($servername, $username, $password, $dbname, 3306);
	
	if($_POST['par_idApp'] == 2){
		
		mysqli_multi_query($MyConnection, "CALL get_constants()");
		if(mysqli_multi_query($MyConnection, "CALL CRUD_prs_app_parameters_safd(@CRUD_READ , NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)")) {
		
			while (mysqli_more_results($MyConnection)) {

				   if ($results = mysqli_store_result($MyConnection)) {
					
						  while ($row = mysqli_fetch_assoc($results)) {
							  $rowdata = [];
							  $rowdata['idAppParSaf'] =  $row['idAppParSaf'];
							  $rowdata['label'] =  $row['label'];
								$data[] = $rowdata;
						  }
						  mysqli_free_result($results);
				   }
				   mysqli_next_result($MyConnection);
			}
			$response['data']['message'] = '';
			$response['idAppPar'] = $data;
			$response['label'] = 'idAppParSaf';
			$response['success'] = true;
			wp_send_json_success($response);
			mysqli_close($MyConnection);
		}	
	}
	if($_POST['par_idApp'] == 3){
		mysqli_multi_query($MyConnection, "CALL get_constants()");
		if(mysqli_multi_query($MyConnection, "CALL CRUD_prs_app_parameters_COMP(@CRUD_READ , NULL, NULL, NULL, NULL, NULL, NULL)")) {
		
			while (mysqli_more_results($MyConnection)) {

				   if ($results = mysqli_store_result($MyConnection)) {
					
						  while ($row = mysqli_fetch_assoc($results)) {
							  $rowdata = [];
							  $rowdata['idAppParCmp'] =  $row['idAppParCmp'];
							  $rowdata['label'] =  $row['label'];
							  $data[] = $rowdata;
						  }
						  mysqli_free_result($results);
				   }
				   mysqli_next_result($MyConnection);
			}
			$response['data']['message'] = '';
			$response['idAppPar'] = $data;
			$response['label'] = 'idAppParCmp';
			$response['success'] = true;
			wp_send_json_success($response);
			mysqli_close($MyConnection);
		}	
	}
	if($_POST['par_idApp'] == 4){
		mysqli_multi_query($MyConnection, "CALL get_constants()");
		if(mysqli_multi_query($MyConnection, "CALL CRUD_prs_app_parameters_fede(@CRUD_READ , NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)")) {
		
			while (mysqli_more_results($MyConnection)) {

				   if ($results = mysqli_store_result($MyConnection)) {
					
						  while ($row = mysqli_fetch_assoc($results)) {
							  $rowdata = [];
							  $rowdata['idAppParFed'] =  $row['CRUD_prs_app_parameters_fede'];
							  $rowdata['label'] =  $row['label'];
							  $data[] = $rowdata;
						  }
						  mysqli_free_result($results);
				   }
				   mysqli_next_result($MyConnection);
			}
			$response['data']['message'] = '';
			$response['idAppPar'] = $data;
			$response['label'] = 'idAppParFed';
			$response['success'] = true;
			wp_send_json_success($response);
			mysqli_close($MyConnection);
		}	
	}
	wp_send_json_error( $result );
}

add_action('wp_ajax_get_id_app_par', 'get_id_app_par');
add_action('wp_ajax_nopriv_get_id_app_par', 'get_id_app_par');

// Store process 

function store_process() {
	check_ajax_referer('wpawss3', 'security');
	
	$result = [];
	// default response
	$result['success'] = false;
	$result['data'] = [
		'message' => 'Network error.',
	];
	
	$folderhas = $_POST['folderhas'];
	$process_radio = $_POST['process_radio'];
	$filehas = $_POST['filehas'];
	$par_idApp = $_POST['par_idApp'];
	$idAppPar = $_POST['idAppPar'];
	$comment = $_POST['comment'];
	
	$userId = 1;
	if( is_user_logged_in() ) {
		$userId = get_current_user_id();
	}

	$servername = get_option('wpawss3_host');
	$username = get_option('wpawss3_username');
	$password = get_option('wpawss3_password');
	$dbname = get_option('wpawss3_db_name');
	
	$MyConnection = new mysqli($servername, $username, $password, $dbname, 3306);
	mysqli_multi_query($MyConnection, "CALL get_constants()");
	
		if($process_radio == 'process_file'){
			
// 			$output = shell_exec("sh /home/Actions/Actions.sh --action 2 --idFile '".$filehas."' --idAppPar $idAppPar --idApp $par_idApp --idUser $userId --label '".$comment."'");
// 			if($output){
// 				$result['success'] = true;
// 				$result['data'] = [
// 					'message' => $output,
// 				];
// 				wp_send_json_success($result);
// 			}else{
// 				wp_send_json_error($result);
// 			}
			
			if(mysqli_multi_query($MyConnection, "CALL prs_app_file('".$filehas."', $idAppPar, $par_idApp, $userId, '".$comment."')")) {
				
				
				while (mysqli_more_results($MyConnection)) {

					   if ($results = mysqli_store_result($MyConnection)) {

							  while ($row = mysqli_fetch_assoc($results)) {
								 
									$data[] = $row;
							  }
							  mysqli_free_result($results);
					   }
					   mysqli_next_result($MyConnection);
				}
				
				
				$result['success'] = true;
				$result['data'] = $data;
// 				$result['data'] = [
// 					'message' => 'File processed Successfully.',
// 				];
				wp_send_json_success( $result );
				mysqli_close($MyConnection);
			}else{
				 $result['success'] = false;
        		 $result['data'] = [
					'message' => mysqli_error($MyConnection)
				];
				 wp_send_json_error( $result );
        
			}

		}else if($process_radio == 'process_folder'){
			
// 			$output = shell_exec("sh /home/Actions/Actions.sh --action 3 --idFolder '".$folderhas."' --idAppPar $idAppPar --idApp $par_idApp --idUser $userId --label '".$comment."'");
// 			if($output){
// 				$result['success'] = true;
// 				$result['data'] = [
// 					'message' => $output,
// 				];
// 				wp_send_json_success($result);
// 			}else{
// 				wp_send_json_error($result);
// 			}
			
			if(mysqli_multi_query($MyConnection, "CALL prs_app_folder('".$folderhas."', $idAppPar, $par_idApp, $userId,  '".$comment."')")) {
				
				while (mysqli_more_results($MyConnection)) {

					   if ($results = mysqli_store_result($MyConnection)) {

							  while ($row = mysqli_fetch_assoc($results)) {
								 
									$data[] = $row;
							  }
							  mysqli_free_result($results);
					   }
					   mysqli_next_result($MyConnection);
				}
				
				
				
				$result['success'] = true;
// 				$result['data'] = [
// 					'message' => 'Folder processed Successfully.',
// 				];
 				$result['data'] = $data;
				wp_send_json_success( $result );
				mysqli_close($MyConnection);
			}else{
				 $result['success'] = false;
        		 $result['data'] = [
					'message' => mysqli_error($MyConnection)
				];
				 wp_send_json_error( $result );
        
			}

		}
		
	wp_send_json_error( $result );
}

add_action('wp_ajax_store_process', 'store_process');
add_action('wp_ajax_nopriv_store_process', 'store_process');


function add_meta_for_existing_swath() {
	check_ajax_referer('wpawss3', 'security');
	
	$result = [];
	// default response
	$result['success'] = false;
	$result['data'] = [
		'message' => 'Network error.',
	];
	
	$folderhas = $_POST['folderhas'];
	$par_idFile = $_POST['filehas'];
	$par_mode = $_POST['mode'];
	$par_isSwath = $_POST['par_isSwath'];
	$par_idSwath = $_POST['par_idSwath'];
	
	$servername = get_option('wpawss3_host');
	$username = get_option('wpawss3_username');
	$password = get_option('wpawss3_password');
	$dbname = get_option('wpawss3_db_name');

	$MyConnection = new mysqli($servername, $username, $password, $dbname, 3306);
	mysqli_multi_query($MyConnection, "CALL get_constants()");

	if(mysqli_multi_query($MyConnection, "CALL CRUD_prs_files_metadata(@CRUD_CREATE, '".$par_idFile."', $par_mode, $par_isSwath, $par_idSwath, NULL)")) {
		
		$result['success'] = true;
		$result['data'] = [
			'message' => 'Added file meta with existing swath successfully.',
		];
		wp_send_json_success( $result );
		mysqli_close($MyConnection);
	}
	
	wp_send_json_error( $result );
}

add_action('wp_ajax_add_meta_for_existing_swath', 'add_meta_for_existing_swath');
add_action('wp_ajax_nopriv_add_meta_for_existing_swath', 'add_meta_for_existing_swath');



function add_meta_for_new_swath() {
	check_ajax_referer('wpawss3', 'security');
	
	$result = [];
	// default response
	$result['success'] = false;
	$result['data'] = [
		'message' => 'Network error.',
	];
	
	$folderhas = $_POST['folderhas'];
	$par_idFile = $_POST['filehas'];
	$par_mode = $_POST['mode'];
	$par_isSwath = $_POST['par_isSwath'];
	
	$par_expIndex = $_POST['par_expIndex'];
	$par_startMass = $_POST['par_startMass'];
	$par_stopMass = $_POST['par_stopMass'];
	$par_ces = $_POST['par_ces'];
	
	$j = count($par_expIndex);
 	
	$servername = get_option('wpawss3_host');
	$username = get_option('wpawss3_username');
	$password = get_option('wpawss3_password');
	$dbname = get_option('wpawss3_db_name');

	$MyConnection = new mysqli($servername, $username, $password, $dbname, 3306);
	mysqli_multi_query($MyConnection, "CALL get_constants()");
	
	$idSwath = NULL;
	
	for ($i = 0; $i <= $j-1; $i++) {
	  	if($i == 0){
				
			if(mysqli_multi_query($MyConnection, "CALL CRUD_prs_files_metadata_swath(@CRUD_CREATE, NULL, $par_expIndex[$i], $par_startMass[$i], $par_stopMass[$i], $par_ces[$i], NULL)")) {
				
				while (mysqli_more_results($MyConnection)) {

				   if ($results = mysqli_store_result($MyConnection)) {
						  
						  while ($row = mysqli_fetch_assoc($results)) {
								$idSwath = $row['idSwath'];
							  	$data[] = $row;
						  }
						  mysqli_free_result($results);
				   }
				   mysqli_next_result($MyConnection);
				}
	
				
			}else{
				
				 $result['success'] = false;
        		 $result['message'] = mysqli_error($MyConnection);
				 wp_send_json_error( $result );
        
			}
		}else{
			
			if(mysqli_multi_query($MyConnection, "CALL CRUD_prs_files_metadata_swath(@CRUD_CREATE, $idSwath, $par_expIndex[$i], $par_startMass[$i], $par_stopMass[$i], $par_ces[$i], NULL)")) {
				
				while (mysqli_more_results($MyConnection)) {

				   if ($results = mysqli_store_result($MyConnection)) {
						  
						  while ($row = mysqli_fetch_assoc($results)) {
								$data[] = $row;
						  }
						  mysqli_free_result($results);
				   }
				   mysqli_next_result($MyConnection);
				}
	
				
			}else{
				
				 $result['success'] = false;
        		 $result['message'] = mysqli_error($MyConnection);
				 wp_send_json_error( $result );
        
			}
			
		}
		
	}
	
	if(mysqli_multi_query($MyConnection, "CALL CRUD_prs_files_metadata(@CRUD_CREATE, '".$par_idFile."', $par_mode, $par_isSwath, $idSwath, NULL)")) {
		
		$result['success'] = true;
		$result['data'] = [
			'message' => 'Added file meta with new swath successfully.',
		];
		wp_send_json_success( $result );
		mysqli_close($MyConnection);
	}
	wp_send_json_error( $result );
	
}

add_action('wp_ajax_add_meta_for_new_swath', 'add_meta_for_new_swath');
add_action('wp_ajax_nopriv_add_meta_for_new_swath', 'add_meta_for_new_swath');


function add_meta_without_swath()
{	
  	$result = [];
	// default response
	$result['success'] = false;
	$result['data'] = [
		'message' => 'Network error.',
	];
	
	$folderhas = $_POST['folderhas'];
	$par_idFile = $_POST['filehas'];
	$par_mode = $_POST['mode'];
	$par_isSwath = $_POST['par_isSwath'];
	
	$servername = get_option('wpawss3_host');
	$username = get_option('wpawss3_username');
	$password = get_option('wpawss3_password');
	$dbname = get_option('wpawss3_db_name');

	$MyConnection = new mysqli($servername, $username, $password, $dbname, 3306);
	mysqli_multi_query($MyConnection, "CALL get_constants()");

	if(mysqli_multi_query($MyConnection, "CALL CRUD_prs_files_metadata(@CRUD_CREATE, '".$par_idFile."', $par_mode, $par_isSwath, NULL, NULL)")) {
		
		$result['success'] = true;
		$result['data'] = [
			'message' => 'Added file meta without swath successfully.',
		];
		wp_send_json_success( $result );
		mysqli_close($MyConnection);
	}
	
	wp_send_json_error( $result );
	
	
}	
add_action('wp_ajax_add_meta_without_swath', 'add_meta_without_swath');
add_action('wp_ajax_nopriv_add_meta_without_swath', 'add_meta_without_swath');


function get_swath_ids()
{
	check_ajax_referer('wpawss3', 'security');
    $dbname = get_option('wpawss3_db_name');
	$servername = "localhost:3306";
	$username = 'wpDataTables';
	$password = 'd903kdas;l390-f$jki43 i-0233kd023;% IKO3($*#kjdl';

    $conn = new PDO("mysql:host=$servername;dbname=processing", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql_stmt = "SELECT DISTINCT idSwath FROM processing.prs_files_metadata_swath"; 
    $stmt = $conn->prepare($sql_stmt);
	$stmt->execute();
	$datas = $stmt->fetchAll();
	$swathArray = [];
	foreach($datas as $data) {
	    $swathArray[] =  $data['idSwath'];
	}
	
	$result['success'] = true;
	$result['data'] = $swathArray;
	wp_send_json_success( $result );
}	
add_action('wp_ajax_get_swath_ids', 'get_swath_ids');
add_action('wp_ajax_nopriv_get_swath_ids', 'get_swath_ids');



function process_ajax() {
	check_ajax_referer('wpawss3', 'security');
	$insertdata = [];
	$result = [];

	// default response
	$result['success'] = false;
	$result['data'] = [
		'message' => 'Network error.',
	];
	
	if (!empty($_POST) && !empty($_POST['hrefVal'])) {    
		$hrefVal = $_POST['hrefVal'];
		
		
		$servername = get_option('wpawss3_host');
		$username = get_option('wpawss3_username');
		$password = get_option('wpawss3_password');
		$dbname = get_option('wpawss3_db_name');

		$MyConnection = new mysqli($servername, $username, $password, $dbname, 3306);
		mysqli_multi_query($MyConnection, "CALL get_constants()");

		if(mysqli_multi_query($MyConnection, "CALL CRUD_prs_folders(@CRUD_UPDATE, '".$hrefVal."', @PAR_NONE ,@PAR_NONE ,@PAR_NONE ,@PAR_NONE, @FOLDER_STATUS_PROCESSING_REQUEST, @PAR_NONE)")) {

			$result['success'] = true;
			$result['data'] = [
				'message' => 'Folder processed Successfully.',
			];
			wp_send_json_success( $result );
			mysqli_close($MyConnection);
		}

		wp_send_json_error( $result );
		
		
		
// 		$output = shell_exec('sh /home/Actions/Actions.sh --action 1 --idFolder '.$hrefVal);
// 		if($output){
// 			$result['success'] = true;
// 			$result['data'] = $output;
// 			wp_send_json_success($result);
// 		}else{
// 			wp_send_json_error($result);
// 		}
// 		echo "<pre>$output</pre>";
		
// 		$cmd = 'python3 /home/Action/Actions.py --action PROCESS_FOLDER --idFolder '.$hrefVal;
// 		if ($response['success']) {
// 			$response['data'] = $output;
// 			wp_send_json_success($response);
// 		} else {
// 			wp_send_json_error($response['msg']);
// 		}
		die;
	}
}
add_action('wp_ajax_process_ajax', 'process_ajax');
add_action('wp_ajax_nopriv_process_ajax', 'process_ajax');



function upload_status_list() {
    check_ajax_referer('wpawss3', 'security');
    $result['success'] = false;
    $result['data'] = [
        'message' => 'Network error.',
    ];
 	
// 	$servername = get_option('wpawss3_host');
// 	$username = get_option('wpawss3_username');
// 	$password = get_option('wpawss3_password');
//  $dbname = get_option('wpawss3_db_name');
	
	
    $dbname = get_option('wpawss3_db_name');
	$servername = "localhost:3306";
	$username = 'wpDataTables';
	$password = 'd903kdas;l390-f$jki43 i-0233kd023;% IKO3($*#kjdl';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=processing", $username, $password);
		
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		try {
            $s3 = new S3Client([
                'version' => get_option('wpawss3_aws_version'),
				'region'  => get_option('wpawss3_aws_region'),
				'credentials' => [
					'key'    => get_option('wpawss3_aws_key'),
					'secret' => get_option('wpawss3_aws_secret_key')
				]
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL; 
        }
		
		$userId = 1;
		if( is_user_logged_in() ) {
			$userId = get_current_user_id();
		}
		
		$user = wp_get_current_user();
		$allowed_roles = array('administrator');
		
		if( array_intersect($allowed_roles, $user->roles ) ) {  
			
		   $sql_stmt = "SELECT * FROM `view_prs_files`";
			
		}else{
			$sql_stmt = "SELECT * FROM `view_prs_files` WHERE idUser = $userId";

		} 
		
		// 			 $sql_stmt = "SELECT 
//                     PF.`folderName`, PFS.`label` as `status`, PU.`label` as idUser, BIN_TO_UUID(PF.`idFolder`) as idFolder, PFI.label as isPublic 
//                     FROM prs_folders PF 
//                     INNER JOIN prs_folders_ispublic PFI ON PFI.id = PF.isPublic
//                     INNER JOIN prs_users PU ON PU.id = PF.idUser
//                     INNER JOIN prs_folders_status PFS ON PFS.id = PF.status
// 					WHERE PF.status = 1 AND PF.idUser = $userId";
					
        $stmt = $conn->prepare($sql_stmt);
        $stmt->execute();
         $records = $stmt->fetchAll();
		$data = [];
		foreach($records as $record) {
			 
			 $record['filesize'] = $record['filesize']/1073741824; 
			 $data[] = $record;
			
        }
        wp_send_json_success( $data );
    } catch (\Exception $ex) {
        $result['success'] = false;
        $result['data'] = [
            'message' => $ex->getMessage(),
        ];
        wp_send_json_error( $result );
    }
    
    wp_send_json_error( $result );
    die();
}
add_action('wp_ajax_upload_status_list', 'upload_status_list');
add_action('wp_ajax_nopriv_upload_status_list', 'upload_status_list');




function view_process_status_list() {
    check_ajax_referer('wpawss3', 'security');
    $result['success'] = false;
    $result['data'] = [
        'message' => 'Network error.',
    ];
 		
    $dbname = get_option('wpawss3_db_name');
	$servername = "localhost:3306";
	$username = 'wpDataTables';
	$password = 'd903kdas;l390-f$jki43 i-0233kd023;% IKO3($*#kjdl';

    try {
        $conn = new PDO("mysql:host=$servername;dbname=processing", $username, $password);
		
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		try {
            $s3 = new S3Client([
                'version' => get_option('wpawss3_aws_version'),
				'region'  => get_option('wpawss3_aws_region'),
				'credentials' => [
					'key'    => get_option('wpawss3_aws_key'),
					'secret' => get_option('wpawss3_aws_secret_key')
				]
            ]);
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL; 
        }
		
	//	$userId = 1;
		if( is_user_logged_in() ) {
			$userId = get_current_user_id();
		}
		
		$user = wp_get_current_user();
		$allowed_roles = array('administrator');
		if( array_intersect($allowed_roles, $user->roles ) ) {  
		   $sql_stmt = "SELECT * FROM `view_prs_app_process` WHERE STATUS NOT IN (4,5)";
		}else{
			$sql_stmt = "SELECT * FROM `view_prs_app_process` WHERE STATUS NOT IN (4,5) AND idUser = $userId";
		} 
		
        $stmt = $conn->prepare($sql_stmt);
        $stmt->execute();
        $records = $stmt->fetchAll();
		$data = [];
		foreach($records as $record) {
			 
			 $record['filesize'] = $record['filesize']/1073741824; 
			 $data[] = $record;
        }
		
			
        wp_send_json_success( $data );
    } catch (\Exception $ex) {
        $result['success'] = false;
        $result['data'] = [
            'message' => $ex->getMessage(),
        ];
        wp_send_json_error( $result );
    }
    
    wp_send_json_error( $result );
    die();
}
add_action('wp_ajax_view_process_status_list', 'view_process_status_list');
add_action('wp_ajax_view_process_status_list', 'view_process_status_list');
