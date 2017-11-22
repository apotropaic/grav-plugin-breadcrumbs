<?php
namespace Grav\Plugin;

use Grav\Common\Grav;

class Breadcrumbs
{

    /**
     * @var array
     */
    protected $breadcrumbs;
    protected $config;

    /**
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Get all items in breadcrumbs.
     *
     * @return array
     */
    public function get($useParent=false)
    {
        if (!$this->breadcrumbs) {
            $this->build($useParent);
        }
        return $this->breadcrumbs;
    }

    /**
     * Build breadcrumbs.
     *
     * @internal
     */
    protected function build($useParent)
    {
        $hierarchy = array();
        $grav = Grav::instance();
        $current = $grav['page'];
        
        if($useParent){
            $current = $current->parent();   
        }

        // Page cannot be routed.
        if (!$current) {
            $this->breadcrumbs = array();
            return;
        }

        if (!$current->root()) {

            if ($this->config['include_current']) {
                $hierarchy[$current->url()] = $current;
            }

            $current = $current->parent();

            while ($current && !$current->root()) {
                $hierarchy[$current->url()] = $current;
                $current = $current->parent();
            }
        }

        if ($this->config['include_home']) {
            $home = $grav['pages']->dispatch('/');
            if ($home && !array_key_exists($home->url(), $hierarchy)) {
                $hierarchy[] = $home;
            }
        }

        $this->breadcrumbs = array_reverse($hierarchy);
    }
}
