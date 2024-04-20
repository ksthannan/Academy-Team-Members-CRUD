<?php 
namespace academy\teammembers\admin;
/**
 * Class acedemy dashboard
 */
class Dashboard{
    // define table info 
    private $table_name;

    public function __construct() {
        // global wpdb variable 
        global $wpdb;
        // table name 
        $this->table_name = $wpdb->prefix . 'academy_team_members';
        //create an admin page
        add_action('admin_menu', [$this, 'add_admin_menu']);

        // Success notice on insert data
        if(isset($_GET['success']) && $_GET['success'] == true ){
            add_action( 'admin_notices', [$this, 'admin_notice_nonce__update_success'] );
        }

        // Bulk delete 
        $this->bulk_delete_all();

        // Screen items per page
        $this->screen_items_per_page();


    }

    // Add mmenu page
    public function add_admin_menu() {
        global $academy_menu;
        $academy_menu = add_menu_page(__('Academy Team Members', 'tmembers'), __('Academy Team Members', 'tmembers'), 'manage_options', 'academy_team_members', [$this, 'admin_page'], 'dashicons-admin-users');

        // Hook into load-{page} action to add screen options
        add_action('load-' . $academy_menu, [$this, 'academy_menu_screen_options']);
    }

    // add screen options
    public function academy_menu_screen_options() {
    
        global $academy_menu; 
        $screen = get_current_screen();
    
        // get out of here if we are not on our settings page
        if(!is_object($screen) || $screen->id != $academy_menu)
            return;
    
        $args = array(
            'label' => __('Items per page', 'tmembers'),
            'default' => 20,
            'option' => 'elements_per_page'
        );
        add_screen_option( 'per_page', $args );

        new ListTable();
    }

    // admin page callback
    public function admin_page() {

        // dashboard content 
        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">'.__('Academy Team Members', 'tmembers').'</h1>';
        echo '<a href="'.admin_url('admin.php?page=add_new_team_member').'" class="page-title-action">'.__('Add New Member', 'tmembers').'</a>';
        // echo '<p>'.__('Showing all the team members information here.', 'tmembers').'</p>';
        echo '<form method="post">';

        $table = new ListTable();
        // Prepare table
        $table->prepare_items();
        // Search form
        $table->search_box('search', 'search_id');
        // Display table
        $table->display();

        echo '</form></div>';
    }

    // Admin update success notice 
    public function admin_notice_nonce__update_success() {
        $class = 'notice notice-success';
        $message = __( 'Data has been updated successfully.', 'tmembers' );
        
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }
    // Admin update delete notice 
    public function admin_notice_nonce__delete_success() {
        $class = 'notice notice-success';
        $message = __( 'Data has been deleted successfully.', 'tmembers' );
        
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }

    // Bulk delete all 
    public function bulk_delete_all(){
        global $wpdb;
        if( isset($_POST['action']) && $_POST['action'] == 'delete_all' ){
            $items = isset($_POST['element']) ? $_POST['element'] : array();
            foreach($items as $item_id){
                $item_id = sanitize_text_field( $item_id );
                $result = $wpdb->delete($this->table_name, 
                array(
                    'id' => $item_id,
                ));

                // Check if deletion was successful
                if ($result === false) {
                    // Error occurred
                    add_action( 'admin_notices', [$this, 'admin_notice_wrong_error'] );
                } else {
                    // Deletion was successful
                    add_action( 'admin_notices', [$this, 'admin_notice_nonce__delete_success'] );
                }
            }
        }
    }

    // Update screen option's item per page
    public function screen_items_per_page(){
        if( isset($_POST['screen-options-apply']) && isset($_POST['wp_screen_options']) ){
            $per_page = $_POST['wp_screen_options']['value'];
            $user_id = get_current_user_id();
            update_user_option($user_id, 'elements_per_page', sanitize_text_field( $per_page ), false);
        }
    }

    // Admin error notice 
    public function admin_notice_wrong_error() {
        $class = 'notice notice-error';
        $message = __( 'Error! Something went wrong.', 'tmembers' );
        
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
    }

}


