<?php 
namespace academy\teammembers;
/**
 * Class Functions 
 */

 class Functions{
    // define properties 
    private $dbv = '1.3';
    public function __construct()
    {
        // init functions 
        add_action('init', [$this, 'init']);

    }

    public function init(){
        // global 
        global $wpdb;
        // table name
        $this->table_name = $wpdb->prefix . 'academy_team_members';

        // register activation and deactivation hook 
        register_activation_hook(__FILE__, [$this, 'create_database_tables']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        // run function on actiavation 
        $this->activate();

        // instantiate dashboard 
        new admin\Dashboard();

        // instantiate AddNew 
        new admin\AddNew();

        // instantiate EditMember 
        new admin\EditMember();

        // instantiate DeleteMember 
        new admin\DeleteMember();
    }

    // Run function on deactivattion
    function deactivate() {
        global $wpdb;
        // $wpdb->query("DROP TABLE IF EXISTS $this->table_name");
    }

    /**
     * activation update options
     */
    public function activate()
    {

        // update version
        $this -> add_version();

        // update database version 
        $dbv = get_option('dbv');
        if ($dbv != $this->dbv) {
            $this->create_database_tables();
            update_option('dbv', $this->dbv);
        }

    }

    // add version 
    public function add_version()
    {
        $installed = get_option('academy_team_members_installed');

        if (!$installed) {
            update_option('academy_team_members_installed', time());
        }

        update_option('academy_version', ACADEMY_VER);

    }

    // create table when activating plugin 
    public function create_database_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(50) NOT NULL,
            email varchar(50) NOT NULL,
            -- phone varchar(15) NOT NULL,
            -- gender varchar(10) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }

    
 }
