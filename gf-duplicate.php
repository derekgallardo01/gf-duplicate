<?php
/**
* Plugin Name: GF duplicate
* Plugin URI: no
* Description: Copy gravity form entries.
* Version: 1.0.0
* Author: Monika
* Author URI: no
* License: GPL2
*/

add_action( 'wp_head', 'my_facebook_tags' );
function my_facebook_tags() {
//echo 'I am in the head section';
}

add_action( 'gform_entries_first_column_actions', 'first_column_actions', 10, 4 );
function first_column_actions( $form_id, $field_id, $value, $entry ) {

$lead_id = $entry['id'];
//echo "| <a href=";">Print</a>";

}

add_action( 'gform_entries_first_column_actions', function ( $form_id, $field_id, $value, $entry ) {
//echo "| Mark as Paid";
echo '<span><a href="admin.php?page=copy-gf-record&amp;entry_id='.$entry['id'].'&form_id='.$form_id.'">Duplicate record</a></span>';
}, 10, 4 );

add_action('admin_menu', 'gf_dulicate_admin_page');
function gf_dulicate_admin_page() {

//add_menu_page('FFFFFFF', 'EEEEEE', 'administrator','Copy GF records', 'custom');
//remove_menu_page('custom');
add_submenu_page('edit.php?post_type=custom-type', 'My Page Title', 'My Page Title', 'manage_options', 'my-page-slug', 'my_page_callback');
//then remove it
//remove_submenu_page('edit.php?post_type=custom-type','my-page-slug');
add_submenu_page( null, 'Copy GF records', 'Copy GF records', 'administrator', 'copy-gf-record', 'duplicate_gf_records' );

}

function duplicate_gf_records(){
global $wpdb;

//entry_id

$entryid = (isset($_GET["entry_id"]) && $_GET["entry_id"]!="")? $_GET["entry_id"]:"";
$formid = (isset($_GET["form_id"]) && $_GET["form_id"]!="")? $_GET["form_id"]:"";

$lead = RGFormsModel::get_lead( $entryid ); 

//
if(isset($lead["created_by"]) && $lead["created_by"]!='' ){
	$user = get_user_by( 'id', $lead["created_by"] );
	
}

$wpdb->query("INSERT INTO ".$wpdb->prefix."rg_lead (`form_id`, `post_id`, `date_created`, `is_starred`, `is_read`, `ip`, `source_url`, `user_agent`, `currency`, `payment_status`, `payment_date`, `payment_amount`, `payment_method`, `transaction_id`, `is_fulfilled`, `created_by`, `transaction_type`, `status`) SELECT `form_id`, `post_id`, `date_created`, `is_starred`, `is_read`, `ip`, `source_url`, `user_agent`, `currency`, `payment_status`, `payment_date`, `payment_amount`, `payment_method`, `transaction_id`, `is_fulfilled`, `created_by`, `transaction_type`, `status` FROM ".$wpdb->prefix."rg_lead WHERE id = '".$entryid."' ");
$leadid = $wpdb->insert_id;

$wpdb->query("INSERT INTO `".$wpdb->prefix."rg_lead_detail`( `lead_id`, `form_id`, `field_number`, `value`) SELECT '".$leadid."', `form_id`, `field_number`, `value` FROM ".$wpdb->prefix."rg_lead_detail WHERE lead_id = '".$entryid."'  and field_number in (14, 48, 15, 38, 39, 3, 2, 43, 40, 41, 42) ");

$wpdb->query(" INSERT INTO `".$wpdb->prefix."rg_lead_meta`(`lead_id`, `form_id`, `meta_key`, `meta_value`) SELECT '".$leadid."', `form_id`, `meta_key`, `meta_value` FROM ".$wpdb->prefix."rg_lead_meta WHERE lead_id = '".$entryid."' ");

/* $to = $user->user_email;
$subject = 'Add Event';
$body = "Dear ".$user->nick_name." <br /><br />
Please view the URL to add a new event.<br /><br />
".site_url()."/submit-job/?quote=appointment&mode=edit&edit_id={$leadid}
";
$headers = array('Content-Type: text/html; charset=UTF-8');
 
wp_mail( $to, $subject, $body, $headers ); */

echo '<script>document.location=\'admin.php?page=gf_entries&view=entries&id='.$formid.'\'</script>';
//wp_redirect(admin_url('/admin.php?page=gf_entries&view=entries&id='.$formid, 'http'), 301);
exit;

}