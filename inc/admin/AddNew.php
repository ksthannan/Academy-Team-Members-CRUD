<?php 
namespace academy\teammembers\admin;
/**
 * Class acedemy dashboard
 */
class AddNew{
    // define table info 
    private $table_name;

    public function __construct() {
        // global wpdb variable 
        global $wpdb;
        // table name 
        $this->table_name = $wpdb->prefix . 'academy_team_members';
        //create an admin page
        // add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_menu', [$this, 'add_admin_sub_menu']);

        $this->form_submit();
    }

    // Add mmenu page
    public function add_admin_sub_menu() {
        // add_menu_page(__('Add New Team Members', 'tmembers'), __('Add New Team Members', 'tmembers'), 'manage_options', 'academy_team_members', [$this, 'admin_page'], 'dashicons-admin-users');
        add_submenu_page('academy_team_members', __('Add New', 'tmembers'), __('Add New', 'tmembers'), 'manage_options', 'add_new_team_member', [$this, 'admin_sub_menu_page']);
    }

    // admin page callback
    public function admin_sub_menu_page() { 
        // dashboard content
        ?>
        <div class="wrap">
            <div class="academy_container">
                <h1 class="wp-heading-inline"><?php _e('Add new team member', 'tmembers');?></h1>
                <p><?php _e('Add new team member\'s information', 'tmembers')?></p>
                <form method="post" action="<?php echo admin_url( 'admin.php?page=add_new_team_member' );?>">
                    <?php wp_nonce_field('add_new_team_member_action', 'add_new_team_member_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="team_member_name"><?php _e('Name', 'tmembers');?></label></th>
                            <td><input type="text" name="team_member_name" id="team_member_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="team_member_email"><?php _e('Email', 'tmembers');?></label></th>
                            <td><input type="email" name="team_member_email" id="team_member_email" class="regular-text"></td>
                        </tr>
                    </table>

                    <input type="hidden" name="action" value="submit_team_member">
                    <p class="submit">
                        <input type="submit" name="submit_team_member" class="button button-primary" value="<?php _e('Add Team Member', 'tmembers');?>">
                    </p>
                </form>
            </div>
        </div>
        <?php 
    }

    public function form_submit(){
        global $wpdb;
        // pluggable
        require_once( ABSPATH . 'wp-includes/pluggable.php' );

        if(isset($_POST['action']) && $_POST['action'] == 'submit_team_member'){

            // Verify nonce
            if ( ! isset( $_POST['add_new_team_member_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['add_new_team_member_nonce']), 'add_new_team_member_action' ) ) {
                // Nonce message
                add_action( 'admin_notices', [$this, 'admin_notice_nonce__error'] );
                return;
            }

            $name = sanitize_text_field($_POST['team_member_name']);
            $email = sanitize_email($_POST['team_member_email']);
            $result = $wpdb->insert($this->table_name, ['name' => $name, 'email' => $email]);

            // Check if deletion was successful
            if ($result === false) {
                // Error occurred
                add_action( 'admin_notices', [$this, 'admin_notice_wrong_error'] );
            } else {
                // Successful
                add_action( 'admin_notices', [$this, 'admin_notice_nonce__update_success'] );

                // Redirect back to the form page
                wp_redirect( admin_url( 'admin.php?page=academy_team_members&success=true' ) );
                exit();
                
            }

            
            
        }
    }

    // Admin error notice 
    public function admin_notice_nonce__error() {
        $class = 'notice notice-error';
        $message = __( 'Error! Nonce verification failed.', 'tmembers' );
        
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }

    // Admin error notice 
    public function admin_notice_wrong_error() {
        $class = 'notice notice-error';
        $message = __( 'Error! Something went wrong.', 'tmembers' );
        
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }

    // Admin update success notice 
    public function admin_notice_nonce__update_success() {
        $class = 'notice notice-success';
        $message = __( 'Data has been updated successfully.', 'tmembers' );
        
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }

}


