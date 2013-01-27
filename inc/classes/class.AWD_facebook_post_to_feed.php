<?php
/**
 * 
 * @author alexhermann
 *
 */
Class AWD_facebook_post_to_feed extends AWD_facebook_plugin_abstract
{
	/**
	 * The Slug of the plugin
	 * @var string
	 */
    public $plugin_slug = 'awd_fcbk_post_to_feed';
    
    /**
     * The Name of the plugin
     * @var string
     */
    public $plugin_name = 'Facebook AWD Post To Feed';
    
    /**
     * The text domain of the plugin
     * @var string
     */
    public $ptd = 'AWD_facebook_post_to_feed';
    
    /**
     * The version required for AWD_facebook object
     * @var float
     */
    public $version_requiered = "1.5";
    
    /**
     * The array of deps
     * @var array
     */
    public $deps = array('connect'=>1);
	
	/**
	 * Constructor
	 */
	public function __construct($file,$AWD_facebook)
	{
		parent::__construct(__FILE__,$AWD_facebook);
	}
	
	/**
	 * Initialisation of the Facebook AWD plugin
	 */
	public function initialisation()
	{
		parent::init();
		add_shortcode('AWD_facebook_post_to_feed_button', array($this, 'shortcode_post_to_feed'));
	}
	
	/**
	 * Enqueus JS on admin and front
	 */
	public function global_enqueue_js()
	{
		wp_register_script($this->plugin_slug,$this->plugin_url.'/assets/js/facebook_awd_post_to_feed.js',array($this->AWD_facebook->plugin_slug), $this->get_version(), true);
		wp_enqueue_script($this->plugin_slug);
	}
	
	/**
	 * Define default $options
	 * @param array $options
	 */
	public function default_options($options)
	{
		$options = parent::default_options($options);
		$default_options = array();
		$default_options['label'] = __('Share',$this->ptd);
		$default_options['link'] = home_url();
		$default_options['class'] = 'btn';
		$default_options['callbackjs'] = '';
		
		//attach options to Container
		if(!isset($options['post_to_feed']))
			$options['post_to_feed'] = array();
		$options['post_to_feed'] = wp_parse_args($options['post_to_feed'], $default_options);

		return $options;
	}
	
	/**
	 * Register widget
	 */
	public function register_widgets()
	{
		global $wp_widget_factory;
		$fields = apply_filters('AWD_facebook_plugins_form', array());
		$fields = isset($fields['post_to_feed']) ? $fields['post_to_feed'] : array();
		$wp_widget_factory->widgets['AWD_facebook_widget_post_to_feed'] = new AWD_facebook_widget(array(
			'id_base' => 'post_to_feed',
			'name' => $this->plugin_name,
			'description' => __('Allow your users to share your posts',$this->ptd),
			'model' => $fields,
			'self_callback' => array($this, 'shortcode_post_to_feed'),
			'text_domain' => $this->ptd,
			'preview' => true
		));
	}
	
	/**
	 * Plugins menu filter
	 * @param array $list
	 */
	public function plugin_settings_menu($list)
	{
		$list['post_to_feed_settings'] = __('Post To Feed Button', $this->ptd);
		return $list;
	}
	
	/**
	 * Model of form
	 * @param array $fields
	 */
	public function plugin_settings_form($fields)
	{
		$fields['post_to_feed'] = array(
			
			'title_config' => array(
				'type'=>'html',
				'html'=> '
					<h1>'.__('Configure the button',$this->ptd).'</h1>
				',
				'widget_no_display' => true
			),
			
			'before_config' => array(
				'type'=>'html',
				'html'=> '
					<div class="row">
				'
			),
			
			'widget_title'=> array(
				'type'=> 'text',
				'label'=> __('Title',$this->ptd),
				'class'=>'span4',
				'attr'=> array('class'=>'span4'),
				'widget_only' => true
			),	
				
			'label'=> array(
				'type'=> 'text',
				'label'=> __('Label',$this->ptd),
				'class'=>'span4',
				'attr'=> array('class'=>'span4')
			),
						
			'link'=> array(
				'type'=> 'text',
				'label'=> __('Link to Share',$this->ptd),
				'class'=>'span4',
				'attr'=> array('class'=>'span4')
			),

			'class'=> array(
				'type'=> 'text',
				'label'=> __('Css Class',$this->ptd),
				'class'=>'span4',
				'attr'=> array('class'=>'span4')
			),

			'callbackjs' => array(
				'type'=> 'text',
				'label'=> __('Callback JS',$this->ptd),
				'class'=>'span4',
				'attr'=> array('class'=>'span4')
			),
			
			
			'after_config' => array(
				'type'=>'html',
				'html'=> '
					</div>
				'
			),
			
			'preview' => array(
				'type'=>'html',
				'html'=> '
					<h1>'.__('Preview',$this->ptd).'</h1>
					<div class="well">'.do_shortcode('[AWD_facebook_post_to_feed_button]').'</div>
					<h1>'.__('Options List',$this->ptd).'</h1>
					<table class="table table-bordered table-condensed table-striped">
						<thead>
							<tr>
								<th>Option</th>
								<th>Value</th>
							</tr>
						</thead>
						<tbody>
							<tr><td>label</td><td>string</td></tr>
							<tr><td>link</td><td>string</td></tr>
							<tr><td>class</td><td>number</td></tr>
							<tr><td>callbackjs</td><td>number</td></tr>
						</tbody>
						<tfoot>
							<th colspan="2">[AWD_facebook_post_to_feed_button option="value"]</th>
						</tfoot>
					</table>
				',
				'widget_no_display' => true
			)
		);
		return $fields;
	}
	
	/**
	 * Shortcode function hook
	 * @param array $options
	 */
	public function shortcode_post_to_feed($options=array())
	{
		global $post;
		return $this->get_the_post_to_feed_button($post,$options);
	}
	
	/**
	 * Get the app request button
	 * @param array $options
	 */
	public function get_the_post_to_feed_button($post = null, $options=array())
	{
		if(!isset($options['link']) && is_object($post)){
			$options['link'] = get_permalink($post->ID);
		}
		$options = wp_parse_args($options, $this->AWD_facebook->options['post_to_feed']);
		return '<div class="AWD_facebook_wrap"><a href="'.$options['link'].'" class="AWD_facebook_post_to_feed_button '.$options['class'].'" data-link="'.$options['link'].'" data-callbackjs="'.$options['callbackjs'].'">'.$options['label'].'</a></div>';
	}
}