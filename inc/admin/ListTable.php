<?php
namespace academy\teammembers\admin;
// Load WP_List_Table
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
/**
 * Class academy Table
 */
class ListTable extends \WP_List_Table{

    // define $table_data property
    private $table_data;

    // Define table columns
    public function get_columns()
    {
        $columns = array(
                'cb'            => '<input type="checkbox" />',
                'id'          => __('ID', 'tmembers'),
                'name'          => __('Name', 'tmembers'),
                'email'         => __('Email', 'tmembers')
        );
        return $columns;
    }

    // Get table data
    private function get_table_data( $search = '' ) {
        global $wpdb;

        $table = $wpdb->prefix . 'academy_team_members';

        if ( !empty($search) ) {
            return $wpdb->get_results(
                "SELECT * FROM $table WHERE id Like '%{$search}%' OR name Like '%{$search}%' OR email Like '%{$search}%'",
                ARRAY_A
            );
        } else {
            return $wpdb->get_results(
                "SELECT * FROM $table",
                ARRAY_A
            );
        }
    }

    // Bind table with columns, data and all
    public function prepare_items()
    {
        //data
        if ( isset($_POST['s']) ) {
            $this->table_data = $this->get_table_data($_POST['s']);
        } else {
            $this->table_data = $this->get_table_data();
        }

        $columns = $this->get_columns();

        $hidden = ( is_array(get_user_meta( get_current_user_id(), 'list_tablecolumnshidden', true)) ) ? get_user_meta( get_current_user_id(), 'list_tablecolumnshidden', true) : array();

        $sortable = $this->get_sortable_columns();

        $primary  = 'id';

        $this->_column_headers = array($columns, $hidden, $sortable, $primary);

        usort($this->table_data, array(&$this, 'usort_reorder'));

        /* pagination */
        $per_page = $this->get_items_per_page('elements_per_page', 20);
        $current_page = $this->get_pagenum();
        $total_items = count($this->table_data);

        $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
                'total_items' => $total_items, 
                'per_page'    => $per_page, 
                'total_pages' => ceil( $total_items / $per_page ) 
        ));

        $this->items = $this->table_data;
    }

    // column default 
    public function column_default($item, $column_name)
    {
          switch ($column_name) {
                case 'id':
                case 'name':
                case 'email':
                default:
                return $item[$column_name];
          }
    }

    // column checkbox 
    function column_cb($item)
    {
        return sprintf(
                '<input type="checkbox" name="element[]" value="%s" />',
                $item['id']
        );
    }

    // Ordering 
    protected function get_sortable_columns()
    {
        $sortable_columns = array(
                'id'  => array('id', false),
                'name'  => array('name', false),
                'email' => array('email', false)
        );
        return $sortable_columns;
    }

    // Sorting function
    function usort_reorder($a, $b)
    {
        // If no sort, default to id
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'id';

        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';

        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }

    // Adding action links to column
    function column_name($item)
    {
        $nonce = wp_create_nonce('delete_team_member_nonce');
        $actions = array(
                'edit'      => sprintf('<a href="?page=%s&action=%s&member_id=%s">' . __('Edit', 'tmembers') . '</a>', 'edit_team_member', 'edit', $item['id']),
                'delete'    => sprintf('<a href="?page=%s&action=%s&member_id=%s&nonce=%s" onclick="return confirmDelete();">' . __('Delete', 'tmembers') . '</a>', $_REQUEST['page'], 'delete', $item['id'], $nonce),
        );

        return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions));
    }

    // To show bulk action dropdown
    function get_bulk_actions()
    {
            $actions = array(
                    'delete_all'    => __('Delete', 'tmembers'),
                    // 'draft_all' => __('Move to Draft', 'tmembers')
            );
            return $actions;
    }

}