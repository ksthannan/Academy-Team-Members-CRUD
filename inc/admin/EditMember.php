<?php 
namespace academy\teammembers\admin;
/**
 * Class acedemy dashboard
 */
class EditMember{
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
        add_submenu_page('options.php', __('Edit Member Info', 'tmembers'), __('Edit Member Info', 'tmembers'), 'manage_options', 'edit_team_member', [$this, 'admin_sub_menu_page']);
    }

    // admin page callback
    public function admin_sub_menu_page() { 
        if(isset($_GET['member_id'])){
            $member_id = sanitize_text_field($_GET['member_id']);
            global $wpdb;
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM $this->table_name WHERE id = %d",
                    $member_id
                )
            );
            // echo '<pre>';
            // var_dump($results[0]->id);
            // echo '</pre>';
            // dashboard content
            ?>
            <div class="wrap">
                <div class="academy_container">
                    <h1 class="wp-heading-inline"><?php _e('Edit team member', 'tmembers');?></h1>
                    <a href="<?php esc_attr_e(admin_url('admin.php?page=academy_team_members'));?>" class="page-title-action"><?php esc_attr_e('View All Members', 'tmembers');?></a>
                    <p><?php _e('Edit team member\'s information', 'tmembers')?></p>
                    <form method="post" action="<?php echo admin_url( 'admin.php?page=edit_team_member' . '&action=edit&member_id=' . $member_id );?>">
                        <?php wp_nonce_field('edit_team_member_action', 'edit_team_member_nonce'); ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="team_member_name"><?php _e('Name', 'tmembers');?></label></th>
                                <td><input type="text" name="team_member_name" id="team_member_name" class="regular-text" value="<?php esc_attr_e($results[0]->name);?>" required></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="team_member_email"><?php _e('Email', 'tmembers');?></label></th>
                                <td><input type="email" name="team_member_email" id="team_member_email" value="<?php esc_attr_e($results[0]->email);?>" class="regular-text"></td>
                            </tr>
                        </table>

                        <input type="hidden" name="member_id" value="<?php esc_attr_e($member_id);?>">
                        <input type="hidden" name="action" value="edit_team_member">
                        <p class="submit">
                            <input type="submit" name="submit_team_member" class="button button-primary" value="<?php _e('Update Team Member', 'tmembers');?>">
                        </p>
                    </form>
                </div>
            </div>
            <?php 
        }
    }

    public function form_submit(){
        global $wpdb;
        // pluggable
        require_once( ABSPATH . 'wp-includes/pluggable.php' );

        if(isset($_POST['action']) && $_POST['action'] == 'edit_team_member'){

            // Verify nonce
            if ( ! isset( $_POST['edit_team_member_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['edit_team_member_nonce']), 'edit_team_member_action' ) ) {
                // Nonce message
                add_action( 'admin_notices', [$this, 'admin_notice_nonce__error'] );
                return;
            }
            
            $id = sanitize_text_field($_POST['member_id']);
            $name = sanitize_text_field($_POST['team_member_name']);
            $email = sanitize_email($_POST['team_member_email']);

            $result = $wpdb->update($this->table_name, 
            array(
                'name' => $name, 
                'email' => $email
            ), 
            array(
                'id' => $id,
            ));

            // Check if deletion was successful
            if ($result === false) {
                // Error occurred
                add_action( 'admin_notices', [$this, 'admin_notice_wrong_error'] );
            } else {
                // Successful
                add_action( 'admin_notices', [$this, 'admin_notice_nonce__update_success'] );
            }
            
        }
    }

    // Admin error notice 
    public function admin_notice_nonce__error() {
        $class = 'notice notice-error';
        $message = __( 'Error! Nonce verification failed.', 'tmembers' );
        
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }

    // Admin update success notice 
    public function admin_notice_nonce__update_success() {
        $class = 'notice notice-success';
        $message = __( 'Data has been updated successfully.', 'tmembers' );
        
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }

    // Admin add success notice 
    public function admin_notice_nonce__add_success() {
        $class = 'notice notice-success';
        $message = __( 'Data has been added successfully.', 'tmembers' );
        
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
        
    }

    // Admin error notice 
    public function admin_notice_wrong_error() {
        $class = 'notice notice-error';
        $message = __( 'Error! Something went wrong.', 'tmembers' );
        
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }

}


