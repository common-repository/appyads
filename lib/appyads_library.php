<?php
/*
* Support definitions and methods for AppyAds
*/
define ('APPYADS_DEFAULT_ACCOUNT', 'wpwpwpwp');
define ('APPYADS_BASE_URL', '//appyads.com/campaign-resources/?a='.APPYADS_DEFAULT_ACCOUNT);
define ('APPYADS_ACCOUNT_WP_OPTION', 'appyads_account');
define ('APPYADS_DEFAULT_CAMPAIGN_SIZE', '180X60');
function appyads_getAppyAdsLogo() {
    $iLogoSrc = plugin_dir_url( dirname( __FILE__ ) ) . 'img/appyads_logo.png';
    return "<div style=\"margin:1em auto 0;width:180px;\"><a href=\"//appyads.com/\" target=\"_blank\" style=\"outline:none;\"><img src=\"".$iLogoSrc."\" title=\"AppyAds\" alt=\"AppyAds\" style=\"width:100%;height:auto;border:none;\"></a></div>";
}
function appyads_getCampaignSizes() {
    return array('160X80','160X160','160X600','180X60','180X180','300X250','300X600','728X90','764X400','970X90','970X250');
}
function appyads_chkDupPlacement($size) {
    global $aaUsedCampSizes;
    if (!isset($aaUsedCampSizes) || empty($aaUsedCampSizes)) $aaUsedCampSizes = array();
    $tempId = $size;
    $lpCnt = 0;
    while (in_array($tempId, $aaUsedCampSizes)) {
        $lpCnt++;
        $tempId = $size . "_$lpCnt";
    }
    $aaUsedCampSizes[] = $tempId;
    return $tempId;
}
function appyads_getSpacer($size) {
    $spSrc = plugin_dir_url( dirname( __FILE__ ) ) . 'img/aaspc-'.$size.'.png';
    $maxW = substr($size,0,strpos($size,'X')) . 'px';
    return "<img id=\"aaspcr-$size\" src=\"$spSrc\" style=\"width:100%;max-width:$maxW;height:auto;\">";
}
function appyads_placementMarkup($size) {
    $verSize = appyads_chkDupPlacement($size);
    $spacer = appyads_getSpacer($size);
    return "<div id=\"AppyAd_$verSize\">$spacer</div>";
}
function appyads_shortcodePlacement( $attr ) {

    $campSize = APPYADS_DEFAULT_CAMPAIGN_SIZE;
	if ( ! empty( $attr['size'] ) ) {
        if (in_array(strtoupper($attr['size']), appyads_getCampaignSizes())) {
            $campSize = strtoupper($attr['size']);
        }
	}
    appyads_enqueScript();
    return appyads_placementMarkup($campSize);
}
add_shortcode( 'AppyAds_Placement', 'appyads_shortcodePlacement' );
function appyads_enqueScript() {
    if (!defined('APPYADS_SCRIPT_REGISTERED')) {
        $scriptUrl = APPYADS_BASE_URL;
        if ($ac = get_option(APPYADS_ACCOUNT_WP_OPTION)) $scriptUrl = str_replace('wpwpwpwp',$ac,$scriptUrl);
        wp_register_script('appyads_script', $scriptUrl);
        wp_enqueue_script('appyads_script');
        define('APPYADS_SCRIPT_REGISTERED', true);
    }
}
