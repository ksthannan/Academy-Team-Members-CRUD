<?php 
namespace academy\teammembers\admin;
/**
 * Class acedemy dashboard
 */
class DeleteMember{
    // define table info 
    private $table_name;

    public function __construct() {
        // global wpdb variable 
        global $wpdb;
        // table name 
        $this->table_name = $wpdb->prefix . 'academy_team_members';

        $this->form_submit();
    }
    public function form_submit(){
        global $wpdb;
        // pluggable
        require_once( ABSPATH . 'wp-includes/pluggable.php' );

        if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['member_id'])){

            $member_id = sanitize_text_field( $_GET['member_id'] );

            // Verify nonce
            if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_GET['nonce']), 'delete_team_member_nonce' ) ) {
                // Nonce message
                add_action( 'admin_notices', [$this, 'admin_notice_nonce__error'] );
                return;
            }
            
            $result = $wpdb->delete($this->table_name, 
            array(
                'id' => $member_id,
            ));

            // Check if deletion was successful
            if ($result === false) {
                // Error occurred
                add_action( 'admin_notices', [$this, 'admin_notice_wrong_error'] );
            } else {
                // Deletion was successful
                add_action( 'admin_notices', [$this, 'admin_notice_nonce__update_success'] );
            }
            
        }
    }

    // Admin update success notice 
    public function admin_notice_nonce__update_success() {
        $class = 'notice notice-success';
        $message = __( 'Data has been deleted successfully.', 'tmembers' );
        
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
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

}


