<?php 

class Sslcom_Success_Url
{
    protected static $instance = NULL;
    public function __construct()
    { }
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }
    public function setup()
    {
        add_action('init', array($this, 'rewrite_rules'));
        add_filter('query_vars', array($this, 'query_vars'), 10, 1);
        add_action('parse_request', array($this, 'parse_request'), 10, 1);
        register_activation_hook(__FILE__, array($this, 'flush_rules'));
    }
    public function rewrite_rules()
    {
        add_rewrite_rule('sslcomsuccess/?$', 'sslcommerzQuickpay.php?sslcomsuccess', 'top');
    }
    public function flush_rules()
    {
        $this->rewrite_rules();
        flush_rewrite_rules();
    }
    public function query_vars($vars)
    {
        $vars[] = 'sslcomsuccess';
        return $vars;
    }
    public function parse_request($wp)
    {
        if (array_key_exists('sslcomsuccess', $wp->query_vars)) {
            include plugin_dir_path(__FILE__) . 'sslcom-quickpay-success.php';
            exit();
        }
    }
}

class Sslcom_Fail_Url
{
    protected static $instance = NULL;
    public function __construct()
    { }
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }
    public function setup()
    {
        add_action('init', array($this, 'rewrite_rules'));
        add_filter('query_vars', array($this, 'query_vars'), 10, 1);
        add_action('parse_request', array($this, 'parse_request'), 10, 1);
        register_activation_hook(__FILE__, array($this, 'flush_rules'));
    }
    public function rewrite_rules()
    {
        add_rewrite_rule('sslcomfail/?$', 'sslcommerzQuickpay.php?sslcomfail', 'top');
    }
    public function flush_rules()
    {
        $this->rewrite_rules();
        flush_rewrite_rules();
    }
    public function query_vars($vars)
    {
        $vars[] = 'sslcomfail';
        return $vars;
    }
    public function parse_request($wp)
    {
        if (array_key_exists('sslcomfail', $wp->query_vars)) {
            include plugin_dir_path(__FILE__) . 'sslcom-quickpay-fail.php';
            exit();
        }
    }
}

class Sslcom_Cancel_Url
{
    protected static $instance = NULL;
    public function __construct()
    { }
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }
    public function setup()
    {
        add_action('init', array($this, 'rewrite_rules'));
        add_filter('query_vars', array($this, 'query_vars'), 10, 1);
        add_action('parse_request', array($this, 'parse_request'), 10, 1);
        register_activation_hook(__FILE__, array($this, 'flush_rules'));
    }
    public function rewrite_rules()
    {
        add_rewrite_rule('sslcomcancel/?$', 'sslcommerzQuickpay.php?sslcomcancel', 'top');
    }
    public function flush_rules()
    {
        $this->rewrite_rules();
        flush_rewrite_rules();
    }
    public function query_vars($vars)
    {
        $vars[] = 'sslcomcancel';
        return $vars;
    }
    public function parse_request($wp)
    {
        if (array_key_exists('sslcomcancel', $wp->query_vars)) {
            include plugin_dir_path(__FILE__) . 'sslcom-quickpay-cancel.php';
            exit();
        }
    }
}

class Sslcom_Ipn_Url
{
    protected static $instance = NULL;
    public function __construct()
    { }
    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }
    public function setup()
    {
        add_action('init', array($this, 'rewrite_rules'));
        add_filter('query_vars', array($this, 'query_vars'), 10, 1);
        add_action('parse_request', array($this, 'parse_request'), 10, 1);
        register_activation_hook(__FILE__, array($this, 'flush_rules'));
    }
    public function rewrite_rules()
    {
        add_rewrite_rule('sslcomipn/?$', 'sslcommerzQuickpay.php?sslcomipn', 'top');
    }
    public function flush_rules()
    {
        $this->rewrite_rules();
        flush_rewrite_rules();
    }
    public function query_vars($vars)
    {
        $vars[] = 'sslcomipn';
        return $vars;
    }
    public function parse_request($wp)
    {
        if (array_key_exists('sslcomipn', $wp->query_vars)) {
            include plugin_dir_path(__FILE__) . 'sslcom-quickpay-ipn.php';
            exit();
        }
    }
}
