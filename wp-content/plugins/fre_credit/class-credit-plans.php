<?php
class FRE_Credit_Plan_Posttype extends AE_Posts
{
    public static $instance;

    /**
     * getInstance method
     *
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * The constructor
     *
     * @param string $post_type
     * @param array $taxs
     * @param array $meta_data
     * @param array $localize
     * @return void void
     *
     * @since 1.0
     * @author Jack Bui
     */
    public function __construct($post_type = '', $taxs = array(), $meta_data = array(), $localize = array())
    {
        parent::__construct('fre_credit_plan', $taxs, $meta_data, $localize);
    }
    /**
      * init for this class
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function init()
    {
        $this->fre_credit_register_post_type();
    }
    /**
      * register post type
      *
      * @param void
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function  fre_credit_register_post_type(){

        register_post_type('fre_credit_plan', array(
            'labels' => array(
                'name' => __('Credit plan', ET_DOMAIN) ,
                'singular_name' => __('Credit plan', ET_DOMAIN) ,
                'add_new' => __('Add New', ET_DOMAIN) ,
                'add_new_item' => __('Add New Credit plan', ET_DOMAIN) ,
                'edit_item' => __('Edit Credit plan', ET_DOMAIN) ,
                'new_item' => __('New Credit plan', ET_DOMAIN) ,
                'all_items' => __('All Credit plans', ET_DOMAIN) ,
                'view_item' => __('View Credit plan', ET_DOMAIN) ,
                'search_items' => __('Search Credit plans', ET_DOMAIN) ,
                'not_found' => __('No Credit plan found', ET_DOMAIN) ,
                'not_found_in_trash' => __('No Credit plans found in Trash', ET_DOMAIN) ,
                'parent_item_colon' => '',
                'menu_name' => __('Credit plans', ET_DOMAIN)
            ) ,
            'public' => false,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => true,

            'capability_type' => 'post',
            // 'capabilities' => array(
            //     'manage_options'
            // ) ,
            'has_archive' => 'packs',
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array(
                'title',
                'editor',
                'author',
                'custom-fields'
            )
        ));
        $package = new AE_Pack('fre_credit_plan',array(
            'sku',
            'et_price',
            'et_number_posts',
            'order',
            'et_featured'
        ),
            array(
                'backend_text' => array(
                    'text' => __('%s  for %s credits', ET_DOMAIN) ,
                    'data' => array(
                        'et_price',
                        'et_number_posts'
                    )
                )
            ));
        global $ae_post_factory;
        $ae_post_factory->set('fre_credit_plan', $package);
    }
}