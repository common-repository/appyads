<?php
/*
* AppyAds administration routines
*/
// Add a WP menu item for AppyAds plugin
function appyads_updateWPMenu() {
    require_once('appyads_icon.php');
    add_menu_page( 'AppyAds', 'AppyAds', 'manage_options', 'appyads', 'appyads_mgmtConsole', appyads_getMenuIcon(), '54.85321' );
}
add_action('admin_menu', 'appyads_updateWPMenu');
// Management console
function appyads_mgmtConsole() {
    if (!is_user_logged_in() || !current_user_can('manage_options')) return;
    $aa_account = get_option(APPYADS_ACCOUNT_WP_OPTION);
    if (empty($aa_account) || $aa_account == APPYADS_DEFAULT_ACCOUNT) $aa_account = '';

    echo "<div style=\"width:90%;margin:auto;text-align:center;\">";
    echo appyads_getAppyAdsLogo();
    echo "<br><br>";
    if (empty($aa_account)) {
    ?>
    <p>To get the most benefit out of hosting AppyAds, make sure you have an AppyAds account. Register for free at <a href="//appyads.com/appyads-membership/" target="_blank">AppyAds.com</a></p>
    <?php
    }
    else {
    ?>
    <p>Manage your AppyAds account at <a href="//appyads.com/member-home/" target="_blank">AppyAds.com</a></p>
    <?php
    }
    ?>
    <script type="text/javascript">
        var appyads_object = {"ajax_url":"\/wp-admin\/admin-ajax.php"};
        function appyadsGetJson(str) {
            try {
                result = JSON.parse(str);
            } catch (e) {
                return false;
            }
            return result;
        }
        function appyadsMgmtPost(aType, data) {
            var msg = '';
            jQuery.post(appyads_object.ajax_url, data, function(response) {
                if (response != 0) {
                    var jonRsp = appyadsGetJson(response);
                    if (jonRsp !== false) {
                        if (jonRsp.status !== undefined && jonRsp.status && jonRsp.message !== undefined) {
                            appyadsShowTextResponse(jonRsp.message);
                        }
                        else if (jonRsp.message !== undefined) {
                            console.log(jonRsp.message);
                            appyadsShowErrorResponse(jonRsp.message);
                        }
                        else {
                            msg = 'Received an invalid response from the server.';
                            console.log(msg);
                            console.log(response);
                            appyadsShowErrorResponse(msg);
                        }
                    }
                    else {
                        msg = 'Received an unsupported response format from the server.';
                        console.log(msg);
                        console.log(response);
                        appyadsShowErrorResponse(msg);
                    }
                }
                else {
                    msg = 'An unexpected system error has occurred.';
                    console.log(msg);
                    appyadsShowErrorResponse(msg);
                }
            }).fail(function() {
                msg = 'An unexpected system error has occurred.';
                console.log(msg);
                appyadsShowErrorResponse(msg);
            });
        }
        function appyadsSubmitAccountUpdate() {
            var reqType = 3;
            var tAcct = document.getElementById('aa_account_id');
            if (typeof tAcct !== "undefined" && tAcct !== null) {
                appyadsResetMessageArea();
                var data = {
                    'action': 'appyads_settingsUpdate',
                    'rtype': reqType,
                    'account_id': tAcct.value
                };
                appyadsMgmtPost(reqType, data);
            }
            return false;
        }
        function appyadsResetMessageArea() {
            jQuery("#aa-general-info-messages").html( "&nbsp;" ).fadeIn("fast");
            jQuery("#aa-general-error-messages").html( "&nbsp;" ).fadeIn("fast");
        }
        function appyadsShowErrorResponse(msg) {
            jQuery("#aa-general-error-messages").html( msg ).fadeIn("fast");
        }
        function appyadsShowTextResponse(msg) {
            jQuery("#aa-general-info-messages").html( msg ).fadeIn("fast");
        }
    </script>
    <div id="aa_mgmt_form_area" style="width:80%;margin:auto;text-align:center;">
      <form>
        <fieldset>
          <label for="aa_account_id">Enter your AppyAds account ID:</label>
          <input type="text" name="aa_account_id" id="aa_account_id" size="32" maxlength="32" placeholder="AppyAds Account ID" value="<?php echo $aa_account; ?>">
          <br><br>
          <input type="submit" value="Save" onclick="return appyadsSubmitAccountUpdate();">
            
          <!-- Allow form submission with keyboard without duplicating the dialog button -->
          <input type="submit" tabindex="-1" style="position:absolute; top:-1000px" onclick="return appyadsSubmitAccountUpdate();">
        </fieldset>
      </form>
      <div id="aa-general-info-messages" style="color:green;"></div>
      <div id="aa-general-error-messages" style="color:red;"></div>
    </div>
    <?php
    echo "</div>\n";
}
// Admin AJAX handlers
function appyads_settingsUpdate() {
    if (is_user_logged_in() && current_user_can('manage_options')) {
        $requestType = intval( $_POST['rtype'] );
        switch ($requestType) {
            case 3:
                $reqAcctId = appyads_filterUserAcctId($_POST['account_id']);
                if ($reqAcctId !== false) {
                    appyads_updateAppyAdsAccount($reqAcctId);
                }
                else {
                    appyads_ajaxReturnAndEnd(array('status' => false, 'message' => 'Invalid AppyAds ID.'));
                }
                break;
        }
    }
    else echo "Not allowed.";
	wp_die();
}
function appyads_filterUserAcctId($input) {
    $len = strlen($input);
    if ($len == 0) return '';
    if ($len != 12) return false;
    $validCharacters = '01234567891abcdefghijklmnopqrstuvwxyz';
    for ($i=0; $i<$len; $i++) {
        if (strpos($validCharacters,substr($input,$i,1)) === false) return false;
    }
    return $input;
}
function appyads_ajaxReturnAndEnd($retObj) {
    $retJson = json_encode($retObj);
    if ($retJson) echo $retJson;
    else echo "Processing error";
    exit();
}
add_action( 'wp_ajax_appyads_settingsUpdate', 'appyads_settingsUpdate' );
function appyads_updateAppyAdsAccount($accountId) {
    if (!empty($accountId)) update_option(APPYADS_ACCOUNT_WP_OPTION, $accountId);
    else update_option(APPYADS_ACCOUNT_WP_OPTION, APPYADS_DEFAULT_ACCOUNT);
    appyads_ajaxReturnAndEnd(array('status' => true, 'message' => 'AppyAds account ID updated.'));
}
